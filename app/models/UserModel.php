<?php
// models/UserModel.php

require_once 'app/core/Model.php';

class UserModel extends Model
{

    // app/models/UserModel.php

    // app/models/UserModel.php

    public function getAllUsers($keyword, $page, $pageSize, $role = 'user')
    {
        // Tính toán OFFSET
        $offset = ($page - 1) * $pageSize;
    
        // Câu lệnh SQL để lấy dữ liệu người dùng theo phân trang
        $sql = "SELECT * FROM users
                WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                  AND role = :role
                ORDER BY username, fullname
                LIMIT $offset, $pageSize"; // Chèn trực tiếp OFFSET và LIMIT
    
        // Câu lệnh SQL để lấy tổng số bản ghi
        $countSql = "SELECT COUNT(*) as total FROM users
                     WHERE (username LIKE :keyword OR phone LIKE :keyword OR email LIKE :keyword OR fullname LIKE :keyword)
                       AND role = :role";
    
        // Thay đổi các tham số cho PDO
        $params = [
            ':keyword' => "%$keyword%",
            ':role' => $role
        ];
    
        // Lấy dữ liệu người dùng
        $users = $this->fetchAll($sql, $params);
    
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

    public function createUser($username,$usercode, $fullname, $phone, $email, $password)
    {
        // Kiểm tra xem username đã tồn tại trong cơ sở dữ liệu hay chưa
        $existingUser = $this->getUserByUsername($username);
        if ($existingUser) {
            // Trả về thông báo lỗi nếu username đã tồn tại
            return [
                'code' => 409,
                'msg' => 'Username này đã tồn tại trong hệ thống!'
            ];
        }

        // Mã hóa mật khẩu
        $hashedPassword = md5($password);

        // Chuẩn bị câu lệnh SQL để chèn dữ liệu
        $sql = "INSERT INTO users (username,user_code, fullname, phone, email, password) VALUES (:username,:user_code, :fullname, :phone, :email, :password)";

        // Thực thi câu lệnh SQL với dữ liệu
        $data = [
            ':username' => $username,
            ':user_code'=>$usercode,
            ':fullname' => $fullname,
            ':phone' => $phone,
            ':email' => $email,
            ':password' => $hashedPassword
        ];

        $this->execute($sql, $data);

        // Trả về phản hồi thành công
        return [
            'code' => 201,
            'msg' => 'Thêm mới tài khoản thành công!!'
        ];
    }

    public function updateUser($id, $data)
    {
        // Thêm ID vào dữ liệu cần cập nhật
        $data['id'] = $id;

        // Câu lệnh SQL để cập nhật thông tin người dùng
        $sql = "UPDATE users SET user_code = :user_code,fullname = :fullname, phone=:phone, email = :email, password = :password WHERE id = :id";

        // Thực thi câu lệnh SQL
        $result = $this->execute($sql, $data);

        // Kiểm tra số hàng bị ảnh hưởng để xác định xem cập nhật có thành công hay không
        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Cập nhật thông tin người dùng thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Cập nhật thông tin người dùng không thành công!'
            ];
        }
    }


    public function deleteUser($id)
    {
        // Chuẩn bị câu lệnh SQL để xóa người dùng
        $sql = "DELETE FROM users WHERE id = :id";

        // Thực thi câu lệnh SQL
        $result = $this->execute($sql, ['id' => $id]);

        // Kiểm tra số hàng bị ảnh hưởng để xác định xem xóa có thành công hay không
        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Xóa tài khoản thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Xóa tài khoản không thành công!'
            ];
        }
    }


    public function updateProfile($id, $data)
    {
        // Lấy thông tin người dùng hiện tại
        $currentUser = $this->getUserById($id);
        if (!$currentUser) {
            return [
                'code' => 404,
                'msg' => 'Không tìm thấy người dùng!'
            ];
        }

        // Kiểm tra mật khẩu hiện tại
        if (md5($data['password']) !== $currentUser['password']) {
            return [
                'code' => 401,
                'msg' => 'Mật khẩu hiện tại không đúng!'
            ];
        }

        // Cập nhật thông tin người dùng
        $updateData = [
            'fullname' => $data['fullname'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'id' => $id
        ];



        $updateData['password'] = md5($data['new_password']);
        $sql = "UPDATE users SET fullname = :fullname, phone = :phone, email = :email, password = :password WHERE id = :id";

        // Thực thi câu lệnh SQL
        $result = $this->execute($sql, $updateData);

        // Kiểm tra số hàng bị ảnh hưởng để xác định xem cập nhật có thành công hay không
        if ($result > 0) {
            return [
                'code' => 200,
                'msg' => 'Cập nhật thông tin người dùng thành công!'
            ];
        } else {
            return [
                'code' => 400,
                'msg' => 'Cập nhật thông tin người dùng không thành công!'
            ];
        }
    }

    public function getAccountStatisticsGroupedByUser($keyword = '', $from_date, $to_date)
    {
        // Câu lệnh SQL để lấy dữ liệu thống kê
        $sql = "
        SELECT 
            user_id,
            username,
            user_code,
            fullname,
            COUNT(*) AS total_exams,
            SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) AS exams_in_month,
            SUM(CASE WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1) THEN 1 ELSE 0 END) AS exams_in_week,
            MAX(correct_questions) AS best_exam_correct_questions,
            MAX(total_questions_in_lession) AS best_exam_total_questions,
            MAX(DATE_FORMAT(created_at, '%d/%m/%Y %H:%i')) AS best_exam_date
        FROM (
            SELECT 
                exam_results.user_id,
                users.username,
                users.user_code,
                users.fullname,
                exam_results.id AS exam_id,
                (SELECT COUNT(*)
                FROM (
                    SELECT exam_answers.exam_result_id, exam_answers.question_id
                    FROM exam_answers
                    JOIN question_blanks ON exam_answers.question_id = question_blanks.question_id
                    WHERE exam_answers.answer = question_blanks.blank_text
                    GROUP BY exam_answers.exam_result_id, exam_answers.question_id
                    HAVING COUNT(*) = (SELECT COUNT(*) FROM question_blanks WHERE question_id = exam_answers.question_id)
                ) AS correct_questions_subquery
                WHERE correct_questions_subquery.exam_result_id = exam_results.id
                ) AS correct_questions,
                (SELECT COUNT(*)
                FROM questions
                WHERE questions.lession_id = exam_results.lession_id) AS total_questions_in_lession,
                exam_results.created_at
            FROM exam_results
            JOIN exam_answers ON exam_results.id = exam_answers.exam_result_id  
            JOIN question_blanks ON exam_answers.question_id = question_blanks.question_id
            JOIN users ON exam_results.user_id = users.id
            JOIN lessions ON exam_results.lession_id = lessions.id
            JOIN subjects ON lessions.subject_id = subjects.id
            WHERE exam_results.created_at BETWEEN :from_date AND :to_date
            AND (
                users.username LIKE :keyword OR 
                users.fullname LIKE :keyword OR 
                lessions.name LIKE :keyword OR 
                subjects.name LIKE :keyword
            )
            GROUP BY exam_results.id, exam_results.user_id, users.username, users.fullname, exam_results.created_at
            ORDER BY exam_results.user_id, exam_results.created_at DESC
        ) AS subquery
        GROUP BY user_id
        ORDER BY user_id";

        // Các tham số truyền vào truy vấn SQL
        $params = [
            ':from_date' => $from_date,
            ':to_date' => $to_date,
            ':keyword' => '%' . $keyword . '%',
        ];

        // Thực thi truy vấn và trả về kết quả
        return $this->fetchAll($sql, $params);
    }

    public function importUsers($users)
    {
        $failedUsers = []; // Mảng để chứa các tài khoản không insert thành công

        foreach ($users as $user) {
            // Lấy thông tin người dùng từ mảng $user
            $username = $user['Username'];
            $usercode = $user['Usercode'];
            $fullname = $user['Fullname'];
            $phone = $user['Phone'];
            $email = $user['Email'];
            $password = $user['Password'];

            // Kiểm tra xem username đã tồn tại hay chưa
            $existingUser = $this->getUserByUsername($username);
            if ($existingUser) {
                // Tạo số ngẫu nhiên có 3 chữ số
                $randomNumber = rand(100, 999);
                // Nối số ngẫu nhiên vào username
                $username .= $randomNumber;
            }

            // Thực hiện tạo người dùng mới
            $result = $this->createUser($username,$usercode, $fullname, $phone, $email, $password);

            // Kiểm tra kết quả và xử lý nếu không thành công
            if ($result['code'] !== 201) {
                // Thêm thông tin tài khoản vào mảng failedUsers
                $failedUsers[] = [
                    'username' => $username,
                    'fullname' => $fullname,
                    'reason' => $result['msg']
                ];
            }
        }

        // Trả về mảng chứa các tài khoản không được insert thành công dạng JSON
        return json_encode([
            'code'=> count($failedUsers) === 0?201:500,
            'msg'=> count($failedUsers) === 0?'Nhập danh sách thành viên thành công!':'Nhập danh sách thành viên thất bại',
            'status' => count($failedUsers) === 0 ? 'success' : 'partial_success',
            'failedUsers' => $failedUsers
        ]);
    }



}
