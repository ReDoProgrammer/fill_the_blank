<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="<?php echo BASE_URL; ?>/public/assets/plugins/select2/select2.min.css" rel="stylesheet">
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/select2/select2.min.js"></script>



<div class="modal fade" id="modalCustomize" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thiết lập đề thi tuỳ chỉnh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <select class="form-control" id="slQuestions" style="width: 100%;"></select>
                        <button class="btn btn-outline-secondary btn-warning text-white" type="button"
                            id="btnAddQuestion">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Thêm câu hỏi
                        </button>
                    </div>
                    <!--  Table -->
                    <table class="table table-bordered table-striped table-hover mt-3">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Câu hỏi</th>
                                <th scope="col">Điểm</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="tblQuestions"></tbody>
                    </table>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="">Số câu hỏi:</label>
                            <label id="lblNumberOfQuestions"></label>
                        </div>
                        <div class="col-md-6">
                            <label for="">Điểm:</label>
                            <label id="lblTotalMarks" class="fw-bold text-danger"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div id="questions"></div>

<script>
    const $questionsList = $('#questions');
    $(document).ready(function () {
        let id = getUrlParameter('id');

        GetDetail(id)
            .then(detail => {
                const { number_of_questions } = detail.exam;
                for (let i = 1; i <= number_of_questions; i++) {
                    $questionsList.append(`<div class="row mt-3">
                     <div class="col-md-12">
                         <div class="form-group">
                             <label>Câu hỏi: ${i}</label>                                     
                            <select class="form-control select2" id="select_${i}" style="width: 100%;">
                             </select>
                         </div>
                     </div>
                   </div>`);

                    $(`#select_${i}`).select2({
                        placeholder: 'Vui lòng chọn',
                        allowClear: true,
                        ajax: {
                            url: '<?php echo BASE_URL; ?>/admin/quiz/search',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    keyword: params.term // Truyền từ khóa tìm kiếm đến API
                                };
                            },
                            processResults: function (data) {
                                // Chuyển đổi dữ liệu từ server về định dạng phù hợp với Select2
                                return {
                                    results: data.map(item => ({
                                        id: item.id,
                                        text: item.name
                                    }))
                                };
                            },
                            cache: true
                        }
                    });
                }
            })
            .catch(err => {
                console.log(err);
            });
    });

    function getUrlParameter(sParam) {
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
    }

    function GetDetail(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/admin/exam/detail',
                type: 'get',
                dataType: 'json',
                data: { id },
                success: function (response) {
                    return resolve(response);
                },
                error: function (err) {
                    return reject(err);
                }
            });
        });
    }
</script>