<div class="card">
    <div class="card-header" id="cardTitle">
        HỌC VIÊN KHÔNG TỒN TẠI
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover mt-3">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Ngày tham gia thi</th>
                    <th scope="col">Môn học</th>
                    <th scope="col">Lớp</th>
                    <th scope="col" class="text-center">Số câu hỏi</th>
                    <th scope="col" class="text-center">Thời gian thi</th>
                    <th scope="col" class="text-center">Trả lời đúng (câu)</th>
                    <th scope="col" class="text-center">Điểm</th>
                </tr>
            </thead>
            <tbody id="tblHistory"></tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
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
            data: { id },
            success: function (response) {
                console.log(response);
                const { user } = response;
                if (user.user_code == code) {
                    $('#cardTitle').html(`Lịch sử thi của học viên: ${user.user_code}. Họ tên: <span class = "fw-bold text-info">${user.fullname}</span>. Lớp: <span class="text-warning">${user.name}</span>`);
                    LoadQuizHistory(id);
                }
            },
            error: function (err) {
                console.log(err.responseText);

            }
        })
    }

    function LoadQuizHistory(id) {

    }
</script>