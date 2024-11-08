<?php
// models/SubjectModel.php

require_once 'app/core/Model.php';

class SubjectModel extends Model
{
    // Lấy tất cả các môn học
    public function searchSubject($keyword = '', $page = 1, $pageSize = 10)
    {
        // Tính toán offset cho phân trang
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Lấy dữ liệu phân trang và tìm kiếm
        $sql = "SELECT * FROM subjects WHERE name LIKE :keyword ORDER BY name LIMIT :offset, :pageSize";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy tổng số bản ghi
        $sqlTotal = "SELECT COUNT(*) as total FROM subjects WHERE name LIKE :keyword";
        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        // Tính tổng số trang
        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'subjects' => $subjects,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }


    public function allSubjects()
    {
        $sql = "SELECT * FROM subjects ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectsWithLessions()
    {
        // Truy vấn để lấy thông tin các subject có ít nhất một lession và chỉ lấy các lession có câu hỏi
        $sql = "
            SELECT s.id AS subject_id, s.name AS subject_name, s.meta AS subject_meta, 
                   l.id AS lession_id, l.name AS lession_name, l.meta AS lession_meta
            FROM subjects s
            INNER JOIN lessions l ON l.subject_id = s.id
            INNER JOIN questions q ON q.lession_id = l.id
            GROUP BY s.id, l.id
            ORDER BY s.id, l.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Fetch tất cả các kết quả
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return null; // Nếu không có subject nào có lession
        }

        $subjects = [];
        foreach ($results as $result) {
            $subject_id = $result['subject_id'];
            if (!isset($subjects[$subject_id])) {
                // Thêm thông tin subject mới nếu chưa tồn tại trong danh sách
                $subjects[$subject_id] = [
                    'id' => $result['subject_id'],
                    'name' => $result['subject_name'],
                    'meta' => $result['subject_meta'],
                    'lessions' => []
                ];
            }

            // Thêm lession vào danh sách các lession của subject
            $subjects[$subject_id]['lessions'][] = [
                'id' => $result['lession_id'],
                'name' => $result['lession_name'],
                'meta' => $result['lession_meta'],
            ];
        }

        // Chuyển mảng subjects từ dạng associative array sang dạng indexed array
        $subjects = array_values($subjects);

        return $subjects;
    }

    public function getSubjectsWithExams()
    {
        // Lấy ngày hiện tại
        $currentDate = date('Y-m-d H:i:s');

        // Truy vấn để lấy thông tin các subjects có ít nhất một bài thi (exam) với điều kiện begin_date và end_date chứa ngày hiện tại
        $sql = "
            SELECT s.id AS subject_id, s.name AS subject_name, s.meta AS subject_meta, 
                   e.id AS exam_id, e.title AS exam_title, e.description AS exam_description
            FROM subjects s
            INNER JOIN exams e ON e.subject_id = s.id
            WHERE e.begin_date <= :currentDate AND e.end_date >= :currentDate
            GROUP BY s.id, e.id
            ORDER BY s.id, e.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':currentDate', $currentDate);
        $stmt->execute();

