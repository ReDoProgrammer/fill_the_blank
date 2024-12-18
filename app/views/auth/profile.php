<div class="card">
    <div class="card-header">
        Thông tin tài khoản
    </div>
    <div class="card-body">
        <div class="containter" id="profile" data-id="<?php echo $profile['id']; ?>" data-teaching="<?php echo $profile['teaching_id'];?>">
            <div class="row mb-3">
                <div class="col-md-3 form-group">
                    <label for="">Username:</label>
                    <input type="text" name="username" id="username" value="<?php echo $profile['username']; ?>"
                        readonly class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Mật khẩu hiện tại:</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Mật khẩu mới:</label>
                    <input type="password" name="new_password" id="new_password" class="form-control"
                        placeholder="Mật khẩu mới">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Xác nhận mật khẩu mới:</label>
                    <input type="password" name="confirm_new_password" placeholder="Xác nhận mật khẩu mới"
                        id="confirm_new_password" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 form-group">
                    <label for="">Mã học viên</label>
                    <input type="text" class="form-control" readonly id="txtUserCode" value="<?php echo $profile['user_code']; ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Họ tên:</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo $profile['fullname']; ?>"
                        placeholder="Họ và tên" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Điện thoại:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $profile['phone']; ?>"
                        placeholder="Số điện thoại" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo $profile['email']; ?>"
                        placeholder="Địa chỉ email" class="form-control">
                </div>

            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" id="btnSubmit" class="btn btn-success">Lưu thông tin</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#btnSubmit').click(function () {
            let id = parseInt($('#profile').attr('data-id'));
            let username = $('#username').val();
            let password = $('#password').val();
            let new_password = $('#new_password').val();
            let confirm_new_password = $('#confirm_new_password').val();
            let fullname = $('#fullname').val();
            let phone = $('#phone').val();
            let email = $('#email').val();
            console.log({ username, password, new_password, confirm_new_password, fullname, phone, email });
            if (password.trim().length === 0) {
                Swal.fire({
                    title: "Ràng buộc dữ liệu",
                    text: "Vui lòng nhập mật khẩu hiện tại",
                    icon: "error"
                })
                return;
            }
            if (new_password.trim().lenth === 0 || confirm_new_password.trim().length === 0) {
                Swal.fire({
                    title: "Ràng buộc dữ liệu",
                    text: "Vui lòng nhập mật khẩu mới",
                    icon: "error"
                })
                return;
            }
            if (new_password != confirm_new_password) {
                Swal.fire({
                    title: "Ràng buộc dữ liệu",
                    text: "Hai lần nhập mật khẩu mới không khớp",
                    icon: "error"
                })
                return;
            }

            $.ajax({
                url: '<?php echo BASE_URL; ?>/userauth/update',
                type: 'POST',
                dataType: 'json',
                data: {
                    id, username, fullname, password, new_password, confirm_new_password, phone, email,
                    user_code: $('#txtUserCode').val(),teaching_id:parseInt($('#profile').attr('data-teaching'))
                },
                success: function (response) {
                    const { code, msg } = response;
                    if (code === 200) {
                        Swal.fire({
                            icon: "success",
                            title: "SUCCESSFULLY",
                            text: `${msg} Bạn sẽ được điều hướng đăng xuất ra khỏi tài khoản để cập nhật lại thông tin!`
                        }).then(_=>{
                            window.location.href = '<?php echo BASE_URL;?>/userauth/logout';
                        });
                    } else {
                        Swal.fire({
                            title: `Lỗi: ${code}`,
                            text: msg,
                            icon: "error"
                        })
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            })
        })
    })
</script>