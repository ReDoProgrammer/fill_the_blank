
$(document).ready(function () {
    $('#btnSubmit').click(function (e) {
        e.preventDefault();
        var username = $('#username').val();
        var password = $('#password').val();      

        $.ajax({
            url: '<?php echo BASE_URL; ?>/auth/login', // URL của phương thức login trong AuthController
            method: 'POST',
            data: {
                username: username,
                password: password,
                role: 'user'
            },
            dataType: 'json', // Đảm bảo rằng AJAX mong đợi phản hồi JSON
            success: function (response) {
                console.log(response);
                const { code, message, redirect } = response;
                if (code == 200) {
                    window.location.href = response.redirect;
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
                $.toast({
                    heading: 'Error',
                    text: 'Something went wrong. Please try again.',
                    showHideTransition: 'fade',
                    icon: 'error'
                });
            }
        });
    });
});
