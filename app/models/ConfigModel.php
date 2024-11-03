<?php
require_once 'app/core/Model.php';

class ConfigModel extends Model
{
    public function createConfig($subject_id, $title, $levels)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi mảng levels thành chuỗi JSON
            $levelsJson = json_encode($levels);

            $sql = "INSERT INTO exam_configs (subject_id, title, levels) 
                    VALUES (:subject_id, :title, :levels)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':levels', $levelsJson);

            $stmt->execute();
            $this->pdo->commit();
            return ['code' => 201, 'msg' => 'Cấu hình đã được thêm thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    public function updateConfig($id, $title, $levels)
    {
        try {
            $this->pdo->beginTransaction();

            // Chuyển đổi mảng levels thành chuỗi JSON
            $levelsJson = json_encode($levels);

            $sql = "UPDATE exam_configs 
                    SET title = :title, levels = :levels 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':levels', $levelsJson);

            $stmt->execute();
            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Cấu hình đã được cập nhật thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    public function deleteConfig($id)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "DELETE FROM exam_configs WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return ['code' => 200, 'msg' => 'Cấu hình đã được xóa thành công'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    public function getConfigById($id)
    {
        try {
            $sql = "SELECT id, subject_id, title, levels
                    FROM exam_configs
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['code' => 404, 'msg' => 'Không tìm thấy cấu hình'];
            }

            // Giải mã chuỗi JSON từ cột levels
            $levels = json_decode($result['levels'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON'];
            }

            return [
                'code' => 200,
                'config' => [
                    'id' => $result['id'],
                    'subject_id' => $result['subject_id'],
                    'title' => $result['title'],
                    'levels' => $levels
                ]
            ];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }
    public function getAllConfigs($keyword = '')
    {
        try {
            // Câu lệnh SQL với join để lấy tên môn học từ bảng subjects và điều kiện tìm kiếm theo title
            $sql = "
            SELECT 
                ec.id,
                ec.title,
                s.name AS subject_name,
                ec.levels
            FROM exam_configs ec
            JOIN subjects s ON ec.subject_id = s.id
            WHERE ec.title LIKE :keyword
            ";

            $stmt = $this->pdo->prepare($sql);

            // Thay thế các ký tự đặc biệt trong từ khóa với ký tự thay thế để tránh lỗi SQL
            $keywordParam = "%$keyword%";
            $stmt->bindParam(':keyword', $keywordParam, PDO::PARAM_STR);

            $stmt->execute();
            $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($configs as &$config) {
                // Giải mã chuỗi JSON từ cột levels
                $levels = json_decode($config['levels'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return ['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON'];
                }

                // Tính số câu hỏi và số điểm
                $number_of_questions = 0;
                $marks = 0;

                foreach ($levels as $level) {
                    $quantity = isset($level['quantity']) ? (int) $level['quantity'] : 0;
                    $mark = isset($level['mark']) ? (float) $level['mark'] : 0.0;

                    // Cộng dồn tổng số câu hỏi và tổng số điểm
                    $number_of_questions += $quantity;
                    $marks += $quantity * $mark;
                }

                $config['number_of_questions'] = $number_of_questions;
                $config['marks'] = $marks;
            }

            return ['code' => 200, 'configs' => $configs];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }


    public function getConfigsBySubjectAndCriteria($subject_id, $marks, $number_of_questions)
    {
        try {
            // Câu lệnh SQL với join để lấy tên môn học từ bảng subjects và điều kiện lọc theo subject_id
            $sql = "
            SELECT 
                ec.id,
                ec.title,
                s.name AS subject_name,
                ec.levels
            FROM exam_configs ec
            JOIN subjects s ON ec.subject_id = s.id
            WHERE ec.subject_id = :subject_id
            ";

            $stmt = $this->pdo->prepare($sql);

            // Gán giá trị subject_id vào câu lệnh SQL
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);

            $stmt->execute();
            $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filteredConfigs = [];

            foreach ($configs as &$config) {
                // Giải mã chuỗi JSON từ cột levels
                $levels = json_decode($config['levels'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return ['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON'];
                }

                // Tính số câu hỏi và tổng điểm
                $calculatedQuestions = 0;
                $totalMarks = 0;

                foreach ($levels as $level) {
                    $quantity = isset($level['quantity']) ? (int) $level['quantity'] : 0;
                    $mark = isset($level['mark']) ? (float) $level['mark'] : 0.0;

                    // Cộng dồn tổng số câu hỏi và tổng số điểm
                    $calculatedQuestions += $quantity;
                    $totalMarks += $quantity * $mark;
                }

                // Kiểm tra nếu tổng điểm và tổng số câu hỏi khớp với giá trị đầu vào
                if ($totalMarks == $marks && $calculatedQuestions == $number_of_questions) {
                    $config['number_of_questions'] = $calculatedQuestions;
                    $config['marks'] = $totalMarks;
                    $filteredConfigs[] = $config; // Chỉ thêm các cấu hình hợp lệ vào danh sách kết quả
                }
            }

            return ['code' => 200, 'configs' => $filteredConfigs];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }
    public function getConfigsBySubject($subject_id)
    {
        try {
            // Câu lệnh SQL với join để lấy tên môn học từ bảng subjects và điều kiện lọc theo subject_id
            $sql = "
            SELECT 
                ec.id,
                ec.title,
                s.name AS subject_name,
                ec.levels
            FROM exam_configs ec
            JOIN subjects s ON ec.subject_id = s.id
            WHERE ec.subject_id = :subject_id
            ";

            $stmt = $this->pdo->prepare($sql);

            // Gán giá trị subject_id vào câu lệnh SQL
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);

            $stmt->execute();
            $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['code' => 200, 'configs' => $configs];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    public function getConfigsBySubjectQuestionsAndMarks($subject_id, $number_of_questions)
    {
        try {
            // Câu lệnh SQL với join để lấy tên môn học từ bảng subjects và điều kiện lọc theo subject_id
            $sql = "
            SELECT 
                ec.id,
                ec.title,
                s.name AS subject_name,
                ec.levels
            FROM exam_configs ec
            JOIN subjects s ON ec.subject_id = s.id
            WHERE ec.subject_id = :subject_id
            ";
    
            $stmt = $this->pdo->prepare($sql);
    
            // Gán giá trị subject_id vào câu lệnh SQL
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
    
            $stmt->execute();
            $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $filteredConfigs = [];
    
            foreach ($configs as &$config) {
                // Giải mã chuỗi JSON từ cột levels
                $levels = json_decode($config['levels'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return ['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON'];
                }
    
                // Tính tổng số câu hỏi và tổng điểm
                $calculatedQuestions = 0;
                $totalMarks = 0;
    
                foreach ($levels as $level) {
                    $quantity = isset($level['quantity']) ? (int) $level['quantity'] : 0;
                    $mark = isset($level['mark']) ? (float) $level['mark'] : 0.0;
    
                    // Cộng dồn tổng số câu hỏi và tổng điểm
                    $calculatedQuestions += $quantity;
                    $totalMarks += $quantity * $mark;
                }
    
                // Kiểm tra nếu tổng số câu hỏi và tổng điểm khớp với giá trị đầu vào
                if ($calculatedQuestions == $number_of_questions) {
                    $config['number_of_questions'] = $calculatedQuestions;
                    $config['marks'] = $totalMarks;
                    $filteredConfigs[] = $config; // Chỉ thêm các cấu hình hợp lệ vào danh sách kết quả
                }
            }
    
            return ['code' => 200, 'configs' => $filteredConfigs];
        } catch (Exception $e) {
            return ['code' => 500, 'msg' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }
    


}
