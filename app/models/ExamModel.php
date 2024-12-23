<?php
require_once 'app/core/Model.php';

class ExamModel extends Model
{
    public function getExamsByUserId($userId)
    {
        // SQL truy vấn để lấy thông tin các bài kiểm tra từ userId
        $sql = "
            SELECT
                e.id AS exam_id,
                e.title AS exam_title,
                e.description AS exam_description,
                e.number_of_questions,
                e.duration,
                e.begin_date,
                e.end_date,
                s.id AS subject_id,
                s.name AS subject_name,
                s.meta AS subject_meta
            FROM users u
            INNER JOIN teachings t ON t.id = u.teaching_id
            INNER JOIN exams e ON e.teaching_id = t.id
            INNER JOIN subjects s ON s.id = e.subject_id
            WHERE u.id = :userId
                AND e.begin_date <= NOW()
                AND e.end_date >= NOW()
            ORDER BY s.id, e.begin_date DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch tất cả các kết quả
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) {
            return null; // Nếu không có dữ liệu
        }

        $subjects = [];

        foreach ($results as $result) {
            $subjectId = $result['subject_id'];

            if (!isset($subjects[$subjectId])) {
                $subjects[$subjectId] = [
                    'subject' => [
                        'id' => $result['subject_id'],
                        'name' => $result['subject_name'],
                        'meta' => $result['subject_meta'],
                    ],
                    'exams' => []
                ];
            }

            $subjects[$subjectId]['exams'][] = [
                'exam_id' => $result['exam_id'],
                'title' => $result['exam_title'],
                'description' => $result['exam_description'],
                'number_of_questions' => $result['number_of_questions'],
                'duration' => $result['duration'],
                'begin_date' => $result['begin_date'],
                'end_date' => $result['end_date']
            ];
        }

