<?php
require_once 'app/core/Model.php';

class HistoryModel extends Model
{
    public function ownHistory($user_id, $currentPage = 1, $resultsPerPage = 10, $keyword = '')
    {
        // Tính toán chỉ số bắt đầu cho LIMIT
        $offset = ($currentPage - 1) * $resultsPerPage;
    
        // Tạo điều kiện tìm kiếm nếu có từ khóa
        $searchCondition = '';
        $searchParams = [];
        if (!empty($keyword)) {
            $searchCondition = " AND (s.name LIKE ? OR l.name LIKE ?) ";
            $searchParams = ["%$keyword%", "%$keyword%"];
        }
    
        // Đếm tổng số kết quả
        $countSql = "
            SELECT COUNT(*) as total
            FROM exam_results er
            JOIN lessions l ON er.lession_id = l.id
            JOIN subjects s ON l.subject_id = s.id
            WHERE er.user_id = ?
            $searchCondition
        ";
        $totalResults = $this->fetch($countSql, array_merge([$user_id], $searchParams))['total'];
    
        // Tính toán tổng số trang
        $totalPages = ceil($totalResults / $resultsPerPage);
    
        // Kiểm tra có trang trước và trang sau hay không
        $hasNext = $currentPage < $totalPages;
        $hasPrev = $currentPage > 1;
    
        // Lấy danh sách kết quả với LIMIT và OFFSET
        $sql = "
            SELECT 
                er.id,
                DATE_FORMAT(er.created_at, '%d/%m/%Y %H:%i') AS exam_date,
                s.name AS subject_name,
                l.name AS lession_name,
                (SELECT COUNT(*) 
                 FROM questions q 
                 WHERE q.id IN (SELECT DISTINCT ea.question_id 
                                FROM exam_answers ea 
                                WHERE ea.exam_result_id = er.id) 
                 AND NOT EXISTS (SELECT * 
                                 FROM question_blanks qb 
                                 LEFT JOIN exam_answers ea 
                                 ON qb.id = ea.question_blank_id 
                                 AND ea.exam_result_id = er.id 
                                 WHERE qb.question_id = q.id 
                                 AND (ea.answer IS NULL OR ea.answer != qb.blank_text))) AS correctQuestions,
                (SELECT COUNT(DISTINCT ea.question_id) 
                 FROM exam_answers ea 
                 WHERE ea.exam_result_id = er.id) AS totalAnswers,
                (SELECT COUNT(*) 
                 FROM question_blanks qb 
                 JOIN exam_answers ea ON ea.question_blank_id = qb.id 
                 WHERE ea.exam_result_id = er.id) AS blanksNumbers,
                (SELECT COUNT(*) 
                 FROM questions q
                 WHERE q.lession_id = er.lession_id) AS totalQuestions,
                (SELECT COUNT(*)
                 FROM exam_answers ea
                 JOIN question_blanks qb ON ea.question_blank_id = qb.id
                 WHERE ea.exam_result_id = er.id AND ea.answer = qb.blank_text) AS correct_answers
            FROM 
                exam_results er
            JOIN 
                lessions l ON er.lession_id = l.id
            JOIN 
                subjects s ON l.subject_id = s.id
            WHERE 
                er.user_id = ?
                $searchCondition
            ORDER BY 
                s.id, l.id, er.id ASC
            LIMIT $resultsPerPage OFFSET $offset
        ";
    
        $params = array_merge([$user_id], $searchParams);
        $results = $this->fetchAll($sql, $params);
    
        // Tính số lần ôn tập và tỉ lệ
        $attempts = [];
        foreach ($results as &$result) {
            $key = $result['lession_name'] . '|' . $result['subject_name'];
            if (!isset($attempts[$key])) {
                $attempts[$key] = 0;
            }
            $attempts[$key]++;
            $result['attempt_number'] = $attempts[$key];
    
            // Tính tỉ lệ phần trăm
            $result['correct_answers_percentage'] = $result['blanksNumbers'] > 0 
                ? round(($result['correct_answers'] / $result['blanksNumbers']) * 100, 2) 
                : 0;
            $result['correct_questions_percentage'] = $result['totalQuestions'] > 0 
                ? round(($result['correctQuestions'] / $result['totalQuestions']) * 100, 2) 
                : 0;
        }
    
        return [
            'history' => $results,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'hasNext' => $hasNext,
            'hasPrev' => $hasPrev
        ];
    }
    


