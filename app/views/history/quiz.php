<div class="row justify-content-end mb-4">
    <div class="col col-md-4 align-self-end text-end">
        <div class="input-group">
            <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm theo môn học hoặc bài học...">
            <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                <i class="fa fa-search fw-bold"></i> Search
            </button>
        </div>
    </div>

</div>



<table class="table table-stripped table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Thumbnail</th>
            <th scope="col">Môn học</th>
            <th scope="col">Đề thi</th>
            <th scope="col">Ngày bắt đầu</th>
            <th scope="col">Ngày kết thúc</th>
            <th scope="col">Ngày làm bài</th>
            <th scope="col">Thời gian làm (phút)</th>
            <th scope="col">Trả lời đúng (câu)</th>
            <th scope="col">Điểm</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody id="tblData">

    </tbody>
</table>


<!-- Pagination -->
<nav aria-label="Page navigation example">
    <ul class="pagination pagination-sm">
        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
        <li class="page-item"><a class="page-link" href="#">Next</a></li>
    </ul>
</nav>

<div class="modal" tabindex="-1" id="modal" style="z-index: 9999 !important;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                <div class="containter" id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
const $table = $('#tblData'),
    pageSize = 10,
    $pagination = $('.pagination'),
    $modal = $('#modal'),
    $modalContent = $('#modalContent');
let page = 1;
var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
$(document).ready(function() {
    LoadHistoryExams();
    $pagination.on('click', '.page-link', function(e) {
        e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
        currentPage = parseInt($(this).text()); // Lấy giá trị data-page

        if (currentPage !== undefined) {
            if (currentPage !== 0 && currentPage !== pageNumber) {
                page = currentPage;
                LoadHistoryExams();
            } else {
                if (currentPage === 0 && page > 1) {
                    page--;
                    LoadHistoryExams();
                } else if (currentPage == pageNumber && page < pageNumber) {
                    page++;
                    LoadHistoryExams();
                }
            }
            console.log({
                page
            });

        }
    });
})

const ViewResult = function(id) {
    $modalContent.empty();
    $.ajax({
        url: '<?php echo BASE_URL; ?>/history/quiz_detail',
        type: 'get',
        dataType: 'json',
        data: {
            id
        },
        success: function(response) {
            const {
                code,
                msg,
                data
            } = response;
            $modal.modal('show');
            let idx = (page - 1) * pageSize;
            data.forEach(q => {
                let options = q.options;
                $modalContent.append(`
                            <div class="container mt-5">
                                <div class="question-card p-4 shadow-sm bg-light rounded">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="text-secondary me-3">Câu số ${++idx<10?'0'+idx:idx}:</span>
                                        <h5 class="card-title mb-0">${formatDisplay(q.question_text)}</h5>
                                    </div>
                                    <p class="card-text">
                                        Điểm: <span class="fw-bold text-danger">${q.mark}</span>
                                        ${q.correct_option == q.user_answer ? '<i class="fa fa-check text-success" aria-hidden="true"></i> Đúng' : `<i class="fa fa-times text-danger" aria-hidden="true"></i> Sai${q.user_answer==-1?' <b class = "fw-bold text-secondary">(Không trả lời)</b>':''}`}
                                    </p>
                                    <ul class="list-group">
                                        <li class="list-group-item answer ${options.correct_option == 1 ? 'correct' : ''} ${q.user_answer == 1 ? 'text-warning fw-bold' : ''}">${formatDisplay(options.option_1)}</li>
                                        <li class="list-group-item answer ${options.correct_option == 2 ? 'correct' : ''} ${q.user_answer == 2 ? 'text-warning fw-bold' : ''}">${formatDisplay(options.option_2)}</li>
                                        <li class="list-group-item answer ${options.correct_option == 3 ? 'correct' : ''} ${q.user_answer == 3 ? 'text-warning fw-bold' : ''}">${formatDisplay(options.option_3)}</li>
                                        <li class="list-group-item answer ${options.correct_option == 4 ? 'correct' : ''} ${q.user_answer == 4 ? 'text-warning fw-bold' : ''}">${formatDisplay(options.option_4)}</li>
                                    </ul>
                                </div>
                            </div>
                        `);
            })
        },
        error: function(err) {
            console.log(err);

        }
    })
}

const LoadHistoryExams = function() {
    $table.empty();
    $pagination.empty();
    $.ajax({
        url: '<?php echo BASE_URL; ?>/history/showquiz',
        type: 'get',
        dataType: 'json',
        data: {
            page,
            pageSize,
            keyword: $('#txtKeyword').val().trim()
        },
        success: function(response) {
            const {
                history,
                totalPages,
                currentPage,
                hasNext,
                hasPrev
            } = response;

            console.log(history);


            let idx = (page - 1) * pageSize;
            history.forEach(h => {
                $table.append(`
                    <tr class = "align-middle">
                        <td>${++idx<10?'0'+idx:idx}</td>
                        <td>
                            <img src = "<?php echo BASE_URL; ?>${h.thumbnail.trim().length > 0 ? h.thumbnail : '/public/assets/images/no_image.jpg'}" class = "img-thumbnail" style="width:200; height:120px;"/>
                        </td>
                        <td>${h.subject_name}</td>
                        <td class = "fw-bold text-info">${h.exam_title}</td>
                        <td>${h.begin_date}</td>
                        <td>${h.end_date}</td>
                        <td>${h.quiz_date}</td>
                        <td class = "text-center">${convertSecondsToHMS(h.spent_time)}</td>
                        <td class = "text-center fw-bold"><span class ="text-success">${h.fcorrect_answers}</span>/${h.number_of_questions}</td>
                        <td class = "fw-bold text-center"><span class = "text-danger">${h.got_marks}</span>/${h.marks}</td>
                        <td><a href="javascript:void(0)" onClick="ViewResult(${h.quiz_result_id})"><i class="fa fa-eye text-info"></i></a></td>
                    </tr>
                `);


            })
            $pagination.append(
                `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#">Previous</a></li>`
            );
            for (i = 1; i <= totalPages; i++) {
                $pagination.append(
                    ` <li class="page-item ${i == page ? 'active' : ''}"><a class="page-link " href="#">${i}</a></li>`
                )
            }
            $pagination.append(
                `<li class="page-item  ${page === totalPages ? 'disabled' : ''}"><a class="page-link" href="#">Next</a></li>`
            );



        },
        error: function(err) {
            console.log(err);

        }
    })
}


const convertSecondsToHMS = function(seconds) {
    // Tính toán giờ, phút và giây
    var hours = Math.floor(seconds / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var secs = seconds % 60;

    // Định dạng theo định dạng giờ:phút:giây
    return (
        (hours < 10 ? "0" : "") + hours + ":" +
        (minutes < 10 ? "0" : "") + minutes + ":" +
        (secs < 10 ? "0" : "") + secs
    );
}

const formatDisplay = function(inputHTML) {
    // Giữ nguyên nội dung HTML của q.question mà không render
    var formattedQuestionText = $('<div>').text(inputHTML).html();
    return formattedQuestionText
        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
        .replace(/<p>\s+/g, function(match) {
            return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
        });
}
</script>

<style>
.question-card {
    border: 1px solid #dee2e6;
    border-radius: .25rem;
    margin-bottom: 1rem;
    padding: 1.25rem;
}

.answer {
    cursor: pointer;
}

.answer.correct {
    background-color: #d4edda;
    /* bg-success */
    color: #155724;
}

.answer.incorrect {
    background-color: #f8d7da;
    /* bg-danger */
    color: #721c24;
}
</style>