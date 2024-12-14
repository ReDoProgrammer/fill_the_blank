<div class="card">
    <div class="card-header" id="cardTitle">
        HỌC VIÊN KHÔNG TỒN TẠI
    </div>
    <div class="card-body">
        <!-- statistic Table -->
        <table class="table table-bordered table-striped table-hover mt-3" id="table">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Môn học</th>
                <th scope="col">Bài học</th>
                <th scope="col">Thời gian</th>
                <th scope="col">Số câu hỏi</th>
                <th scope="col" class="text-center">Số đáp án</th>
                <th scope="col" class="text-center">Trả lời đúng</th>
                <th scope="col" class="text-center"></th>
            </tr>
        </table>
    </div>
</div>

<script>
    var page = 1, pageSize = 10, keyword = '';
    $(document).ready(function() {
        var url = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var u = parseInt(urlParams.get('u'));
        var c = urlParams.get('c');

        GetUserById(u, c);

    })

    function GetUserById(id, code) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/user/detail',
            type: 'GET',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {
                const {
                    user
                } = response;
                if (user.user_code == code) {
                    $('#cardTitle').html(`Lịch sử ôn tập của học viên: ${user.user_code}. Họ tên: <span class = "fw-bold text-info">${user.fullname}</span>. Lớp: <span class="text-warning">${user.name}</span>`);
                    LoadPesionalPractice(id);
                }

            },
            error: function(err) {
                console.log(err.responseText);

            }
        })
    }

    function LoadPesionalPractice(userId){
        console.log({userId});
        $.ajax({
            url:`<?php echo BASE_URL; ?>/teacher/user/persionalPractice`,
            type:'GET',
            dataType:'json',
            data:{
                userId,
                page,pageSize,
                keyword
            },
            success:function(respsonse){
                console.log(respsonse);
                
            },
            error:function(err){
                console.log(err.responseText);
                
            }
        })
        
    }
</script>