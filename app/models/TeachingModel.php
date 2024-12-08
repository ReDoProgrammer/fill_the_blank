<?php
require_once 'app/core/Model.php';

class TeachingModel extends Model
{
    // Lấy danh sách tất cả các "teaching" với phân trang và từ khóa tìm kiếm
    public function getAllTeachings($keyword = '', $page = 1, $pageSize = 10)
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        $sql = "
            SELECT t.*, 
                   u.fullname AS teacher_name, 
                   s.name AS subject_name 
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id
            JOIN subjects s ON t.subject_id = s.id
            WHERE u.fullname LIKE :keyword 
               OR s.name LIKE :keyword
               OR t.school_year LIKE :keyword
            ORDER BY t.school_year DESC, u.fullname, s.name
            LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $teachings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tổng số bản ghi
        $countSql = "
            SELECT COUNT(*) as total 
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id
            JOIN subjects s ON t.subject_id = s.id
            WHERE u.fullname LIKE :keyword 
               OR s.name LIKE :keyword
               OR t.school_year LIKE :keyword";

        $stmtTotal = $this->pdo->prepare($countSql);
        $stmtTotal->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        $totalPages = ceil($total['total'] / $pageSize);

        return [
            'teachings' => $teachings,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $total['total']
        ];
    }

    // Lấy một teaching bằng ID
    public function getTeachingById($id)
    {
        $sql = "
            SELECT t.*, 
                   u.fullname AS teacher_name, 
                   s.name AS subject_name 
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id
            JOIN subjects s ON t.subject_id = s.id
            WHERE t.id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function getClassesByTeacherId($teacherId)
    {
        $sql = "
            SELECT t.id AS teaching_id,t.name AS class_name, t.school_year AS school_year,
            s.id AS subject_id,
            s.name AS subject_name,
            s.meta AS subject_meta
            FROM teachings t
            JOIN subjects s ON t.subject_id = s.id
            WHERE t.teacher_id = :teacherId            
            ORDER BY t.school_year DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $classes;
    }

    
    // Thêm teaching mới
    public function createTeaching($name,$teacherId, $subjectId, $schoolYear)
    {
        // Kiểm tra xem quá trình giảng dạy đã tồn tại hay chưa
        $existingTeaching = $this->isTeachingExist($teacherId, $subjectId, $schoolYear);
        if ($existingTeaching) {
            return [
                'code' => 409, // HTTP Conflict
                'msg' => 'Quá trình giảng dạy này đã tồn tại!'
            ];
        }

        // Thêm mới nếu không tồn tại
        $sql = "INSERT INTO teachings (name,teacher_id, subject_id, school_year) 
                VALUES (:name,:teacherId, :subjectId, :schoolYear)";
        $params = [
            'name'=>$name,
            'teacherId' => $teacherId,
            'subjectId' => $subjectId,
            'schoolYear' => $schoolYear
        ];
        $this->execute($sql, $params);

        return [
            'code' => 201, // HTTP Created
            'msg' => 'Quá trình giảng dạy đã được thêm thành công!'
        ];
    }


    public function updateTeaching($id, $name,$teacherId, $subjectId, $schoolYear)
    {
        // Kiểm tra xem đã tồn tại quá trình giảng dạy với thông tin mới hay chưa
        $existingTeaching = $this->isTeachingExist($teacherId, $subjectId, $schoolYear, $id);
        if ($existingTeaching) {
            return [
                'code' => 409, // HTTP Conflict
                'msg' => 'Quá trình giảng dạy với thông tin này đã tồn tại!'
            ];
        }

        // Cập nhật thông tin nếu không có dữ liệu trùng lặp
        $sql = "UPDATE teachings 
            SET 
                name = :name,
                teacher_id = :teacherId, 
                subject_id = :subjectId, 
                school_year = :schoolYear 
            WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $name,
            'teacherId' => $teacherId,
            'subjectId' => $subjectId,
            'schoolYear' => $schoolYear
        ];
        $this->execute($sql, $params);

        return [
            'code' => 200, // HTTP OK
            'msg' => 'Quá trình giảng dạy đã được cập nhật thành công!'
        ];
    }


    // Xóa teaching
    public function deleteTeaching($id)
    {
        $sql = "DELETE FROM teachings WHERE id = :id";
        $result = $this->execute($sql, ['id' => $id]);

        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Quá trình giảng dạy đã bị xoá thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Xoá quá trình giảng dạy thất bại!'
            ];
        }
    }

    // Kiểm tra sự tồn tại của teaching
    public function isTeachingExist($teacherId, $subjectId, $schoolYear)
    {
        $sql = "
            SELECT * 
            FROM teachings 
            WHERE teacher_id = :teacherId 
              AND subject_id = :subjectId 
              AND school_year = :schoolYear";
        return $this->fetch($sql, [
            'teacherId' => $teacherId,
            'subjectId' => $subjectId,
            'schoolYear' => $schoolYear
        ]);
    }

    
}
