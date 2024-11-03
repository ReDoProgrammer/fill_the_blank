<?php

class QuestionModel extends Model
{
    public function createQuestion($lession_id, $question_text, $blanksJson)
    {
        try {
            // Giải mã JSON thành mảng
            $blanks = json_decode($blanksJson, true);

            // Kiểm tra nếu giải mã không thành công hoặc $blanks không phải là mảng
            if (!is_array($blanks)) {
                throw new Exception('Dữ liệu blanks không hợp lệ.');
            }

            $this->pdo->beginTransaction();

            // Chèn câu hỏi
            $sql = "INSERT INTO questions (lession_id, question_text) VALUES (:lession_id, :question_text)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':lession_id', $lession_id);
            $stmt->bindParam(':question_text', $question_text);
            $stmt->execute();
            $question_id = $this->pdo->lastInsertId();

            // Chèn các blank vào bảng question_blanks
            foreach ($blanks as $blank) {
                $sql = "INSERT INTO question_blanks (question_id, position, blank_text) VALUES (:question_id, :position, :blank_text)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':question_id', $question_id);
                $stmt->bindParam(':position', $blank['position']);
                $stmt->bindParam(':blank_text', $blank['blank_text']);
                $stmt->execute();
            }

            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Câu hỏi đã được thêm thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function updateQuestion($id, $lession_id, $question_text, $blanksJson)
    {
        try {
            // Giải mã JSON thành mảng
            $blanks = json_decode($blanksJson, true);

            // Kiểm tra nếu giải mã không thành công hoặc $blanks không phải là mảng
            if (!is_array($blanks)) {
                throw new Exception('Dữ liệu blanks không hợp lệ.');
            }

            $this->pdo->beginTransaction();

            // Kiểm tra xem lession_id có tồn tại không
            $sqlCheckLession = "SELECT COUNT(*) FROM lessions WHERE id = :lession_id";
            $stmtCheckLession = $this->pdo->prepare($sqlCheckLession);
            $stmtCheckLession->bindParam(':lession_id', $lession_id);
            $stmtCheckLession->execute();
            $countLession = $stmtCheckLession->fetchColumn();

            if ($countLession == 0) {
                throw new Exception('Lession không tồn tại.');
            }

            // Cập nhật câu hỏi
            $sqlUpdateQuestion = "UPDATE questions SET lession_id = :lession_id, question_text = :question_text WHERE id = :id";
            $stmtUpdateQuestion = $this->pdo->prepare($sqlUpdateQuestion);
            $stmtUpdateQuestion->bindParam(':id', $id);
            $stmtUpdateQuestion->bindParam(':lession_id', $lession_id);
            $stmtUpdateQuestion->bindParam(':question_text', $question_text);
            $stmtUpdateQuestion->execute();

            // Xóa các bản ghi cũ trong question_blanks
            $sqlDeleteBlanks = "DELETE FROM question_blanks WHERE question_id = :question_id";
            $stmtDeleteBlanks = $this->pdo->prepare($sqlDeleteBlanks);
            $stmtDeleteBlanks->bindParam(':question_id', $id);
            $stmtDeleteBlanks->execute();

            // Thêm các bản ghi mới vào question_blanks
            foreach ($blanks as $blank) {
                $sqlInsertBlank = "INSERT INTO question_blanks (question_id, position, blank_text) VALUES (:question_id, :position, :blank_text)";
                $stmtInsertBlank = $this->pdo->prepare($sqlInsertBlank);
                $stmtInsertBlank->bindParam(':question_id', $id);
                $stmtInsertBlank->bindParam(':position', $blank['position']);
                $stmtInsertBlank->bindParam(':blank_text', $blank['blank_text']);
                $stmtInsertBlank->execute();
            }

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Câu hỏi đã được cập nhật thành công.'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function getAllQuestions($subject_id, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Truy vấn SQL với JOIN để lấy thông tin bài học và câu hỏi
        $sql = "SELECT q.id, q.question_text, l.name as lession_name
                FROM questions q
                JOIN lessions l ON q.lession_id = l.id
                JOIN subjects s ON l.subject_id = s.id
                WHERE s.id = :subject_id AND q.question_text LIKE :keyword
                ORDER BY l.name, q.question_text
                LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Truy vấn SQL để lấy tổng số câu hỏi
        $sqlTotal = "SELECT COUNT(*) as total
                     FROM questions q
                     JOIN lessions l ON q.lession_id = l.id
                     JOIN subjects s ON l.subject_id = s.id
                     WHERE s.id = :subject_id AND q.question_text LIKE :keyword";

        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':subject_id', $subject_id);
        $stmtTotal->bindParam(':keyword', $keyword);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'questions' => $questions,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }

    public function getAllQuestionsByLession($lession_id)
    {
        // Truy vấn SQL để lấy toàn bộ câu hỏi theo lession_id
        $sql = "SELECT q.id, q.question_text, l.name as lession_name
            FROM questions q
            JOIN lessions l ON q.lession_id = l.id
            WHERE l.id = :lession_id
            ORDER BY q.question_text";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':lession_id', $lession_id, PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $questions;
    }


    public function getQuestionById($id)
    {
        try {
            // Truy vấn để lấy thông tin câu hỏi và các chỗ trống từ bảng question_blanks
            $sql = "SELECT q.id, q.question_text, q.lession_id, qb.id AS blank_id, qb.position, qb.blank_text
                FROM questions q
                LEFT JOIN question_blanks qb ON qb.question_id = q.id
                WHERE q.id = :id
                ORDER BY qb.position";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch tất cả các kết quả
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$results) {
                return ['code' => 404, 'msg' => 'Không tìm thấy câu hỏi']; // Nếu không tìm thấy câu hỏi
            }

            // Lấy thông tin câu hỏi từ bản ghi đầu tiên
            $question = [
                'id' => $results[0]['id'],
                'question_text' => $results[0]['question_text'],
                'lession_id' => $results[0]['lession_id'],
            ];

            // Lấy thông tin các blank từ kết quả
            $blanks = [];
            foreach ($results as $result) {
                if (!empty($result['blank_id'])) {
                    $blanks[] = [
                        'id' => (int) $result['blank_id'],
                        'position' => (int) $result['position'],
                        'blank_text' => $result['blank_text'] // Thêm blank_text vào kết quả
                    ];
                }
            }

            // Truy vấn SQL để tìm câu hỏi tiếp theo
            $sqlNext = "SELECT id
                    FROM questions
                    WHERE lession_id = (SELECT lession_id FROM questions WHERE id = :id) AND id > :current_id
                    ORDER BY id ASC
                    LIMIT 1";

            $stmtNext = $this->pdo->prepare($sqlNext);
            $stmtNext->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtNext->bindParam(':current_id', $id, PDO::PARAM_INT);
            $stmtNext->execute();

            $nextResult = $stmtNext->fetch(PDO::FETCH_ASSOC);

            $hasNext = $nextResult ? true : false;
            $nextQuestionId = $nextResult['id'] ?? null;

            // Truy vấn SQL để tìm câu hỏi trước đó
            $sqlPrev = "SELECT id
                    FROM questions
                    WHERE lession_id = (SELECT lession_id FROM questions WHERE id = :id) AND id < :current_id
                    ORDER BY id DESC
                    LIMIT 1";

            $stmtPrev = $this->pdo->prepare($sqlPrev);
            $stmtPrev->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtPrev->bindParam(':current_id', $id, PDO::PARAM_INT);
            $stmtPrev->execute();

            $prevResult = $stmtPrev->fetch(PDO::FETCH_ASSOC);

            $hasPrev = $prevResult ? true : false;
            $prevQuestionId = $prevResult['id'] ?? null;

            // Truy vấn SQL để đếm tổng số câu hỏi thuộc lession_id
            $sqlTotal = "SELECT COUNT(*) as total
                     FROM questions
                     WHERE lession_id = :lession_id";

            $stmtTotal = $this->pdo->prepare($sqlTotal);
            $stmtTotal->bindParam(':lession_id', $question['lession_id'], PDO::PARAM_INT);
            $stmtTotal->execute();

            $totalResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
            $totalQuestion = $totalResult['total'] ?? 0;

            // Truy vấn để lấy số thứ tự của câu hỏi hiện tại trong lession
            $sqlOrder = "SELECT id
                     FROM questions
                     WHERE lession_id = :lession_id
                     ORDER BY id ASC";

            $stmtOrder = $this->pdo->prepare($sqlOrder);
            $stmtOrder->bindParam(':lession_id', $question['lession_id'], PDO::PARAM_INT);
            $stmtOrder->execute();

            $questions = $stmtOrder->fetchAll(PDO::FETCH_ASSOC);
            $order = array_search(['id' => $question['id']], array_map(function ($q) {
                return ['id' => $q['id']];
            }, $questions)) + 1;

            return [
                'question' => $question,
                'blanks' => $blanks, // Trả về mảng chứa các blank với id, position và text
                'hasNext' => $hasNext,
                'nextQuestionId' => $nextQuestionId,
                'hasPrev' => $hasPrev,
                'prevQuestionId' => $prevQuestionId,
                'totalQuestion' => $totalQuestion, // Thêm tổng số câu hỏi
                'order' => $order // Thêm số thứ tự của câu hỏi hiện tại
            ];

        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }



    public function getMinIdQuestionIdByLessionId($lession_id)
    {
        try {
            // Truy vấn SQL để lấy ID câu hỏi có ID nhỏ nhất dựa vào lession_id
            $sql = "SELECT MIN(id) as min_id
                    FROM questions
                    WHERE lession_id = :lession_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':lession_id', $lession_id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch ID câu hỏi có ID nhỏ nhất
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $min_id = $result ? $result['min_id'] : null;

            // Truy vấn để lấy ID bài học tiếp theo cùng subject_id và kiểm tra nếu bài học đó có câu hỏi
            $sqlNextLession = "SELECT l.id 
                               FROM lessions l
                               WHERE l.subject_id = (SELECT subject_id FROM lessions WHERE id = :lession_id)
                               AND l.id > :lession_id
                               AND EXISTS (SELECT 1 FROM questions q WHERE q.lession_id = l.id)
                               ORDER BY l.id ASC
                               LIMIT 1";

            $stmtNextLession = $this->pdo->prepare($sqlNextLession);
            $stmtNextLession->bindParam(':lession_id', $lession_id, PDO::PARAM_INT);
            $stmtNextLession->execute();

            // Fetch ID của lession tiếp theo có câu hỏi
            $nextLession = $stmtNextLession->fetch(PDO::FETCH_ASSOC);
            $nextLessionId = $nextLession ? $nextLession['id'] : null;

            // Truy vấn để lấy ID bài học trước cùng subject_id và kiểm tra nếu bài học đó có câu hỏi
            $sqlPrevLession = "SELECT l.id 
                               FROM lessions l
                               WHERE l.subject_id = (SELECT subject_id FROM lessions WHERE id = :lession_id)
                               AND l.id < :lession_id
                               AND EXISTS (SELECT 1 FROM questions q WHERE q.lession_id = l.id)
                               ORDER BY l.id DESC
                               LIMIT 1";

            $stmtPrevLession = $this->pdo->prepare($sqlPrevLession);
            $stmtPrevLession->bindParam(':lession_id', $lession_id, PDO::PARAM_INT);
            $stmtPrevLession->execute();

            // Fetch ID của lession trước có câu hỏi
            $prevLession = $stmtPrevLession->fetch(PDO::FETCH_ASSOC);
            $prevLessionId = $prevLession ? $prevLession['id'] : null;

            // Trả về cả min_id, nextLessionId, và prevLessionId
            return [
                'code' => 200,
                'msg' => 'Lấy câu hỏi nhỏ nhất, id của bài học tiếp theo và trước thành công!',
                'minQuestionId' => $min_id,
                'nextLessionId' => $nextLessionId,
                'prevLessionId' => $prevLessionId
            ];

        } catch (Exception $e) {
            return null; // Trả về null nếu xảy ra lỗi
        }
    }




    public function deleteQuestion($id)
    {
        try {
            // Kiểm tra xem có tồn tại question_blank_id trong bảng exam_answers không
            $sqlCheck = "SELECT COUNT(*) FROM exam_answers WHERE question_id = :id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':id', $id);
            $stmtCheck->execute();
            $count = $stmtCheck->fetchColumn();

            if ($count > 0) {
                return ['code' => 400, 'msg' => 'Không thể xóa câu hỏi đã được thành viên làm bài'];
            }

            // Xóa dữ liệu từ bảng question_blanks
            $sqlDeleteBlanks = "DELETE FROM question_blanks WHERE question_id = :question_id";
            $stmtDeleteBlanks = $this->pdo->prepare($sqlDeleteBlanks);
            $stmtDeleteBlanks->bindParam(':question_id', $id);
            $stmtDeleteBlanks->execute();

            // Xóa câu hỏi từ bảng questions
            $sqlDeleteQuestion = "DELETE FROM questions WHERE id = :id";
            $stmtDeleteQuestion = $this->pdo->prepare($sqlDeleteQuestion);
            $stmtDeleteQuestion->bindParam(':id', $id);
            $stmtDeleteQuestion->execute();

            return ['code' => 200, 'msg' => 'Câu hỏi đã được xóa thành công'];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function canDoTest($subject_id, $lession_id, $user_id)
    {
        try {
            // Kiểm tra số lượng dòng dữ liệu hiện có
            $sqlCheck = "SELECT COUNT(*) FROM exam_results WHERE user_id = :user_id AND subject_id = :subject_id AND lession_id = :lession_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':user_id', $user_id);
            $stmtCheck->bindParam(':subject_id', $subject_id);
            $stmtCheck->bindParam(':lession_id', $lession_id);
            $stmtCheck->execute();
            $resultCount = $stmtCheck->fetchColumn();

            // Nếu số lượng dòng dữ liệu < 3, cho phép làm bài kiểm tra
            if ($resultCount < 3) {
                return ['code' => 200, 'msg' => 'Bạn có thể làm bài kiểm tra'];
            } else {
                return ['code' => 409, 'msg' => 'Bạn đã  làm 3 lần kiểm tra bài học này. Vui lòng chọn bài học khác hoặc liên hệ admin để được hỗ trợ!'];
            }
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function saveResult($user_id, $subject_id, $lession_id, $answers)
    {
        try {
            // Kiểm tra số lượng dòng dữ liệu hiện có
            $sqlCheck = "SELECT COUNT(*) FROM exam_results WHERE user_id = :user_id AND subject_id = :subject_id AND lession_id = :lession_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':user_id', $user_id);
            $stmtCheck->bindParam(':subject_id', $subject_id);
            $stmtCheck->bindParam(':lession_id', $lession_id);
            $stmtCheck->execute();
            $resultCount = $stmtCheck->fetchColumn();

            // Nếu số lượng dòng dữ liệu >= 3, không tiếp tục lưu dữ liệu và trả về thông báo
            if ($resultCount >= 3) {
                return ['code' => 409, 'msg' => 'Bạn đã làm đủ 3 lần kiểm tra cho bài học này'];
            }


            $this->pdo->beginTransaction();

            // Chèn kết quả bài thi vào bảng exam_results
            $sqlInsertResult = "INSERT INTO exam_results (user_id, subject_id, lession_id, created_at) VALUES (:user_id, :subject_id, :lession_id, NOW())";
            $stmtInsertResult = $this->pdo->prepare($sqlInsertResult);
            $stmtInsertResult->bindParam(':user_id', $user_id);
            $stmtInsertResult->bindParam(':subject_id', $subject_id);
            $stmtInsertResult->bindParam(':lession_id', $lession_id);
            $stmtInsertResult->execute();

            // Lấy ID của kết quả bài thi vừa chèn
            $exam_result_id = $this->pdo->lastInsertId();

            // Chèn các câu trả lời vào bảng exam_answers
            $sqlInsertAnswer = "INSERT INTO exam_answers (exam_result_id, question_id, question_blank_id, answer) VALUES (:exam_result_id, :question_id, :question_blank_id, :answer)";
            $stmtInsertAnswer = $this->pdo->prepare($sqlInsertAnswer);

            // Duyệt qua từng câu trả lời và chèn vào bảng exam_answers
            foreach ($answers as $answer) {
                $stmtInsertAnswer->bindParam(':exam_result_id', $exam_result_id);
                $stmtInsertAnswer->bindParam(':question_id', $answer['question_id']);
                $stmtInsertAnswer->bindParam(':question_blank_id', $answer['question_blank_id']);
                $stmtInsertAnswer->bindParam(':answer', $answer['answer']);
                $stmtInsertAnswer->execute();
            }

            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Kết quả bài thi đã được lưu thành công', 'id' => $exam_result_id];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }
    public function checkAnswers($questionId, $answers)
    {
        // Lấy tất cả các blanks của câu hỏi từ database
        $blankIds = array_keys($answers);
        $placeholders = implode(',', array_fill(0, count($blankIds), '?'));
        $sql = "SELECT * FROM question_blanks WHERE question_id = ? AND id IN ($placeholders) ORDER BY position";
        $params = array_merge([$questionId], $blankIds);
        $blanks = $this->fetchAll($sql, $params);

        // So sánh câu trả lời của người dùng với câu trả lời đúng
        $result = [];
        foreach ($blanks as $blank) {
            $userAnswer = $answers[$blank['id']] ?? '';

            // Loại bỏ các ký tự xuống hàng và khoảng trắng
            $userAnswer = str_replace(["\r", "\n"], '', $userAnswer);
            $correctAnswer = str_replace(["\r", "\n"], '', $blank['blank_text']);

            $isCorrect = trim(mb_strtolower($userAnswer)) === trim(mb_strtolower($correctAnswer));
            $result[] = [
                'id' => $blank['id'],
                'position' => $blank['position'],
                'correct_answer' => $blank['blank_text'],
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect
            ];
        }

        return ['code' => 200, 'msg' => 'Trả lời câu hỏi thành công!', 'detail' => $result];
    }

    public function getQuestionStatistics($keyword = '', $from_date, $to_date)
    {
        // Câu lệnh SQL với điều kiện lọc
        $sql = "
        SELECT 
            subjects.name AS subject_name,
            lessions.name AS lession_name,
            questions.id AS question_id,
            questions.question_text AS question_text,
            COUNT(DISTINCT exam_result_id) AS total_exams,
            SUM(
                CASE 
                    WHEN total_blanks = correct_blanks THEN 1
                    ELSE 0
                END
            ) AS correct_answers
        FROM (
            SELECT 
                questions.id AS question_id,
                exam_results.id AS exam_result_id,
                COUNT(question_blanks.id) AS total_blanks,
                COUNT(
                    DISTINCT CASE 
                        WHEN exam_answers.answer = question_blanks.blank_text 
                             AND exam_answers.question_blank_id = question_blanks.id 
                        THEN exam_results.id
                        ELSE NULL 
                    END
                ) AS correct_blanks
            FROM exam_results
            JOIN exam_answers ON exam_results.id = exam_answers.exam_result_id
            JOIN questions ON exam_answers.question_id = questions.id
            JOIN question_blanks ON exam_answers.question_blank_id = question_blanks.id
            WHERE exam_results.created_at BETWEEN :from_date AND :to_date
            AND questions.question_text LIKE :keyword
            GROUP BY questions.id, exam_results.id
        ) AS subquery
        JOIN questions ON questions.id = subquery.question_id
        JOIN lessions ON questions.lession_id = lessions.id
        JOIN subjects ON lessions.subject_id = subjects.id
        GROUP BY questions.id
        ORDER BY total_exams DESC;
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


}