        return array_values($subjects);
    }




    public function createExam($teaching_id, $title, $description, $number_of_questions, $duration, $mode, $thumbnail, $begin_date, $end_date, $subject_id, $created_by)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi định dạng ngày giờ từ dd/mm/yyyy hh:mm sang Y-m-d H:i:s
            $begin_date = DateTime::createFromFormat('d/m/Y H:i', $begin_date)->format('Y-m-d H:i:s');
            $end_date = DateTime::createFromFormat('d/m/Y H:i', $end_date)->format('Y-m-d H:i:s');

            // Kiểm tra số câu hỏi có sẵn cho subject_id
            $sqlCheck = "SELECT COUNT(*) FROM quizs WHERE subject_id = :subject_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':subject_id', $subject_id);
            $stmtCheck->execute();
            $availableQuestions = $stmtCheck->fetchColumn();

            if ($availableQuestions < $number_of_questions) {
                return ['code' => 400, 'msg' => 'Ngân  hàng câu hỏi của môn học này không đáp ứng đủ!'];
            }

            // Insert một kỳ thi mới vào bảng exams
            $sql = "INSERT INTO exams (teaching_id,title, description, number_of_questions, duration, mode, thumbnail, begin_date, end_date, subject_id,created_by) 
                VALUES (:teaching_id,:title, :description, :number_of_questions, :duration, :mode, :thumbnail, :begin_date, :end_date, :subject_id,:created_by)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':teaching_id', $teaching_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindValue(':number_of_questions', $number_of_questions, PDO::PARAM_INT);
            $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
            $stmt->bindParam(':mode', $mode, PDO::PARAM_INT);
            $stmt->bindParam(':thumbnail', $thumbnail);
            $stmt->bindParam(':begin_date', $begin_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':subject_id', $subject_id);
            $stmt->bindParam(':created_by', $created_by);
            $stmt->execute();

            // Lấy id của exam vừa được insert
            $exam_id = $this->pdo->lastInsertId();

            if ($mode == 0) {
                // Truy vấn để lấy các câu hỏi ngẫu nhiên từ bảng quizs
                $sqlQuiz = "SELECT id FROM quizs 
                        WHERE subject_id = :subject_id 
                        ORDER BY RAND() 
                        LIMIT :number_of_questions";
                $stmtQuiz = $this->pdo->prepare($sqlQuiz);
                $stmtQuiz->bindParam(':subject_id', $subject_id);
                $stmtQuiz->bindValue(':number_of_questions', $number_of_questions, PDO::PARAM_INT);
                $stmtQuiz->execute();
                $quizzes = $stmtQuiz->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy cột id của các quiz

                // Chuyển các id thành JSON và cập nhật cột questions
                $questionsJson = json_encode($quizzes);

                $sqlUpdate = "UPDATE exams SET questions = :questions WHERE id = :exam_id";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':questions', $questionsJson);
                $stmtUpdate->bindParam(':exam_id', $exam_id);
                $stmtUpdate->execute();
            } else {
                if ($mode > 0) {
                    // Xử lý trường hợp mode > 0: lấy thông tin từ config
                    $sqlConfig = "SELECT levels FROM exam_configs WHERE id = :mode";
                    $stmtConfig = $this->pdo->prepare($sqlConfig);
                    $stmtConfig->bindParam(':mode', $mode);
                    $stmtConfig->execute();
                    $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

                    if (!$config) {
                        throw new Exception('Không tìm thấy cấu hình với ID: ' . $mode);
                    }

                    // Giải mã JSON từ cột levels
                    $levels = json_decode($config['levels'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Lỗi khi giải mã dữ liệu JSON từ cấu hình');
                    }

                    // Chọn câu hỏi dựa trên cấu hình
                    $questions = [];
                    foreach ($levels as $level) {
                        $quantity = isset($level['quantity']) ? (int) $level['quantity'] : 0;
                        $mark = isset($level['mark']) ? (float) $level['mark'] : 0.0;

                        // Truy vấn để lấy các câu hỏi từ bảng quizs
                        $sqlQuiz = "SELECT id FROM quizs 
                     WHERE subject_id = :subject_id 
                     AND mark = :mark 
                     ORDER BY RAND() 
                     LIMIT :quantity";
                        $stmtQuiz = $this->pdo->prepare($sqlQuiz);
                        $stmtQuiz->bindParam(':subject_id', $subject_id);
                        $stmtQuiz->bindParam(':mark', $mark);
                        $stmtQuiz->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                        $stmtQuiz->execute();
                        $quizIds = $stmtQuiz->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy cột id của các quiz

                        $questions = array_merge($questions, $quizIds);
                    }

                    // Nếu số lượng câu hỏi vượt quá số lượng yêu cầu, chỉ lấy số lượng yêu cầu
                    $questions = array_slice($questions, 0, $number_of_questions);

                    // Chuyển các id thành JSON và cập nhật cột questions
                    $questionsJson = json_encode($questions);

                    $sqlUpdate = "UPDATE exams SET questions = :questions WHERE id = :exam_id";
                    $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(':questions', $questionsJson);
                    $stmtUpdate->bindParam(':exam_id', $exam_id);
                    $stmtUpdate->execute();
                }
            }

            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Tạo mới đề thi trắc nghiệm thành công!'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Lỗi tạo mới đề thi trắc nghiệm: ' . $e->getMessage()];
        }
    }


    public function updateExam($id, $teaching_id, $title, $description, $number_of_questions, $duration, $mode, $thumbnail, $begin_date, $end_date, $subject_id, $updated_by)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi định dạng ngày giờ từ dd/mm/yyyy hh:mm sang Y-m-d H:i:s
            $begin_date = DateTime::createFromFormat('d/m/Y H:i', $begin_date)->format('Y-m-d H:i:s');
            $end_date = DateTime::createFromFormat('d/m/Y H:i', $end_date)->format('Y-m-d H:i:s');

            // Kiểm tra số câu hỏi có sẵn cho subject_id
            $sqlCheck = "SELECT COUNT(*) FROM quizs WHERE subject_id = :subject_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':subject_id', $subject_id);
            $stmtCheck->execute();
            $availableQuestions = $stmtCheck->fetchColumn();

            if ($availableQuestions < $number_of_questions) {
                return ['code' => 400, 'msg' => 'Ngân  hàng câu hỏi của môn học này không đáp ứng đủ!'];
            }

            // Khởi tạo câu lệnh SQL với các trường cập nhật cơ bản
            $sql = "UPDATE exams 
                    SET teaching_id=:teaching_id,title = :title, description = :description, number_of_questions = :number_of_questions, 
                        duration = :duration, mode = :mode, 
                        begin_date = :begin_date, end_date = :end_date, subject_id = :subject_id,updated_at = NOW(),updated_by = :updated_by";

            // Kiểm tra nếu thumbnail không rỗng, thêm câu lệnh cập nhật thumbnail vào SQL
            if (!empty($thumbnail)) {
                $sql .= ", thumbnail = :thumbnail";
            }

            // Thêm điều kiện WHERE
            $sql .= " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':teaching_id', $teaching_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindValue(':number_of_questions', $number_of_questions, PDO::PARAM_INT);
            $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
            $stmt->bindParam(':mode', $mode, PDO::PARAM_INT);
            $stmt->bindParam(':begin_date', $begin_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':subject_id', $subject_id);
            $stmt->bindParam(':updated_by', $updated_by);

            // Nếu thumbnail không rỗng, bind giá trị thumbnail vào câu lệnh
            if (!empty($thumbnail)) {
                $stmt->bindParam(':thumbnail', $thumbnail);
            }

            $stmt->execute();

            // Kiểm tra chế độ để cập nhật cột questions
            if ($mode == 0) {
                // Truy vấn để lấy các câu hỏi ngẫu nhiên từ bảng quizs
                $sqlQuiz = "SELECT id FROM quizs 
                            WHERE subject_id = :subject_id 
                            ORDER BY RAND() 
                            LIMIT :number_of_questions";
                $stmtQuiz = $this->pdo->prepare($sqlQuiz);
                $stmtQuiz->bindParam(':subject_id', $subject_id);
                $stmtQuiz->bindValue(':number_of_questions', $number_of_questions, PDO::PARAM_INT);
                $stmtQuiz->execute();
                $quizzes = $stmtQuiz->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy cột id của các quiz

                // Chuyển các id thành JSON và cập nhật cột questions
                $questionsJson = json_encode($quizzes);
            } else {
                if ($mode > 0) {
                    // Xử lý trường hợp mode > 0: lấy thông tin từ config
                    $sqlConfig = "SELECT levels FROM exam_configs WHERE id = :mode";
                    $stmtConfig = $this->pdo->prepare($sqlConfig);
                    $stmtConfig->bindParam(':mode', $mode);
                    $stmtConfig->execute();
                    $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

                    if (!$config) {
                        throw new Exception('Không tìm thấy cấu hình với ID: ' . $mode);
                    }

                    // Giải mã JSON từ cột levels
                    $levels = json_decode($config['levels'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Lỗi khi giải mã dữ liệu JSON từ cấu hình');
                    }

                    // Chọn câu hỏi dựa trên cấu hình
                    $questions = [];
                    foreach ($levels as $level) {
                        $quantity = isset($level['quantity']) ? (int) $level['quantity'] : 0;
                        $mark = isset($level['mark']) ? (float) $level['mark'] : 0.0;

                        // Truy vấn để lấy các câu hỏi từ bảng quizs
                        $sqlQuiz = "SELECT id FROM quizs 
                         WHERE subject_id = :subject_id 
                         AND mark = :mark 
                         ORDER BY RAND() 
                         LIMIT :quantity";
                        $stmtQuiz = $this->pdo->prepare($sqlQuiz);
                        $stmtQuiz->bindParam(':subject_id', $subject_id);
                        $stmtQuiz->bindParam(':mark', $mark);
                        $stmtQuiz->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                        $stmtQuiz->execute();
                        $quizIds = $stmtQuiz->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy cột id của các quiz

                        $questions = array_merge($questions, $quizIds);
                    }

                    // Nếu số lượng câu hỏi vượt quá số lượng yêu cầu, chỉ lấy số lượng yêu cầu
                    $questions = array_slice($questions, 0, $number_of_questions);

                    // Chuyển các id thành JSON và cập nhật cột questions
                    $questionsJson = json_encode($questions);
                }
            }

            // Cập nhật cột questions
            $sqlUpdate = "UPDATE exams SET questions = :questions WHERE id = :exam_id";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':questions', $questionsJson);
            $stmtUpdate->bindParam(':exam_id', $id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Cập nhật bài thi trắc nghiệm thành công!'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => $e->getCode(), 'msg' => 'Lỗi cập nhật bài thi trắc nghiệm: ' . $e->getMessage()];
        }
    }


    public function deleteExam($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Kiểm tra xem có bất kỳ quiz_result nào tham chiếu tới exam này hay không
            $sqlCheck = "SELECT COUNT(*) FROM quiz_results WHERE quiz_id = :id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $count = $stmtCheck->fetchColumn();

            // Nếu có quiz_result tham chiếu, không cho phép xoá
            if ($count > 0) {
                $this->pdo->rollBack();
                return ['code' => 403, 'msg' => 'Đã có bài thi liên quan tới đề bài này nên không thể xoá được!'];
            }

            // Xoá exam từ bảng exams
            $sql = "DELETE FROM exams WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Đề thi được xoá thành công!'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'An error occurred: ' . $e->getMessage()];
        }
    }


    public function getExamById($id)
    {
        try {
            // Fetch a specific exam by its ID
            $sql = "SELECT * FROM exams WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $exam = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                return ['code' => 404, 'msg' => 'Exam not found'];
            }

            // Chuyển đổi begin_date và end_date thành định dạng dd/MM/yyyy hh:mm
            if (!empty($exam['begin_date'])) {
                $beginDate = DateTime::createFromFormat('Y-m-d H:i:s', $exam['begin_date']);
                $exam['begin_date'] = $beginDate ? $beginDate->format('d/m/Y H:i') : null;
            }

            if (!empty($exam['end_date'])) {
                $endDate = DateTime::createFromFormat('Y-m-d H:i:s', $exam['end_date']);
                $exam['end_date'] = $endDate ? $endDate->format('d/m/Y H:i') : null;
            }

            // Lấy danh sách các quiz_id từ cột questions
            $quiz_ids = json_decode($exam['questions']);

            if (!empty($quiz_ids)) {
                // Tạo danh sách các quiz_id để dùng trong câu lệnh IN
                $placeholders = implode(',', array_fill(0, count($quiz_ids), '?'));

                // Fetch các quiz từ bảng quizs theo danh sách quiz_id
                $sqlQuizzes = "SELECT * FROM quizs WHERE id IN ($placeholders)";
                $stmtQuizzes = $this->pdo->prepare($sqlQuizzes);
                foreach ($quiz_ids as $index => $quiz_id) {
                    $stmtQuizzes->bindValue($index + 1, $quiz_id, PDO::PARAM_INT);
                }
                $stmtQuizzes->execute();
                $quizzes = $stmtQuizzes->fetchAll(PDO::FETCH_ASSOC);

                // Thêm thông tin các quiz vào exam
                $exam['questions'] = $quizzes;
            } else {
                $exam['questions'] = [];
            }

            return ['code' => 200, 'exam' => $exam];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'An error occurred: ' . $e->getMessage()];
        }
    }


    public function getAllExams($from_date, $to_date, $subject_id, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Convert from_date and to_date to 'Y-m-d' format
        $from_date = $from_date instanceof DateTime ? $from_date->format('Y-m-d') : $from_date;
        $to_date = $to_date instanceof DateTime ? $to_date->format('Y-m-d') : $to_date;

        // Query to fetch exams with pagination, keyword filtering, and subject/teaching info
        $sql = "SELECT
                e.id AS exam_id,
                e.title,
                e.description,
                e.number_of_questions,
                e.duration,
                e.mode,
                e.thumbnail,
                e.begin_date,
                e.end_date,
                e.subject_id,
                e.teaching_id,
                s.name AS subject_name,  -- Added subject name
                t.name AS teaching_name,  -- Added teaching name
                SUM(q.mark) AS total_marks
            FROM
                exams e
            LEFT JOIN quizs q ON e.questions LIKE CONCAT('%', q.id, '%')
            LEFT JOIN subjects s ON e.subject_id = s.id  -- Join with subjects to get subject name
            LEFT JOIN teachings t ON e.teaching_id = t.id  -- Join with teachings to get teaching name
            WHERE 
                e.subject_id = :subject_id 
                AND e.title LIKE :keyword
                AND DATE(e.end_date) BETWEEN :from_date AND :to_date  -- Compare only the date part (ignore time)
            GROUP BY
                e.id
            LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':from_date', $from_date);
        $stmt->bindParam(':to_date', $to_date);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();

        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format date and check availability
        foreach ($exams as &$exam) {
            // Format date
            $exam['begin_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $exam['begin_date'])->format('d/m/Y H:i');
            $exam['end_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $exam['end_date'])->format('d/m/Y H:i');

            // Check if the exam is referenced in quiz_results
            $sqlCheck = "SELECT COUNT(*) as count FROM quiz_results WHERE exam_id = :exam_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':exam_id', $exam['exam_id']);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            // Set available to true if no references are found, otherwise false
            $exam['available'] = $resultCheck['count'] == 0;
        }

        // Query to count total number of exams for pagination
        $sqlTotal = "SELECT COUNT(*) as total FROM exams WHERE subject_id = :subject_id AND title LIKE :keyword AND DATE(end_date) BETWEEN :from_date AND :to_date";
        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $stmtTotal->bindParam(':keyword', $keyword);
        $stmtTotal->bindParam(':from_date', $from_date);
        $stmtTotal->bindParam(':to_date', $to_date);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'exams' => $exams,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }



    public function getOwnExams($roomId, $from_date = null, $to_date = null, $page = 1, $pageSize = 10, $keyword = '')
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Base query
        $sql = "SELECT
                    e.id AS exam_id,
                    e.title,
                    e.description,
                    e.number_of_questions,
                    e.duration,
                    e.mode,
                    e.thumbnail,
                    e.begin_date,
                    e.end_date,
                    e.subject_id,
                    COALESCE(SUM(q.mark), 0) AS total_marks
                FROM
                    exams e
                LEFT JOIN quizs q ON JSON_CONTAINS(e.questions, JSON_QUOTE(CAST(q.id AS CHAR)), '$')
                WHERE 
                    e.teaching_id = :roomId 
                    AND e.title LIKE :keyword";

        // Add date filters if provided
        if (!empty($from_date)) {
            $sql .= " AND e.begin_date >= :from_date";
        }
        if (!empty($to_date)) {
            $sql .= " AND e.end_date <= :to_date";
        }

        $sql .= " GROUP BY e.id 
                  ORDER BY e.begin_date DESC
                  LIMIT $offset, $pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);

        if (!empty($from_date)) {
            $stmt->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmt->bindParam(':to_date', $to_date);
        }

        $stmt->execute();
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format and check availability
        foreach ($exams as &$exam) {
            // Format date
            $exam['begin_date'] = !empty($exam['begin_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $exam['begin_date'])->format('d/m/Y H:i') : null;
            $exam['end_date'] = !empty($exam['end_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $exam['end_date'])->format('d/m/Y H:i') : null;

            // Check availability
            $sqlCheck = "SELECT COUNT(*) as count FROM quiz_results WHERE exam_id = :exam_id";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':exam_id', $exam['exam_id'], PDO::PARAM_INT);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            $exam['available'] = $resultCheck['count'] == 0;
        }

        // Total count query
        $sqlTotal = "SELECT COUNT(*) as total FROM exams 
                     WHERE teaching_id = :roomId 
                     AND title LIKE :keyword 
                     AND created_by = :created_by";

        if (!empty($from_date)) {
            $sqlTotal .= " AND begin_date >= :from_date";
        }
        if (!empty($to_date)) {
            $sqlTotal .= " AND end_date <= :to_date";
        }

        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $stmtTotal->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmtTotal->bindParam(':created_by', $created_by, PDO::PARAM_INT);

        if (!empty($from_date)) {
            $stmtTotal->bindParam(':from_date', $from_date);
        }
        if (!empty($to_date)) {
            $stmtTotal->bindParam(':to_date', $to_date);
        }

        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'exams' => $exams,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }





    public function getBySubject($subject_id)
    {
        // Lấy ngày hiện tại
        $currentDate = date('Y-m-d H:i:s');

        // Câu truy vấn để lấy các exams theo subject_id và thỏa mãn điều kiện ngày
        $sql = "SELECT * FROM exams 
                WHERE subject_id = :subject_id 
                AND begin_date <= :currentDate 
                AND end_date >= :currentDate
                ORDER BY begin_date";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
        $stmt->execute();

        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Chuyển đổi định dạng ngày tháng nếu có kết quả
        if ($exams) {
            foreach ($exams as &$exam) {
                $exam['begin_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $exam['begin_date'])->format('d/m/Y H:i');
                $exam['end_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $exam['end_date'])->format('d/m/Y H:i');
            }
        }

        return $exams;
    }
    public function CheckAvailable($user_id, $exam_id)
    {
        // Câu lệnh SQL để đếm số dòng dữ liệu thỏa điều kiện
        $sql = "SELECT COUNT(*) as count FROM quiz_results WHERE user_id = :user_id AND exam_id = :exam_id";

        // Chuẩn bị câu lệnh SQL
        $stmt = $this->pdo->prepare($sql);

        // Gán giá trị tham số vào câu lệnh SQL
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);

        // Thực thi câu lệnh SQL
        $stmt->execute();

        // Lấy kết quả đếm số dòng
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Trả về số dòng tìm được
        return $result['count'];
    }

    public function saveResult($user_id, $exam_id, $result, $spent_time)
    {
        try {
            // return ['code' => 201, 'msg' => 'Bài thi của bạn đã được lưu trên hệ thống thành công!','answers'=>$result];
            // Bắt đầu transaction
            $this->pdo->beginTransaction();

            // Lấy ngày giờ hiện tại
            $quiz_date = date('Y-m-d H:i:s');

            // Chuyển đổi mảng kết quả thành JSON
            $result_json = json_encode($result);

            // Kiểm tra nếu có lỗi trong quá trình mã hóa JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['code' => 500, 'msg' => 'Lỗi định dạng kết quả bài thi: ' . json_last_error_msg()];
            }

            // Chèn kết quả vào bảng quiz_results
            $sql = "INSERT INTO quiz_results (user_id, quiz_date, exam_id, result,spend_time) 
                VALUES (:user_id, :quiz_date, :exam_id, :result,:spend_time)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':quiz_date', $quiz_date);
            $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
            $stmt->bindParam(':result', $result_json, PDO::PARAM_STR); // Lưu chuỗi JSON
            $stmt->bindParam(':spend_time', $spent_time, PDO::PARAM_INT);


            $stmt->execute();

            // Commit transaction
            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Bài thi của bạn đã được lưu trên hệ thống thành công!'];
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Lỗi lưu kết quả bài thi trắc nghiệm: ' . $e->getMessage()];
        }
    }


    public function getQuestionsByExamId($exam_id)
    {
        try {
            // Truy vấn để lấy thông tin exam theo exam_id
            $sql = "SELECT questions FROM exams WHERE id = :exam_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
            $stmt->execute();
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                return ['code' => 404, 'msg' => 'Exam not found'];
            }

            // Giải mã chuỗi JSON chứa danh sách ID các câu hỏi
            $questionsIds = json_decode($exam['questions'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['code' => 500, 'msg' => 'Error decoding JSON data'];
            }

            if (empty($questionsIds)) {
                return ['code' => 200, 'questions' => []];
            }

            // Truy vấn để lấy thông tin các câu hỏi dựa trên danh sách ID
            $placeholders = implode(',', array_fill(0, count($questionsIds), '?'));
            $sqlQuiz = "SELECT * FROM quizs WHERE id IN ($placeholders)";
            $stmtQuiz = $this->pdo->prepare($sqlQuiz);
            $stmtQuiz->execute($questionsIds);
            $questions = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

            return ['code' => 200, 'questions' => $questions];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'An error occurred: ' . $e->getMessage()];
        }
    }
}
