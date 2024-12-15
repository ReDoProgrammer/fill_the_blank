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


    public function listByTeaching($teachingId)
    {
        // Truy vấn để lấy subject_ids từ bảng teachings dựa trên teachingId
        $sql = "SELECT subject_ids FROM teachings WHERE id = :teachingId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':teachingId', $teachingId, PDO::PARAM_INT);
        $stmt->execute();

        // Lấy subject_ids (có thể là một chuỗi, ví dụ '1,2,3')
        $teaching = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teaching) {
            // Lấy subject_ids từ kết quả
            $subjectIds = $teaching['subject_ids'];

            // Chuyển chuỗi subject_ids thành mảng (nếu có nhiều môn học, chuỗi sẽ phân cách bởi dấu phẩy)
            $subjectIdsArray = explode(',', $subjectIds);

            // Truy vấn bảng subjects để lấy thông tin các môn học
            $placeholders = implode(',', array_fill(0, count($subjectIdsArray), '?')); // Tạo chuỗi "?, ?, ?" cho IN
            $sqlSubjects = "SELECT * FROM subjects WHERE id IN ($placeholders)";
            $stmtSubjects = $this->pdo->prepare($sqlSubjects);

            // Liên kết các ID môn học vào câu truy vấn
            foreach ($subjectIdsArray as $key => $subjectId) {
                $stmtSubjects->bindValue(($key + 1), $subjectId, PDO::PARAM_INT);
            }

            // Thực thi truy vấn để lấy các môn học
            $stmtSubjects->execute();

            // Trả về danh sách các môn học
            return $stmtSubjects->fetchAll(PDO::FETCH_ASSOC);
        }

        // Nếu không tìm thấy teachingId hoặc không có subject_ids, trả về mảng rỗng
        return [];
    }


    public function allSubjects()
    {
        $sql = "SELECT * FROM subjects ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // models/SubjectModel.php

    public function getSubjectsWithLessionsByTeacherId($teacherId)
    {
        // Truy vấn để lấy thông tin các môn học có ít nhất một bài học của giáo viên
        $sql = "
        SELECT s.id AS subject_id, s.name AS subject_name, s.meta AS subject_meta, 
               l.id AS lession_id, l.name AS lession_name, l.meta AS lession_meta
        FROM teachings t
        INNER JOIN subjects s ON s.id = t.subject_id
        INNER JOIN lessions l ON l.subject_id = s.id
        WHERE t.teacher_id = :teacherId
        GROUP BY s.id, l.id
        ORDER BY s.id, l.id
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch tất cả các kết quả
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return null; // Nếu không có môn học và bài học nào
        }

        $subjects = [];
        foreach ($results as $result) {
            $subject_id = $result['subject_id'];
            if (!isset($subjects[$subject_id])) {
                // Thêm thông tin môn học mới nếu chưa tồn tại trong danh sách
                $subjects[$subject_id] = [
                    'id' => $result['subject_id'],
                    'name' => $result['subject_name'],
                    'meta' => $result['subject_meta'],
                    'lessions' => []
                ];
            }

            // Thêm bài học vào danh sách các bài học của môn học
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


    // trả về mảng các môn học và bài học tương ứng của học viên
    public function getOwnSubjects($userId)
    {
        // Truy vấn để lấy teaching_id của người dùng
        $sql = "
            SELECT teaching_id 
            FROM users 
            WHERE id = :userId
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $teachingId = $stmt->fetchColumn();

        if (!$teachingId) {
            return null; // Nếu không tìm thấy teaching_id
        }

        // Truy vấn để lấy subject_ids từ bảng teachings
        $sql = "
            SELECT subject_ids
            FROM teachings
            WHERE id = :teachingId
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':teachingId', $teachingId, PDO::PARAM_INT);
        $stmt->execute();

        $subjectIdsArray = $stmt->fetchColumn();

        if (!$subjectIdsArray) {
            return null; // Nếu không có subject_ids
        }

        // Chuyển đổi chuỗi các số nguyên thành mảng
        $subjectIds = explode(',', $subjectIdsArray);

        if (empty($subjectIds)) {
            return null; // Nếu không có môn học nào
        }

        // Truy vấn để lấy thông tin các môn học từ bảng subjects
        $placeholders = rtrim(str_repeat('?,', count($subjectIds)), ',');
        $sql = "
            SELECT id, name, meta
            FROM subjects
            WHERE id IN ($placeholders)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($subjectIds);

        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tạo mảng kết quả dưới dạng JSON với môn học và bài học tương ứng
        $result = [];

        foreach ($subjects as $subject) {
            // Truy vấn để lấy bài học của môn học hiện tại
            $sql = "
                SELECT id AS lession_id, name AS lession_name, meta AS lession_meta
                FROM lessions
                WHERE subject_id = :subjectId
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':subjectId', $subject['id'], PDO::PARAM_INT);
            $stmt->execute();

            $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Thêm môn học và các bài học vào mảng kết quả
            $result[] = [
                'subject' => [
                    'name' => $subject['name'],
                    'subject_id' => $subject['id'],
                    'meta' => $subject['meta'],
                ],
                'lessions' => $lessons
            ];
        }

        // Trả về kết quả dưới dạng mảng JSON
        return $result;
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

    public function getSubjectStatistic($subjectId, $keyword = '')
    {
        // Câu lệnh SQL để lấy thống kê số lần làm bài và tỉ lệ phần trăm trung bình các câu trả lời đúng
        $sql = "
    SELECT 
        user_id,
        username,
        user_code,
        fullname,
        COUNT(DISTINCT result_id) AS total_attempts,  -- Tổng số lượt làm bài của người dùng cho môn học
        ROUND(AVG((correct_questions / total_questions) * 100), 2) AS avg_correct_percentage,  -- Tỉ lệ % trung bình trả lời đúng
        most_attempted_lession_name,  -- Tên của bài học được làm nhiều nhất
        MAX(lession_attempts) AS most_attempted_lession_attempts,  -- Số lần làm bài của bài học được làm nhiều nhất
        ROUND(MAX(correct_percentage), 2) AS most_attempted_lession_correct_percentage  -- Tỉ lệ % làm bài đúng của bài học này
    FROM (
        SELECT 
            exam_results.id AS result_id,
            exam_results.user_id,
            users.username,
            users.fullname,
            users.user_code,
            lessions.name AS most_attempted_lession_name, -- Tên bài học
            -- Đếm số lần làm mỗi bài học của từng người dùng
            (SELECT COUNT(*) 
             FROM exam_results er
             WHERE er.user_id = exam_results.user_id
             AND er.lession_id = exam_results.lession_id) AS lession_attempts,
            -- Tổng số câu hỏi trong bài học
            (SELECT COUNT(*) 
             FROM questions 
             WHERE questions.lession_id = exam_results.lession_id) AS total_questions,
            -- Tính số câu trả lời đúng cho từng bài làm trong bài học
            (SELECT COUNT(*) 
             FROM questions q
             WHERE q.lession_id = exam_results.lession_id
             AND NOT EXISTS (
                 SELECT * 
                 FROM question_blanks qb
                 LEFT JOIN exam_answers ea ON qb.id = ea.question_blank_id AND ea.exam_result_id = exam_results.id
                 WHERE qb.question_id = q.id
                 AND (ea.answer IS NULL OR ea.answer != qb.blank_text)
             )) AS correct_questions,
            -- Tính tỉ lệ % làm bài đúng của bài học này
            (SELECT (correct_questions / total_questions) * 100) AS correct_percentage
        FROM exam_results
        JOIN users ON exam_results.user_id = users.id
        JOIN lessions ON exam_results.lession_id = lessions.id
        WHERE lessions.subject_id = :subjectId
        AND (
            users.username LIKE :keyword OR 
            users.fullname LIKE :keyword
        )
    ) AS subquery
    GROUP BY user_id
    ORDER BY avg_correct_percentage DESC";

        // Các tham số truyền vào truy vấn SQL
        $params = [
            ':subjectId' => $subjectId,
            ':keyword' => '%' . $keyword . '%'
        ];

        // Thực thi truy vấn và trả về kết quả
        return $this->fetchAll($sql, $params);
    }
}
