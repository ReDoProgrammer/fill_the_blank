<?php
require_once 'app/core/Model.php';

class LessionModel extends Model
{
    // Get all lessions
    public function getAllLessions($subjectId, $keyword = '', $page = 1, $pageSize = 10)
    {
        // Tính toán offset cho phân trang
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Lấy dữ liệu phân trang và tìm kiếm
        $sql = "SELECT l.*, s.name as subject_name
                FROM lessions l
                JOIN subjects s ON l.subject_id = s.id
                WHERE l.subject_id = :subjectId AND l.name LIKE :keyword
                ORDER BY s.name, l.name
                LIMIT :offset, :pageSize";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $lessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy tổng số bản ghi
        $sqlTotal = "SELECT COUNT(*) as total
                     FROM lessions l
                     JOIN subjects s ON l.subject_id = s.id
                     WHERE l.subject_id = :subjectId AND l.name LIKE :keyword";
        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
        $stmtTotal->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        // Tính tổng số trang
        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'lessions' => $lessions,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }


    // Get lesson by ID
    public function getLessionById($id)
    {
        $sql = "SELECT * FROM lessions WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    // Lấy tất cả bài học theo subject_id
    public function getBySubject($subjectId)
    {
        $sql = "SELECT * FROM lessions WHERE subject_id = :subjectId ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Tạo bài học mới
    public function createLession($name, $subjectId)
    {
        // Kiểm tra xem bài học đã tồn tại trong cơ sở dữ liệu hay chưa
        $existingLession = $this->getLessionByName($name, $subjectId);
        if ($existingLession) {
            // Trả về thông báo lỗi nếu bài học đã tồn tại
            return [
                'code' => 409,
                'msg' => 'Bài học đã tồn tại trong hệ thống!'
            ];
        }

        // Chuẩn bị câu lệnh SQL để chèn dữ liệu, bao gồm cả meta được tính toán từ name
        $sql = "INSERT INTO lessions (name, subject_id, meta) VALUES (:name, :subjectId, convertToSlug(:name))";

        // Thực thi câu lệnh SQL với dữ liệu
        $this->execute($sql, ['name' => $name, 'subjectId' => $subjectId]);

        // Trả về phản hồi thành công
        return [
            'code' => 201,
            'msg' => 'Bài học đã được thêm thành công!'
        ];
    }


    public function updateLession($id, $name)
    {
        // Cập nhật thông tin bài học
        $sql = "UPDATE lessions SET name = :name WHERE id = :id";
        $result = $this->execute($sql, ['id' => $id, 'name' => $name]);

        if ($result > 0) {
            // Lấy giá trị meta mới bằng cách gọi hàm convertToSlug trong MySQL
            $sqlMeta = "UPDATE lessions SET meta = convertToSlug(name) WHERE id = :id";
            $this->execute($sqlMeta, ['id' => $id]);

            return [
                'code' => 200,
                'msg' => 'Cập nhật thông tin bài học thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Cập nhật thông tin bài học thất bại!'
            ];
        }
    }


    // Delete lesson
    public function deleteLession($id)
    {
        $sql = "DELETE FROM lessions WHERE id = :id";
        $result = $this->execute($sql, ['id' => $id]);

        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Bài học đã  bị xoá thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Xoá bài học thất bại!'
            ];
        }
    }

    // Get lesson by name
    public function getLessionByName($name, $subjectId)
    {
        $sql = "SELECT * FROM lessions WHERE name = :name AND subject_id = :subjectId";
        return $this->fetch($sql, ['name' => $name, 'subjectId' => $subjectId]);
    }

    public function getExamCountByLession($keyword = '', $from_date, $to_date)
    {
        // Câu lệnh SQL để thống kê số lần làm bài theo lession với điều kiện lọc
        $sql = "
            SELECT 
            subjects.name AS subject_name,
                lessions.name AS lession_name,
                COUNT(exam_results.id) AS total_exams,
                COUNT(DISTINCT exam_results.user_id) AS total_users,
                (SELECT COUNT(*)
                 FROM questions
                 WHERE questions.lession_id = lessions.id) AS total_questions                
            FROM exam_results
            JOIN lessions ON exam_results.lession_id = lessions.id
            JOIN subjects ON lessions.subject_id = subjects.id
            WHERE exam_results.created_at BETWEEN :from_date AND :to_date
            AND lessions.name LIKE :keyword
            GROUP BY lessions.name
            ORDER BY subjects.name,lessions.name, total_exams DESC
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

    public function getReviewStatistic($lessionId, $keyword = '', $page = 1, $pageSize = 10)
    {
        // Tính toán giá trị offset cho phân trang
        $offset = ($page - 1) * $pageSize;

        // Câu lệnh SQL để lấy thống kê kết quả bài làm
        $sql = "
    SELECT 
        user_id,
        username,
        user_code,
        fullname,
        result_id,
        MAX(correct_questions) AS max_correct_questions,
        total_questions,
        ROUND((MAX(correct_questions) / total_questions) * 100, 2) AS highest_score_percentage,
        ROUND((MAX(correct_questions) / total_questions) * 10, 2) AS score,
        MAX(total_blanks) AS total_blanks,
        MAX(correct_blanks) AS correct_blanks,
        ROUND((MAX(correct_blanks) / MAX(total_blanks)) * 100, 2) AS correct_blanks_percent
    FROM (
        SELECT 
            exam_results.id AS result_id,
            exam_results.user_id,
            users.username,
            users.fullname,
            users.user_code,
            exam_results.id AS exam_id,
            (SELECT COUNT(*) 
             FROM questions 
             WHERE questions.lession_id = :lessionId) AS total_questions,
            -- Tính tổng số blanks trong bài học
            (SELECT COUNT(*) 
             FROM question_blanks 
             JOIN questions ON question_blanks.question_id = questions.id
             WHERE questions.lession_id = :lessionId) AS total_blanks,
            -- Tính số câu trả lời đúng (tất cả các blanks trong câu hỏi phải đúng)
            (SELECT COUNT(*) 
             FROM questions q
             WHERE q.lession_id = :lessionId
             AND NOT EXISTS (
                 SELECT * 
                 FROM question_blanks qb
                 LEFT JOIN exam_answers ea ON qb.id = ea.question_blank_id AND ea.exam_result_id = exam_results.id
                 WHERE qb.question_id = q.id
                 AND (ea.answer IS NULL OR ea.answer != qb.blank_text)
             )) AS correct_questions,
            -- Tính số blanks trả lời đúng
            (SELECT COUNT(*)
             FROM exam_answers ea
             JOIN question_blanks qb ON ea.question_blank_id = qb.id
             WHERE ea.exam_result_id = exam_results.id 
             AND ea.answer = qb.blank_text) AS correct_blanks
        FROM exam_results
        JOIN users ON exam_results.user_id = users.id
        JOIN lessions ON exam_results.lession_id = lessions.id
        JOIN subjects ON lessions.subject_id = subjects.id
        WHERE exam_results.lession_id = :lessionId
        AND (
            users.username LIKE :keyword OR 
            users.fullname LIKE :keyword
        )
        GROUP BY exam_results.id, exam_results.user_id
    ) AS subquery
    GROUP BY user_id
    ORDER BY highest_score_percentage DESC
    LIMIT $offset, $pageSize"; // Truyền trực tiếp offset và pageSize

        // Các tham số truyền vào truy vấn SQL
        $params = [
            ':lessionId' => $lessionId,
            ':keyword' => '%' . $keyword . '%'
        ];

        // Thực thi truy vấn và trả về kết quả
        return $this->fetchAll($sql, $params);
    }
}
