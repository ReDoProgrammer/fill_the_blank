<?php
// models/UserModel.php

require_once 'app/core/Model.php';

class UserModel extends Model
{
    public function getAllUsers($keyword, $page, $pageSize, $role = 'user')
    {
        // Tính toán OFFSET
        $offset = ($page - 1) * $pageSize;

        // Câu lệnh SQL để lấy dữ liệu người dùng theo phân trang
        $sql = "SELECT * FROM users
                WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                  AND role = :role
                ORDER BY username, fullname
                LIMIT :offset, :pageSize"; // Dùng tham số hóa

        // Câu lệnh SQL để lấy tổng số bản ghi
        $countSql = "SELECT COUNT(*) as total FROM users
                     WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                       AND role = :role";

        // Thay đổi các tham số cho PDO
        $params = [
            ':keyword' => "%$keyword%",
            ':role' => $role
        ];

        // Lấy dữ liệu người dùng (truyền OFFSET và LIMIT bằng cách nối chuỗi hoặc ép kiểu)
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT); // Ép kiểu số nguyên
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Lấy tổng số bản ghi
        $totalRecords = $this->fetch($countSql, $params)['total'];

        // Tính toán số trang
        $totalPages = ceil($totalRecords / $pageSize);

        // Trả về dữ liệu cùng với thông tin phân trang
        return [
            'users' => $users,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $totalRecords
        ];
    }

    public function listByTeachingId($teachingId, $keyword, $page, $pageSize, $role = 'user')
    {
        // Tính toán OFFSET
        $offset = ($page - 1) * $pageSize;

        // Câu lệnh SQL để lấy dữ liệu người dùng theo phân trang
        $sql = "SELECT * FROM users
                WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                  AND role = :role
                  AND teaching_id = :teachingId
                ORDER BY username, fullname
                LIMIT :offset, :pageSize"; // Dùng tham số hóa

        // Câu lệnh SQL để lấy tổng số bản ghi
        $countSql = "SELECT COUNT(*) as total FROM users
                     WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                       AND role = :role
                       AND teaching_id = :teachingId";

        // Thay đổi các tham số cho PDO
        $params = [
            ':keyword' => "%$keyword%",
            ':role' => $role,
            ':teachingId' => $teachingId
        ];

        // Lấy dữ liệu người dùng (truyền OFFSET và LIMIT bằng cách nối chuỗi hoặc ép kiểu)
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':teachingId', $teachingId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT); // Ép kiểu số nguyên
        $stmt->bindValue(':pageSize', (int) $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Lấy tổng số bản ghi
        $totalRecordsStmt = $this->pdo->prepare($countSql);
        $totalRecordsStmt->execute($params);
        $totalRecords = $totalRecordsStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tính toán số trang
        $totalPages = ceil($totalRecords / $pageSize);

        // Trả về dữ liệu cùng với thông tin phân trang
        return [
            'users' => $users,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $totalRecords
        ];
    }



    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        return $this->fetch($sql, ['username' => $username]);
    }

    public function createUser($username, $usercode, $fullname, $phone, $email, $password, $role)
    {
        if ($this->getUserByUsername($username)) {
            return ['code' => 409, 'msg' => 'Username này đã tồn tại trong hệ thống!'];
        }

        if ($this->getUserByCode($usercode)) {
            return ['code' => 409, 'msg' => 'User code này đã tồn tại trong hệ thống!'];
        }

        if ($this->getUserByEmail($email)) {
            return ['code' => 409, 'msg' => 'Email này đã tồn tại trong hệ thống!'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, user_code, fullname, phone, email, password, role) 
                VALUES (:username, :user_code, :fullname, :phone, :email, :password, :role)";

        $data = [
            ':username' => $username,
            ':user_code' => $usercode,
            ':fullname' => $fullname,
            ':phone' => $phone,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
        ];

        $this->execute($sql, $data);

        return [
            'code' => 201,
            'msg' => $role === 'teacher' ? 'Thêm mới tài khoản giáo viên thành công!' : 'Thêm mới tài khoản thành công!',
        ];
    }

    public function updateUser($id, $data)
    {
        $sql = "UPDATE users 
                SET user_code = :user_code, fullname = :fullname, phone = :phone, email = :email, password = :password, role = :role 
                WHERE id = :id";

        $data['id'] = $id;

        $result = $this->execute($sql, $data);

        // Xác định vai trò người dùng
        $roleMessage = ($data['role'] === 'teacher') ? 'giáo viên' : 'người dùng';

        // Kiểm tra số hàng bị ảnh hưởng để xác định xem cập nhật có thành công hay không
        return $result > 0
            ? ['code' => 200, 'msg' => "Cập nhật thông tin $roleMessage thành công!"]
            : ['code' => 400, 'msg' => "Cập nhật thông tin $roleMessage không thành công!"];
    }

    public function deleteUser($id, $role = 'user')
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $result = $this->execute($sql, ['id' => $id]);
        $msg = $role === 'user' ? 'người dùng' : 'giáo viên';
        return $result > 0
            ? ['code' => 200, 'msg' => "Xóa tài khoản $msg thành công!"]
            : ['code' => 400, 'msg' => "Xóa tài khoản $msg không thành công!"];
    }

    public function importUsers($users)
    {
        $failedUsers = [];

        foreach ($users as $user) {
            $username = $user['Username'];
            $usercode = $user['Usercode'];
            $fullname = $user['Fullname'];
            $phone = $user['Phone'];
            $email = $user['Email'];
            $password = $user['Password'];

            if ($this->getUserByUsername($username)) {
                $username .= rand(100, 999);
            }

            $result = $this->createUser($username, $usercode, $fullname, $phone, $email, $password, 'user');

            if ($result['code'] !== 201) {
                $failedUsers[] = [
                    'username' => $username,
                    'fullname' => $fullname,
                    'reason' => $result['msg'],
                ];
            }
        }

        return [
            'code' => count($failedUsers) === 0 ? 201 : 500,
            'msg' => count($failedUsers) === 0 ? 'Nhập danh sách thành viên thành công!' : 'Nhập danh sách thành viên thất bại!',
            'status' => count($failedUsers) === 0 ? 'success' : 'partial_success',
            'failedUsers' => $failedUsers,
        ];
    }

    private function getUserByCode($usercode)
    {
        $sql = "SELECT * FROM users WHERE user_code = :usercode";
        return $this->fetch($sql, ['usercode' => $usercode]);
    }

    private function getUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->fetch($sql, ['email' => $email]);
    }
}
