<?php
require_once 'app/core/Model.php';
class QuizModel extends Model
{
    public function createQuiz($subject_id, $question, $mark, $options, $correct_option)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi mảng tùy chọn thành chuỗi JSON
            $optionsJson = json_encode([
                'option_1' => $options['option_1'] ?? '',
                'option_2' => $options['option_2'] ?? '',
                'option_3' => $options['option_3'] ?? '',
                'option_4' => $options['option_4'] ?? '',
                'correct_option' => $correct_option
            ]);

            // Thêm câu hỏi trắc nghiệm vào bảng quizs
            $sql = "INSERT INTO quizs (subject_id, question, options, mark) VALUES (:subject_id, :question, :options, :mark)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            $stmt->bindParam(':question', $question, PDO::PARAM_STR);
            $stmt->bindParam(':options', $optionsJson, PDO::PARAM_STR);

            // Sử dụng PDO::PARAM_STR để bind giá trị số thực
            $stmt->bindParam(':mark', $mark, PDO::PARAM_STR);

            $stmt->execute();

            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Quiz đã được thêm thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }



    public function updateQuiz($id, $question, $mark, $options, $correct_option)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi mảng tùy chọn thành chuỗi JSON
            $optionsJson = json_encode([
                'option_1' => $options['option_1'] ?? '',
                'option_2' => $options['option_2'] ?? '',
                'option_3' => $options['option_3'] ?? '',
                'option_4' => $options['option_4'] ?? '',
                'correct_option' => $correct_option
            ]);

            // Cập nhật câu hỏi trắc nghiệm trong bảng quizs
            $sqlUpdateQuiz = "UPDATE quizs SET question = :question, mark = :mark, options = :options WHERE id = :id";
            $stmtUpdateQuiz = $this->pdo->prepare($sqlUpdateQuiz);
            $stmtUpdateQuiz->bindParam(':id', $id);
            $stmtUpdateQuiz->bindParam(':question', $question);
            $stmtUpdateQuiz->bindParam(':mark', $mark);
            $stmtUpdateQuiz->bindParam(':options', $optionsJson);
            $stmtUpdateQuiz->execute();

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Quiz đã được cập nhật thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function deleteQuiz($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Kiểm tra xem có bài thi nào chứa quiz này trong cột questions không
            $sqlCheck = "SELECT COUNT(*) FROM exams WHERE JSON_CONTAINS(questions, :id, '$') = 1";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindValue(':id', $id);
            $stmtCheck->execute();
            $count = $stmtCheck->fetchColumn();

            if ($count > 0) {
                $this->pdo->rollBack();
                return ['code' => 400, 'msg' => 'Không thể xóa quiz vì nó đang được sử dụng trong các bài thi'];
            }

            // Xóa câu hỏi trắc nghiệm trong bảng quizs
            $sqlDeleteQuiz = "DELETE FROM quizs WHERE id = :id";
            $stmtDeleteQuiz = $this->pdo->prepare($sqlDeleteQuiz);
            $stmtDeleteQuiz->bindParam(':id', $id);
            $stmtDeleteQuiz->execute();

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Quiz đã được xóa thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function getQuizzesBySubject($subject_id)
    {
        // Truy vấn SQL để lấy tất cả các câu hỏi dựa trên subject_id
        $sql = "SELECT q.id, q.question, q.mark
                FROM quizs q
                WHERE q.subject_id = :subject_id
                ORDER BY q.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->execute();
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $quizzes;
    }

    public function search($keyword)
    {
        // Thêm ký tự '%' để thực hiện tìm kiếm tương đối (LIKE)
        $keyword = "%" . $keyword . "%";

        // Truy vấn SQL để tìm kiếm các câu hỏi có chứa từ khóa
        $sql = "SELECT q.id, q.question, q.mark
            FROM quizs q
            WHERE q.question LIKE :keyword
            ORDER BY q.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $quizzes;
    }


    public function getAllQuizzes($subject_id, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Truy vấn SQL với JOIN để lấy thông tin câu hỏi và các tùy chọn
        $sql = "SELECT q.id, q.question, q.mark
            FROM quizs q
            WHERE q.subject_id = :subject_id AND q.question LIKE :keyword
            ORDER BY q.id
            LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Truy vấn SQL để lấy tổng số câu hỏi
        $sqlTotal = "SELECT COUNT(*) as total
                 FROM quizs q
                 WHERE q.subject_id = :subject_id AND q.question LIKE :keyword";

        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':subject_id', $subject_id);
        $stmtTotal->bindParam(':keyword', $keyword);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'quizzes' => $quizzes,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }


    public function getQuizById($id)
    {
        try {
            // Truy vấn để lấy thông tin câu hỏi từ bảng quizs
            $sql = "SELECT id, question, mark, options
                FROM quizs
                WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch kết quả
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['code' => 404, 'msg' => 'Không tìm thấy quiz'];
            }

            // Giải mã chuỗi JSON từ cột options
            $options = json_decode($result['options'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON'];
            }

            // Tạo mảng kết quả câu hỏi với các tùy chọn
            $quiz = [
                'id' => $result['id'],
                'question' => $result['question'],
                'mark' => $result['mark'],
                'options' => $options
            ];

            return ['code' => 200, 'quiz' => $quiz];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    public function getMarksAndQuestionCounts($subject_id)
    {
        try {
            // Truy vấn SQL để lấy số lượng câu hỏi theo điểm số
            $sql = "SELECT mark, COUNT(*) as question_count
                FROM quizs
                WHERE subject_id = :subject_id
                GROUP BY mark
                ORDER BY mark";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra nếu không có kết quả
            if (!$result) {
                return ['code' => 404, 'msg' => 'Không tìm thấy dữ liệu'];
            }

            return ['code' => 200, 'data' => $result];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


}
