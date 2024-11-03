<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill the blank</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/jquery.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/sweetalert2/sweetalert2@11.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.js"></script>
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container .form-group {
            margin-bottom: 15px;
        }

        .login-container .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .login-container .form-group input {
            width: 100%;
            padding: 10px;
        }

        .login-container .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container .form-group button:hover {
            background-color: #45a049;
        }

        .login-container .logo {
            display: block;
            margin: 0 auto 20px auto;
            height: 30px;
            width: auto;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#btnSubmit').click(function (e) {
                e.preventDefault();
                var username = $('#username').val();
                var password = $('#password').val();

                $.ajax({
                    url: '<?php echo BASE_URL; ?>/userauth/login', // URL của phương thức login trong AuthController
                    method: 'POST',
                    data: {
                        username: username,
                        password: password,
                        role: 'user'
                    },
                    dataType: 'json', // Đảm bảo rằng AJAX mong đợi phản hồi JSON
                    success: function (response) {
                        const { code, message, redirect } = response;
                        if (code == 200) {
                            Swal.fire({
                                title: "SUCCESSFULLY",
                                text: message,
                                icon: "success"
                            }).then(_ => {                                
                                window.location.href = '<?php echo BASE_URL; ?>';
                            });

                        } else {
                            $.toast({
                                heading: `Error: ${code}`,
                                text: message || 'Invalid username or password',
                                showHideTransition: 'fade',
                                icon: 'error'
                            });
                        }
                    },
                    error: function (err) {
                        console.log(err);
                        // $.toast({
                        //     heading: 'Error',
                        //     text: 'Something went wrong. Please try again.',
                        //     showHideTransition: 'fade',
                        //     icon: 'error'
                        // });
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="login-container">
        <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" class="logo" alt="Logo">
        <div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button id="btnSubmit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>