        // Fetch tất cả các kết quả
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return null; // Nếu không có subject nào có exams thỏa điều kiện
        }

        $subjects = [];
        foreach ($results as $result) {
            $subject_id = $result['subject_id'];
            if (!isset($subjects[$subject_id])) {
                // Thêm thông tin subject mới nếu chưa tồn tại trong danh sách
                $subjects[$subject_id] = [
                    'id' => $result['subject_id'],
                    'name' => $result['subject_name'],
                    'meta' => $result['subject_meta'],
                    'exams' => []
                ];
            }

            // Thêm exam vào danh sách các exam của subject
            $subjects[$subject_id]['exams'][] = [
                'id' => $result['exam_id'],
                'title' => $result['exam_title'],
                'description' => $result['exam_description'],
            ];
        }

        // Chuyển mảng subjects từ dạng associative array sang dạng indexed array
        $subjects = array_values($subjects);

        return $subjects;
    }



    // Lấy môn học theo ID
    public function getSubjectById($id)
    {
        $sql = "SELECT * FROM subjects WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    // Tạo môn học mới
    public function createSubject($name)
    {
        // Kiểm tra xem môn học đã tồn tại trong cơ sở dữ liệu hay chưa
        $existingSubject = $this->getSubjectByName($name);
        if ($existingSubject) {
            // Trả về thông báo lỗi nếu môn học đã tồn tại
            return [
                'code' => 409,
                'msg' => 'Môn học này đã tồn tại trong hệ thống!'
            ];
        }

        // Tính toán giá trị meta từ name sử dụng hàm MySQL convertToSlug
        $sql = "INSERT INTO subjects (name, meta) VALUES (:name, convertToSlug(:name))";

        // Thực thi câu lệnh SQL với dữ liệu
        $this->execute($sql, ['name' => $name]);

        // Trả về phản hồi thành công
        return [
            'code' => 201,
            'msg' => 'Thêm mới môn học thành công!!'
        ];
    }

    public function updateSubject($id, $name)
    {
        // Cập nhật thông tin môn học
        $sql = "UPDATE subjects SET name = :name WHERE id = :id";
        $result = $this->execute($sql, ['id' => $id, 'name' => $name]);

        if ($result > 0) {
            // Lấy giá trị meta mới bằng cách gọi hàm convertToSlug trong MySQL
            $sqlMeta = "UPDATE subjects SET meta = convertToSlug(name) WHERE id = :id";
            $this->execute($sqlMeta, ['id' => $id]);

            return [
                'code' => 200,
                'msg' => 'Cập nhật thông tin môn học thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Cập nhật thông tin môn học thất bại!'
            ];
        }
    }


    // Xóa môn học
    public function deleteSubject($id)
    {
        // Chuẩn bị câu lệnh SQL để xóa môn học
        $sql = "DELETE FROM subjects WHERE id = :id";

        // Thực thi câu lệnh SQL
        $result = $this->execute($sql, ['id' => $id]);

        // Kiểm tra số hàng bị ảnh hưởng để xác định xem xóa có thành công hay không
        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Xóa môn học thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Xóa môn học không thành công!'
            ];
        }
    }

    // Lấy môn học theo tên
    public function getSubjectByName($name)
    {
        $sql = "SELECT * FROM subjects WHERE name = :name";
        return $this->fetch($sql, ['name' => $name]);
    }

    public function getExamCountBySubject($keyword = '', $from_date, $to_date)
    {
        // Câu lệnh SQL để thống kê số lần làm bài thi theo môn học với điều kiện lọc
        $sql = "
                SELECT 
                    subjects.name AS subject_name,
                    COUNT(exam_results.id) AS total_exams
                FROM exam_results
                JOIN lessions ON exam_results.lession_id = lessions.id
                JOIN subjects ON lessions.subject_id = subjects.id
                WHERE exam_results.created_at BETWEEN :from_date AND :to_date
                AND subjects.name LIKE :keyword
                GROUP BY subjects.id
                ORDER BY total_exams DESC
                ";

        // Tham số để truyền vào câu truy vấn
        $params = [
            ':from_date' => $from_date,
            ':to_date' => $to_date,
            ':keyword' => '%' . $keyword . '%',
        ];

        // Thực thi truy vấn và trả về kết quả
        return $this->fetchAll($sql, $params);
    }

    public function getSubjectsWithQuizzes()
    {
        // Truy vấn để lấy thông tin các môn học có câu hỏi trong bảng quizs
        $sql = "
            SELECT DISTINCT s.id AS subject_id, s.name AS subject_name, s.meta AS subject_meta
            FROM subjects s
            INNER JOIN quizs q ON q.subject_id = s.id
            ORDER BY s.name
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Fetch tất cả các kết quả
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$subjects) {
            return null; // Nếu không có subject nào có câu hỏi
        }

        return $subjects;
    }

}
