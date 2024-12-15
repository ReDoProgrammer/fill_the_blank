<?php
require_once 'app/core/Model.php';

class TeachingModel extends Model
{
    // Lấy danh sách tất cả các "teaching" với phân trang và từ khóa tìm kiếm
    public function getAllTeachings($keyword = '', $page = 1, $pageSize = 10)
    {
        $offset = ($page - 1) * $pageSize;
        $keyword = "%$keyword%";

        // Cập nhật SQL để loại bỏ trùng lặp trong tên môn học (subject_names)
        $sql = "
            SELECT t.*, u.fullname AS teacher_name,
                GROUP_CONCAT(DISTINCT s.name ORDER BY s.name) AS subject_names,  -- Loại bỏ trùng lặp trong tên môn học
                COUNT(DISTINCT us.id) AS class_size  -- Đếm số học viên (users) có teaching_id tương ứng
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id
            JOIN subjects s ON FIND_IN_SET(s.id, t.subject_ids) > 0  -- Join với bảng subjects
            LEFT JOIN users us ON us.teaching_id = t.id  -- Join với bảng users để lấy sĩ số
            WHERE u.fullname LIKE :keyword 
               OR t.school_year LIKE :keyword
            GROUP BY t.id, u.id  -- Nhóm theo teaching và teacher
            ORDER BY t.school_year DESC, u.fullname
            LIMIT :offset, :pageSize";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $teachings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Truy vấn tổng số bản ghi
        $countSql = "
            SELECT COUNT(*) as total 
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id          
            WHERE u.fullname LIKE :keyword             
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
                   u.fullname AS teacher_name
            FROM teachings t
            JOIN users u ON t.teacher_id = u.id          
            WHERE t.id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    //hàm trả về danh sách các lớp giảng dạy của năm học hiện tại
    public function listCurrentClasses()
    {
        // Lấy năm hiện tại
        $currentYear = date('Y');

        // Câu truy vấn SQL để lấy danh sách theo điều kiện school_year chứa năm hiện tại
        $sql = "
        SELECT t.*, 
               u.fullname AS teacher_name              
        FROM teachings t
        JOIN users u ON t.teacher_id = u.id       
        WHERE t.school_year LIKE :currentYear
        ORDER BY t.school_year DESC, u.fullname";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':currentYear', "%$currentYear%", PDO::PARAM_STR);
        $stmt->execute();

        $currentClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $currentClasses;
    }


    public function getClassesByTeacherId($teacherId)
    {
        $sql = "
            SELECT t.id AS teaching_id,t.name AS class_name, t.school_year AS school_year,
            FROM teachings t         
            WHERE t.teacher_id = :teacherId            
            ORDER BY t.school_year DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $classes;
    }


    // thêm mới quá lớp (quá trình) giảng dạy
    public function createTeaching($name, $teacherId, $subjects, $schoolYear)
    {
        // Kiểm tra ràng buộc dữ liệu
        if (empty($name) || empty($teacherId) || empty($subjects) || empty($schoolYear)) {
            return [
                'code' => 400, // HTTP Bad Request
                'msg' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }



        // Chuyển mảng subjects thành chuỗi các số nguyên, ngăn cách nhau bởi dấu phẩy
        $subjectString = implode(',', $subjects);

        // Kiểm tra xem giảng dạy đã tồn tại hay chưa
        $existingTeaching = $this->isTeachingExist($teacherId, $subjectString, $schoolYear);
        if ($existingTeaching) {
            return [
                'code' => 409, // HTTP Conflict
                'msg' => "Quá trình giảng dạy môn học đã tồn tại!"
            ];
        }

        // Thêm mới nếu không tồn tại
        $sql = "INSERT INTO teachings (name, teacher_id, subject_ids, school_year) 
                VALUES (:name, :teacherId, :subject_ids, :schoolYear)";
        $params = [
            'name' => $name,
            'teacherId' => $teacherId,
            'subject_ids' => $subjectString,
            'schoolYear' => $schoolYear
        ];
        $this->execute($sql, $params);

        return [
            'code' => 201, // HTTP Created
            'msg' => 'Quá trình giảng dạy đã được thêm thành công!'
        ];
    }



    public function updateTeaching($id, $name, $teacherId, $subjects, $schoolYear)
    {
        // Kiểm tra ràng buộc dữ liệu
        if (empty($name) || empty($teacherId) || empty($subjects) || empty($schoolYear)) {
            return [
                'code' => 400, // HTTP Bad Request
                'msg' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        // Chuyển mảng subjects thành chuỗi các số nguyên, ngăn cách nhau bởi dấu phẩy
        $subjectString = implode(',', $subjects);

        // Kiểm tra xem giảng dạy đã tồn tại hay chưa với thông tin mới
        $existingTeaching = $this->isTeachingExist($teacherId, $subjectString, $schoolYear, $id);
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
                    subject_ids = :subjectString, 
                    school_year = :schoolYear 
                WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $name,
            'teacherId' => $teacherId,
            'subjectString' => $subjectString,
            'schoolYear' => $schoolYear
        ];
        $this->execute($sql, $params);

        return [
            'code' => 200, // HTTP OK
            'msg' => 'Quá trình giảng dạy đã được cập nhật thành công!'
        ];
    }


    public function deleteTeaching($id)
    {
        // Kiểm tra xem có dữ liệu nào liên quan đến teaching_id = $id không trong bảng users
        $checkUserSql = "SELECT COUNT(*) AS user_count FROM users WHERE teaching_id = :id";
        $userCount = $this->fetch($checkUserSql, ['id' => $id]);

        // Kiểm tra nếu không có người dùng tham gia
        if ($userCount['user_count'] > 0) { // Đảm bảo trả về đúng số lượng
            return [
                'code' => 400,
                'msg' => 'Không thể xoá quá trình giảng dạy vì có người dùng đang tham gia!'
            ];
        }


        // Tiến hành xoá quá trình giảng dạy nếu không có dữ liệu liên quan
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



    public function isTeachingExist($teacherId, $subjectIds, $schoolYear, $id = 0)
    {
        // Bắt đầu câu truy vấn cơ bản
        $sql = "
        SELECT * 
        FROM teachings 
        WHERE teacher_id = :teacherId 
          AND subject_ids = :subjectIds 
          AND school_year = :schoolYear";

        // Nếu id > 0, thêm điều kiện loại trừ id
        if ($id > 0) {
            $sql .= " AND id != :id";
        }

        // Xây dựng mảng tham số
        $params = [
            'teacherId' => $teacherId,
            'subjectIds' => $subjectIds,
            'schoolYear' => $schoolYear,
        ];

        // Thêm id vào tham số nếu id > 0
        if ($id > 0) {
            $params['id'] = $id;
        }

        // Thực thi câu truy vấn
        return $this->fetch($sql, $params);
    }

    public function getUsers($teachingId, $page = 1, $pageSize = 10)
    {
        // Tính toán offset dựa trên trang và pageSize
        $offset = ($page - 1) * $pageSize;
    
        // Cập nhật câu truy vấn SQL để lấy danh sách học viên từ bảng users với teaching_id
        $sql = "
            SELECT id,user_code,username,fullname,phone,email
            FROM users 
            WHERE teaching_id = :teachingId
            ORDER BY fullname  -- Sắp xếp theo tên học viên
            LIMIT :offset, :pageSize";  // Phân trang
    
        // Chuẩn bị và thực thi câu truy vấn
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':teachingId', $teachingId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Truy vấn tổng số học viên có teaching_id = $teachingId
        $countSql = "
            SELECT COUNT(*) as total 
            FROM users
            WHERE teaching_id = :teachingId";
    
        $stmtTotal = $this->pdo->prepare($countSql);
        $stmtTotal->bindParam(':teachingId', $teachingId, PDO::PARAM_INT);
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    
        // Tính tổng số trang
        $totalPages = ceil($total['total'] / $pageSize);
    
        // Trả về kết quả phân trang
        return [
            'data' => $users,           // Danh sách học viên
            'pagination' => [
                'total' => $total['total'],  // Tổng số học viên
                'totalPages' => $totalPages, // Tổng số trang
                'currentPage' => $page,      // Trang hiện tại
                'pageSize' => $pageSize      // Số lượng bản ghi mỗi trang
            ]
        ];
    }
    
}
