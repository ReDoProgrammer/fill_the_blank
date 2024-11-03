const BASE_URL = '<?php echo BASE_URL; ?>';
$(document).ready(function(){
    // console.log({BASE_URL});
})
function ajaxRequest(url, method, data, dataType, successCallback, errorCallback) {

    $.ajax({
        url: '<?php echo BASE_URL; ?>' + url, // URL của yêu cầu AJAX
        method: method, // Phương thức HTTP (GET, POST, v.v.)
        data: data, // Dữ liệu gửi đi (có thể là object hoặc string)
        dataType: dataType, // Loại dữ liệu mà AJAX mong đợi phản hồi (ví dụ: 'json')
        success: function(response) {
            // Hàm callback khi yêu cầu thành công
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Hàm callback khi yêu cầu gặp lỗi
            if (typeof errorCallback === 'function') {
                errorCallback(jqXHR, textStatus, errorThrown);
            }
        }
    });
}

function capitalizeWords(str) {
    return str.replace(/\b\w/g, function (char) {
        return char.toUpperCase();
    });
}
