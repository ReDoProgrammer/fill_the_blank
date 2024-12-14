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
            <th scope="col">Ngày làm bài</th>
            <th scope="col">Môn học</th>
            <th scope="col">Bài học</th>
            <th scope="col">Số câu hỏi</th>
            <th scope="col">Số câu hỏi đã trả lời</th>
            <th scope="col">Số câu trả lời đúng</th>
            <th scope="col">Số đáp án</th>
            <th scope="col">Số đáp án đúng</th>
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

<div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                
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

const $search = $('#btnSearch'),
    $keyword = $('#txtKeyword'),
    $table = $('#tblData');
$modal = $('#modal'), $content = $('#modalContent'), $pagination = $('.pagination');
$(document).ready(function() {
    LoadOwnHistory();

    $pagination.on('click', '.page-link', function(event) {
        event.preventDefault();

        var text = $(this).text();
        if (isNumeric(text)) {
            page = parseInt(text);
        } else {
            page = text === 'Next' ? page + 1 : page - 1;
        }
        LoadOwnHistory();
    });
})

$search.click(function() {
    page = 1;
    keyword = $keyword.val().trim();
    LoadOwnHistory();
})

function LoadOwnHistory() {
    $.ajax({
        url: '<?php echo BASE_URL; ?>/history/show',
        type: 'GET',
        dataType: 'json',
        data: {
            page,
            pageSize,
            keyword
        },
        success: function(response) {
            const {
                currentPage,
                hasNext,
                hasPrev,
                history,
                totalPages
            } = response;
            let idx = (page - 1) * pageSize;
            $table.empty();
            $pagination.empty();

            history.forEach(h => {
                $table.append(`
                        <tr>
                            <td>${++idx}</td>
                            <td>${h.exam_date}</td>
                            <td class="fw-bold text-danger">${h.subject_name}</td>
                            <td class="text-primary">${h.lession_name}</td>
                            <td class="text-center fw-bold">${h.totalQuestions}</td>
                            <td class="text-center fw-bold">${h.totalAnswers}/${h.totalQuestions}</td>
                            <td class="text-center fw-bold">${h.correctQuestions}/${h.totalAnswers}</td>
                            <td class="text-center fw-bold">${h.blanksNumbers}</td>
                            <td class="text-center fw-bold">${h.correct_answers}/${h.blanksNumbers}</td>
                            <td>
                               <a href="#" onclick="fetchDetail(${h.id}); return false;"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    `)
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

function fetchDetail(id) {
    $.ajax({
        url: '<?php echo BASE_URL; ?>/history/detail',
        type: 'get',
        dataType: 'json',
        data: {
            id
        },
        success: function(response) {
            console.log(response);
            const {
                code,
                msg,
                data
            } = response;
            renderExamDetails(data);
        },
        error: function(err) {
            console.log(err);
        }
    })
}

function renderExamDetails(data) {
    let idx = 1;
    $content.empty();
    data.forEach(function(item) {
        var questionText = item.question_text;
        var answers = item.answers;

        // Sort answers by position to ensure correct replacement order
        answers.sort(function(a, b) {
            return a.position - b.position;
        });       

        answers.forEach(function(answer) {
            var userAnswer = answer.user_answer;
            var correctAnswer = answer.correct_answer;

            // Tạo input cho user answer với class và style thích hợp
            var inputClass = userAnswer === correctAnswer ? 'text-success' : 'text-danger';
            var inputStyle =
                'border: none; border-bottom: 1px solid #000;'; // Không có border nhưng có gạch chân
            var replacement = '';
            if (userAnswer.trim().length == 0) {
                console.log(1);

                replacement =
                    `<label><span class = "blank fw-bold bg-danger text-secondary" style="${inputStyle}">&nbsp;&nbsp;&nbsp;</span></label>`;
            } else {
                replacement =
                    `<input type="text" class="blank ${inputClass} fw-bold" value="${userAnswer}" style="${inputStyle}" readonly />`;
            }


            console.log({
                userAnswer,
                correctAnswer
            });

            if (userAnswer !== correctAnswer) {
                replacement +=
                    ` [<span class="text-success fw-bold">${formatDisplay(correctAnswer.replace(/</g, '&lt;').replace(/>/g, '&gt;'))}</span>]`;
                console.log(correctAnswer);
            }

            // Thay thế chuỗi ___ bằng giá trị của replacement
            var regex = /___/;
            questionText = questionText.replace(regex, replacement);
        });

        // Append the formatted question to the container
        var questionHtml = `<div class="row mb-1">
                                <div class="col-md-12 form-group">
                                    <label class="fw-bold">Câu thứ ${idx++}:</label>
                                    <div class="mt-2 p-3">${formatDisplay(questionText)}</div>
                                </div>
                            </div>`;
        $content.append(questionHtml);
    });
    $modal.modal('show');
}

function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

const formatDisplay = function(inputHTML) {

    return inputHTML
        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
        .replace(/<p>\s+/g, function(match) {
            return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
        });
}
</script>