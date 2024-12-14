<div class="card">
    <div class="card-header" id="cardTitle">
        HỌC VIÊN KHÔNG TỒN TẠI
    </div>
    <div class="card-body">
        <!-- statistic Table -->
        <table class="table table-bordered table-striped table-hover mt-3" id="table">
            <tr>
                <th rowspan="2" class="align-middle">STT</th>
                <th rowspan="2" class="align-middle">Môn học</th>
                <th rowspan="2" class="align-middle">Bài học</th>
                <th rowspan="2" class="align-middle">Thời gian thi</th>
                <th rowspan="2" class="align-middle">Số lần làm bài</th>
                <th colspan="3" class="text-center">Chỗ trống</th>
                <th colspan="3" class="text-center">Câu hỏi</th>
                <th rowspan="2"></th>
            </tr>
            <tr>
                <th>Tổng số</th>
                <th>Điền đúng</th>
                <th>Tỉ lệ (%)</th>
                <th>Tổng số</th>
                <th>Trả lời đúng</th>
                <th>Tỉ lệ (%)</th>
            </tr>
            <tbody id="tblData"></tbody>
        </table>
    </div>
</div>
<!-- Pagination -->
<nav aria-label="Page navigation example">
    <ul class="pagination pagination-sm">
        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
        <li class="page-item"><a class="page-link" href="#">Next</a></li>
    </ul>
</nav>

<div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài làm ôn tập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                <p class = "fw-bold">
                    <span class = "fw-bold text-decoration-underline">Chú ý: </span><span class="text-success">Câu trả lời có màu xanh</span> tương ứng câu trả lời đúng. Ngược lại: <span class="text-danger">Câu trả lời sai của bạn có màu đỏ</span> [<span class="text-success">Còn đây là đáp án đúng của chỗ trống</span>].
                    <br/>
                    <label>Để trống đáp án <span class = "blank fw-bold bg-danger text-secondary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> sẽ được tính là câu trả lời sai.</label>
            </p>
                <div class="containter p-5" id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
       var page = 1,
        pageSize = 10,
        keyword = '';
    const $table = $('#tblData'),
        $modal = $('#modal'),
        $content = $('#modalContent'),
        $pagination = $('.pagination');
    $(document).ready(function() {
        var url = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var u = parseInt(urlParams.get('u'));
        var c = urlParams.get('c');

        GetUserById(u, c);
        $pagination.on('click', '.page-link', function(event) {
            event.preventDefault();

            var text = $(this).text();
            if (isNumeric(text)) {
                page = parseInt(text);
            } else {
                page = text === 'Next' ? page + 1 : page - 1;
            }
            LoadOwnQuizHistory();
        });

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
                    LoadPersonalQuizHistory(id);
                }

            },
            error: function(err) {
                console.log(err.responseText);

            }
        })
    }

    function LoadPersonalQuizHistory(userId){
        $table.empty();
        $pagination.empty();
        $.ajax({
            url: `<?php echo BASE_URL; ?>/teacher/user/persionalQuiz`,
            type: 'GET',
            dataType: 'json',
            data: {
                userId,
                page,
                pageSize,
                keyword
            },
            success: function(response) {
                console.log(response);
                
                // const {
                //     code,
                //     msg,
                //     result
                // } = response;
                // if (code === 200) {
                //     let idx = (page - 1) * pageSize;
                //     const {
                //         history,
                //         totalPages
                //     } = result;
                //     history.forEach(h => {
                //         $table.append(`
                //             <tr>
                //                 <td>${++idx}</td>
                //                 <td class = "text-warning fw-bold">${h.subject_name}</td>
                //                 <td class = "text-info fw-bold">${h.lession_name}</td>
                //                 <td class = "text-center">${h.exam_date}</td>
                //                 <td class = "text-danger text-center fw-bold">${h.attempt_number}</td>
                //                 <td class = "text-center">${h.blanksNumbers}</td>
                //                 <td class = "text-center">${h.correct_answers}</td>
                //                 <td class = "text-center text-danger">${h.correct_answers_percentage}</td>
                //                 <td class = "text-center">${h.totalQuestions}</td>
                //                 <td class = "text-center">${h.correctQuestions}</td>
                //                 <td class = "text-center text-danger">${h.correct_questions_percentage}</td>
                //                 <td>
                //                <a href="#" onclick="fetchDetail(${h.id}); return false;"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                //             </td>

                //             </tr>
                //         `);
                //     })

                //     $pagination.append(
                //         `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#">Previous</a></li>`
                //     );
                //     for (i = 1; i <= totalPages; i++) {
                //         $pagination.append(
                //             ` <li class="page-item ${i == page ? 'active' : ''}"><a class="page-link " href="#">${i}</a></li>`
                //         )
                //     }
                //     $pagination.append(
                //         `<li class="page-item  ${page === totalPages ? 'disabled' : ''}"><a class="page-link" href="#">Next</a></li>`
                //     );
                // }
            },
            error: function(err) {
                console.log(err.responseText);

            }
        })
        
    }
</script>