<div class="card text-white bg-success">
    <h5 class="card-header mb-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fa fa-book text-white" aria-hidden="true"></i>
            Danh sách đề thi trắc nghiệm môn <span class="text-warning"><?php echo $subject; ?></span>
        </span>
    </h5>

    <div class="card-body bg-white text-dark">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/jquery-ui/jquery-ui.css">
        <script src="<?php echo BASE_URL; ?>/public/assets/plugins/jquery-ui/jquery-ui.min.js"></script>

        <div class="container mb-5 mt-3">
            <div class="row" id="exams"></div>
        </div>
    </div>
</div>

<script>
$exams = $('#exams');
$(document).ready(function() {
    let subject = getUrlParameter('s');
    let arr = subject.split('-');
    if (arr.length == 2 && isNumeric(arr[1])) {
        getExamsList(arr[1]);
    }
});

function getExamsList(id) {
    $.ajax({
        url: '<?php echo BASE_URL; ?>/exam/list_by_subject',
        type: 'get',
        dataType: 'json',
        data: {
            subject_id: id
        },
        success: function(response) {
            const {
                exams
            } = response;
            exams.forEach(e => {
                $exams.append(`
                        <div class="col-md-4 col-sm-6 col-xs-12 mb-5">
                            <div class="card h-100">
                                <img src="<?php echo BASE_URL; ?>${e.thumbnail.trim().length>0?e.thumbnail:'/public/assets/images/no_image.jpg'}" alt="Thumbnail" class="card-img-top img-thumbnail img-responsive">
                                <div class="card-body">
                                    <h5 class="card-title text-success fw-bold">${e.title}</h5>
                                    ${e.description}
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        <div><i class="fa fa-clock-o text-info" aria-hidden="true"></i> Ngày bắt đầu:<strong> ${e.begin_date}</strong></div>
                                        <div><i class="fa fa-clock-o text-danger" aria-hidden="true"></i> Ngày kết thúc:<strong> ${e.end_date}</strong></div>
                                        <div><strong>Số câu hỏi:</strong> ${e.number_of_questions}</div>
                                        <div><strong>Thời gian:</strong> ${e.duration} phút</div>
                                    </small>
                                    <a class="btn btn-sm btn-primary btn-exam mt-3" href="javascript:void(0)" onClick="DoExam(${e.id},'<?php echo BASE_URL; ?>/exam/doing?id=${e.id}')">Làm bài thi</a>
                                </div>
                            </div>
                        </div>
                    `);
            });
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function DoExam(exam_id, url) {
    $.ajax({
        url: '<?php echo BASE_URL; ?>/exam/check_available',
        type: 'get',
        dataType: 'json',
        data: {
            exam_id
        },
        success: function(response) {

            const {
                code,
                msg,
                header
            } = response;
            if (code == 200) {
                window.location.replace(url);
            } else {
                $.toast({
                    heading: header,
                    text: msg,
                    icon: 'warning',
                    loader: true,
                    loaderBg: '#9EC600'
                })
            }

        },
        error: function(err) {
            console.log(err);
        }
    })
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}
</script>

<style>
.card-img-top {
    width: 100%;
    height: auto;
}

.card-body {
    flex: 1;
    padding: 15px;
}

.card-title {
    font-size: 1.25rem;
    margin-bottom: 10px;
}

.card-text {
    font-size: 0.95rem;
    line-height: 1.4;
    overflow: hidden;
    max-height: 6.5em;
    position: relative;
}

.card-footer {
    background-color: #f8f9fa;
    padding: 10px 15px;
    border-top: 1px solid #ddd;
    text-align: left;
    margin-top: auto;
}

.btn-exam {
    margin-top: 15px;
    width: 100%;
}

.card-footer .text-muted {
    font-size: 0.85rem;
    line-height: 1.5;
}
</style>