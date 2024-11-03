var keyword = '', page = 1, pageSize = 10;
const $btnSearch = $('#btnSearch'), $keyword = $('#txtKeyword'), $table = $('#tblData'), $pagination  = $('#pagination');
$(document).ready(function(){
    LoadData();
})

function LoadData(){
    $.ajax({
        url: '<?php echo BASE_URL; ?>/admin/user/search', // URL của phương thức search trong UserController
        method: 'GET',
        data: {
            keyword,
            page,
            pageSize
        },
        dataType: 'json',
        success: function (response) {
            console.log(response);
        },
        error: function (err) {
            console.log(err.responseText);
        }
    });
    
}