    public function ownQuizHistory($user_id, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;

        $searchCondition = '';
        $searchParams = [];
        if (!empty($keyword)) {
            $searchCondition = " AND (s.name LIKE :subject_name OR e.title LIKE :exam_title) ";
            $searchParams = [
                ':subject_name' => "%$keyword%",
                ':exam_title' => "%$keyword%",
            ];
        }

        // Tính tổng số kết quả phù hợp
        $countSql = "
            SELECT COUNT(*) as total
            FROM quiz_results qr
            JOIN exams e ON qr.exam_id = e.id            
            JOIN subjects s ON e.subject_id = s.id
            WHERE qr.user_id = :user_id
            $searchCondition
        ";

        $stmt = $this->pdo->prepare($countSql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        foreach ($searchParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $totalResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $totalPages = ceil($totalResults / $pageSize);
        $hasNext = $page < $totalPages;
        $hasPrev = $page > 1;

        // Lấy chi tiết lịch sử bài thi
        $sql = "
            SELECT 
                qr.id AS quiz_result_id,
                s.name AS subject_name,
                e.title AS exam_title,
                e.duration,
                JSON_LENGTH(e.questions) AS number_of_questions,
                DATE_FORMAT(e.begin_date, '%d/%m/%Y %H:%i') AS begin_date,
                DATE_FORMAT(e.end_date, '%d/%m/%Y %H:%i') AS end_date,
                e.thumbnail,
                e.questions AS exam_questions,
                qr.result AS quiz_result,
                DATE_FORMAT(qr.quiz_date, '%d/%m/%Y %H:%i') AS quiz_date,
                qr.spend_time AS spent_time
            FROM 
                quiz_results qr
            JOIN 
                exams e ON qr.exam_id = e.id
            JOIN 
                subjects s ON e.subject_id = s.id
            WHERE 
                qr.user_id = :user_id
                $searchCondition
            ORDER BY 
                e.subject_id, e.id, qr.id ASC
            LIMIT :pageSize OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        foreach ($searchParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính toán điểm, câu trả lời đúng và số lần thi cho mỗi bài thi
        $attempts = [];
        foreach ($results as &$result) {
            $quizResult = json_decode($result['quiz_result'], true);
            $examQuestions = json_decode($result['exam_questions'], true);

            $marks = 0;
            $gotMarks = 0;
            $correctAnswers = 0; // Biến đếm số câu trả lời đúng

            foreach ($examQuestions as $quizId) {
                $quizSql = "SELECT options, mark FROM quizs WHERE id = ?";
                $quiz = $this->fetch($quizSql, [$quizId]);
                if ($quiz) {
                    $options = json_decode($quiz['options'], true);
                    $correctOption = $options['correct_option'] ?? '';
                    $marks += $quiz['mark'];

                    // Kiểm tra câu trả lời của người dùng
                    foreach ($quizResult as $userAnswer) {
                        if ($userAnswer['id'] == $quizId && $userAnswer['choice'] == $correctOption) {
                            $gotMarks += $quiz['mark'];
                            $correctAnswers++;
                            break;
                        }
                    }
                }
            }

            // Gắn kết quả vào từng bài thi
            $result['marks'] = $marks;
            $result['got_marks'] = $gotMarks;
            $result['correct_answers'] = $correctAnswers;

            // Tính số lần thi
            $key = $result['exam_title'] . '|' . $result['subject_name'];
            if (!isset($attempts[$key])) {
                $attempts[$key] = 0;
            }
            $attempts[$key]++;
            $result['attempt_number'] = $attempts[$key];
        }

        return [
            'history' => $results,
            'totalPages' => $totalPages,
            'currentPage' => (int) $page,
            'hasNext' => $hasNext,
            'hasPrev' => $hasPrev
        ];
    }


    private function get_first_name($fullname)
    {
        // Tách chuỗi fullname bằng dấu cách và lấy từ cuối cùng
        $nameParts = explode(' ', trim($fullname));
        return end($nameParts); // Lấy phần tử cuối cùng
    }
    public function quizHistory($exam_id, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;

        $searchCondition = '';
        $searchParams = [];
        if (!empty($keyword)) {
            $searchCondition = " AND (u.username LIKE :username OR e.title LIKE :title) ";
            $searchParams = [
                ':username' => "%$keyword%",
                ':title' => "%$keyword%"
            ];
        }

        // Truy vấn lấy tất cả kết quả bài thi cho exam_id và các điều kiện tìm kiếm
        $sql = "
        SELECT 
            qr.id AS quiz_result_id,
            u.username,
            u.user_code,
            u.fullname,
            e.title AS exam_title,
            e.duration,
            JSON_LENGTH(e.questions) AS number_of_questions,
            DATE_FORMAT(e.begin_date, '%d/%m/%Y %H:%i') AS begin_date,
            DATE_FORMAT(e.end_date, '%d/%m/%Y %H:%i') AS end_date,
            e.thumbnail,
            qr.result AS quiz_result,
            DATE_FORMAT(qr.quiz_date, '%d/%m/%Y %H:%i') AS quiz_date,
            qr.spend_time AS spent_time,
            e.questions AS exam_questions
        FROM 
            quiz_results qr
        JOIN 
            exams e ON qr.exam_id = e.id
        JOIN 
            users u ON qr.user_id = u.id
        WHERE 
            qr.exam_id = :exam_id
            $searchCondition
        ";

        // Thiết lập tham số cho câu lệnh SQL
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':exam_id', $exam_id, PDO::PARAM_INT);
        // Thêm các tham số tìm kiếm
        foreach ($searchParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lưu trữ kết quả người dùng với điểm cao nhất
        $userTopResults = [];
        foreach ($results as $result) {
            $quizResult = json_decode($result['quiz_result'], true);
            $examQuestions = json_decode($result['exam_questions'], true);

            $marks = 0;
            $gotMarks = 0;
            $correctAnswers = 0;

            // Tính điểm và số câu trả lời đúng
            foreach ($examQuestions as $quizId) {
                $quizSql = "SELECT options, mark FROM quizs WHERE id = ?";
                $quiz = $this->fetch($quizSql, [$quizId]);
                if ($quiz) {
                    $options = json_decode($quiz['options'], true);
                    $correctOption = $options['correct_option'] ?? '';
                    $marks += $quiz['mark'];

                    // Kiểm tra câu trả lời của người dùng
                    foreach ($quizResult as $userAnswer) {
                        if ($userAnswer['id'] == $quizId && $userAnswer['choice'] == $correctOption) {
                            $gotMarks += $quiz['mark'];
                            $correctAnswers++;
                            break;
                        }
                    }
                }
            }

            // Ghi nhận kết quả vào mảng
            $result['marks'] = $marks;
            $result['got_marks'] = $gotMarks;
            $result['correct_answers'] = $correctAnswers;

            // Giữ lại bài thi có điểm cao nhất cho mỗi người dùng
            if (!isset($userTopResults[$result['username']]) || $result['got_marks'] > $userTopResults[$result['username']]['got_marks']) {
                $userTopResults[$result['username']] = $result;
            }
        }

        // Chuyển đổi kết quả thành mảng và sắp xếp theo tên và điểm
        $finalResults = array_values($userTopResults);

        // Sắp xếp theo tên tăng dần
        usort($finalResults, function ($a, $b) {
            return $this->get_first_name($a['fullname']) <=> $this->get_first_name($b['fullname']);
        });

        // Giới hạn số lượng kết quả trả về
        $finalResults = array_slice($finalResults, 0, $pageSize);

        return [
            'history' => $finalResults,
            'totalResults' => count($userTopResults), // Tổng số kết quả tìm thấy
            'totalPages' => ceil(count($userTopResults) / $pageSize), // Tổng số trang
            'currentPage' => (int) $page,
            'hasNext' => count($finalResults) > $pageSize,
            'hasPrev' => $page > 1
        ];
    }





    public function allQuizHistory($exam_id, $keyword = '')
    {
        $searchCondition = '';
        $searchParams = [];
        if (!empty($keyword)) {
            $searchCondition = " AND (u.username LIKE ? OR q.question LIKE ?) ";
            $searchParams = ["%$keyword%", "%$keyword%"];
        }

        $sql = "
        SELECT 
            qr.id AS quiz_result_id,
            u.username,
            u.user_code,
            u.fullname,
            e.title AS exam_title,
            e.duration,
            JSON_LENGTH(e.questions) AS number_of_questions,
            DATE_FORMAT(e.begin_date, '%d/%m/%Y %H:%i') AS begin_date,
            DATE_FORMAT(e.end_date, '%d/%m/%Y %H:%i') AS end_date,
            e.thumbnail,
            e.questions AS exam_questions,
            qr.result AS quiz_result,
            DATE_FORMAT(qr.quiz_date, '%d/%m/%Y %H:%i') AS quiz_date,
            qr.spend_time AS spent_time
        FROM 
            quiz_results qr
        JOIN 
            exams e ON qr.exam_id = e.id
        JOIN 
            users u ON qr.user_id = u.id
        WHERE 
            qr.exam_id = ?
            $searchCondition
        ORDER BY 
            u.fullname ASC, -- Sắp xếp theo họ tên
            qr.quiz_date DESC
    ";

        $params = array_merge([$exam_id], $searchParams);
        $results = $this->fetchAll($sql, $params);

        // Lưu trữ điểm cao nhất cho mỗi người dùng
        $userTopResults = [];
        foreach ($results as $result) {
            $quizResult = json_decode($result['quiz_result'], true);
            $examQuestions = json_decode($result['exam_questions'], true);
            $marks = 0;
            $gotMarks = 0;
            $correctAnswers = 0; // Biến đếm số câu trả lời đúng

            // Tính tổng điểm và điểm người dùng đạt được
            foreach ($examQuestions as $quizId) {
                $quizSql = "SELECT options, mark FROM quizs WHERE id = ?";
                $quiz = $this->fetch($quizSql, [$quizId]);
                $options = json_decode($quiz['options'], true);
                $correctOption = $options['correct_option'] ?? '';
                $marks += $quiz['mark'];

                // Kiểm tra xem người dùng đã chọn đáp án đúng hay chưa
                foreach ($quizResult as $userAnswer) {
                    if ($userAnswer['id'] == $quizId && $userAnswer['choice'] == $correctOption) {
                        $gotMarks += $quiz['mark'];
                        $correctAnswers++; // Tăng biến đếm nếu người dùng trả lời đúng
                        break;
                    }
                }
            }

            // Ghi nhận kết quả vào mảng, nhưng chỉ giữ lại điểm cao nhất
            $result['marks'] = $marks;
            $result['got_marks'] = $gotMarks;
            $result['correct_answers'] = $correctAnswers; // Thêm số câu trả lời đúng vào kết quả

            if (!isset($userTopResults[$result['username']]) || $result['got_marks'] > $userTopResults[$result['username']]['got_marks']) {
                $userTopResults[$result['username']] = $result;
            }
        }

        // Chuyển đổi kết quả thành mảng
        $finalResults = array_values($userTopResults);

        return [
            'history' => $finalResults,
            'totalResults' => count($finalResults), // Trả về tổng số kết quả tìm thấy
        ];
    }

    public function getTopResults($exam_id)
    {
        // Truy vấn để lấy kết quả bài thi
        $resultsSql = "
        SELECT 
            qr.id AS quiz_result_id,
            u.username,
            u.user_code,
            u.fullname,
            e.title AS exam_title,
            e.duration,
            JSON_LENGTH(e.questions) AS number_of_questions,
            DATE_FORMAT(e.begin_date, '%d/%m/%Y %H:%i') AS begin_date,
            DATE_FORMAT(e.end_date, '%d/%m/%Y %H:%i') AS end_date,
            e.thumbnail,
            qr.result AS quiz_result,
            DATE_FORMAT(qr.quiz_date, '%d/%m/%Y %H:%i') AS quiz_date,
            qr.spend_time AS spent_time,
            e.questions AS exam_questions
        FROM 
            quiz_results qr
        JOIN 
            exams e ON qr.exam_id = e.id
        JOIN 
            users u ON qr.user_id = u.id
        WHERE 
            qr.exam_id = ?
        ";

        $results = $this->fetchAll($resultsSql, [$exam_id]);

        // Tính điểm và lọc kết quả
        $userResults = [];
        foreach ($results as $result) {
            $quizResult = json_decode($result['quiz_result'], true);
            $examQuestions = json_decode($result['exam_questions'] ?? '[]', true);

            $marks = 0;
            $gotMarks = 0;
            $correctAnswers = 0; // Biến đếm số câu trả lời đúng

            foreach ($examQuestions as $quizId) {
                $quizSql = "SELECT options, mark FROM quizs WHERE id = ?";
                $quiz = $this->fetch($quizSql, [$quizId]);
                if ($quiz) {
                    $options = json_decode($quiz['options'], true);
                    $correctOption = $options['correct_option'] ?? '';
                    $marks += $quiz['mark'];

                    // Kiểm tra xem người dùng đã chọn đáp án đúng hay chưa
                    foreach ($quizResult as $userAnswer) {
                        if ($userAnswer['id'] == $quizId && $userAnswer['choice'] == $correctOption) {
                            $gotMarks += $quiz['mark'];
                            $correctAnswers++; // Tăng biến đếm nếu người dùng trả lời đúng
                            break;
                        }
                    }
                }
            }

            $result['marks'] = $marks;
            $result['got_marks'] = $gotMarks;
            $result['correct_answers'] = $correctAnswers; // Thêm số câu trả lời đúng vào kết quả

            // Keep only the highest score for each user
            if (!isset($userResults[$result['username']]) || $result['got_marks'] > $userResults[$result['username']]['got_marks']) {
                $userResults[$result['username']] = $result;
            }
        }

        // Sort results by got_marks and spent_time
        usort($userResults, function ($a, $b) {
            return $b['got_marks'] <=> $a['got_marks'] ?: $a['spent_time'] <=> $b['spent_time'];
        });

        // Get top 3 results
        $topResults = array_slice($userResults, 0, 3);

        return [
            'code' => 200,
            'msg' => 'Lấy top 3 thành viên làm bài thi thành công!',
            'history' => $topResults
        ];
    }






    function detail($exam_result_id)
    {
        $sql = "
        SELECT 
            ea.id AS exam_answer_id,
            ea.answer AS user_answer,
            q.id AS question_id,
            q.question_text,
            qb.position,
            qb.blank_text AS correct_answer
        FROM 
            exam_answers ea
        JOIN 
            questions q ON ea.question_id = q.id
        JOIN 
            question_blanks qb ON ea.question_blank_id = qb.id
        WHERE 
            ea.exam_result_id = ?
        ORDER BY 
            q.id, qb.position
    ";

        $params = [$exam_result_id];
        $results = $this->fetchAll($sql, $params);

        $details = [];
        foreach ($results as $row) {
            $question_id = $row['question_id'];
            if (!isset($details[$question_id])) {
                $details[$question_id] = [
                    'question_id' => $row['question_id'],
                    'question_text' => $row['question_text'],
                    'answers' => []
                ];
            }
            $details[$question_id]['answers'][] = [
                'exam_answer_id' => $row['exam_answer_id'],
                'position' => $row['position'],
                'correct_answer' => $row['correct_answer'],
                'user_answer' => $row['user_answer']
            ];
        }

        // Đổi định dạng kết quả từ dạng [question_id => detail] sang dạng list
        $details = array_values($details);

        return $details;
    }

    public function getQuizDetails($quizResultId)
    {
        // Lấy thông tin bài thi từ bảng quiz_results
        $quizResultSql = "
            SELECT 
                qr.exam_id,
                qr.result AS quiz_result,
                e.questions AS exam_questions
            FROM 
                quiz_results qr
            JOIN 
                exams e ON qr.exam_id = e.id
            WHERE 
                qr.id = ?
        ";
        $quizResult = $this->fetch($quizResultSql, [$quizResultId]);

        if (!$quizResult) {
            return []; // Không tìm thấy kết quả
        }

        $quizResultData = json_decode($quizResult['quiz_result'], true);
        $examQuestions = json_decode($quizResult['exam_questions'], true);

        // Danh sách câu hỏi và đáp án của người dùng
        $details = [];

        foreach ($examQuestions as $quizId) {
            // Lấy thông tin câu hỏi từ bảng quizs
            $quizSql = "
                SELECT 
                    id AS question_id, 
                    question, 
                    options, 
                    mark 
                FROM 
                    quizs 
                WHERE 
                    id = ?
            ";
            $quiz = $this->fetch($quizSql, [$quizId]);

            if ($quiz) {
                // Giải mã JSON của trường options
                $options = isset($quiz['options']) ? json_decode($quiz['options'], true) : null;
                $correctOption = isset($options['correct_option']) ? $options['correct_option'] : null;

                // Tìm đáp án của người dùng cho câu hỏi này
                $userAnswer = null;
                foreach ($quizResultData as $answer) {
                    if ($answer['id'] == $quizId) {
                        $userAnswer = $answer['choice'];
                        break;
                    }
                }

                // Thêm câu hỏi và đáp án của người dùng vào kết quả
                $details[] = [
                    'question_id' => $quiz['question_id'],
                    'question_text' => $quiz['question'],
                    'user_answer' => (int) $userAnswer,
                    'correct_option' => $correctOption,
                    'options' => $options, // Các tùy chọn (nếu có)
                    'mark' => $quiz['mark'] // Thêm thuộc tính mark
                ];
            }
        }

        return $details;
    }
}
