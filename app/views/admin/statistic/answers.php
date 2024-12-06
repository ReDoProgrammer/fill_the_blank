<script src="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.css">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <select name="" id="slSubjects" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <select name="" id="slLessions" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm kiếm...">
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
                <button class="btn btn-outline-warning btn-success text-white" type="button" id="btnExport">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- statistic Table -->
    <table class="table table-bordered table-striped table-hover mt-3" id="table">

    </table>
</div>


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
    const $slSubjects = $('#slSubjects'),
        $slLessions = $('#slLessions'),
        $keyword = $('#txtKeyword'),
        $search = $('#btnSearch'),
        $modal = $('#modal'),
        $content = $('#modalContent'),
        $table = $('#table'),
        $export = $('#btnExport'),
        pageSize = 10;

    let page = 1;
    $(document).ready(function() {
        LoadSubjects();
    })




    $slLessions.change(function() {
        $table.empty();
        if ($(this).val()) {
            // Xóa <thead> hiện tại nếu đã tồn tại để tránh trùng lặp
            $table.find('thead').remove();

            // Thêm mới phần <thead> vào bảng
            $table.append(`
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">STT</th>
                                <th rowspan="2" class="align-middle">Mã thành viên</th>
                                <th rowspan="2" class="align-middle">Tài khoản</th>
                                <th rowspan="2" class="align-middle">Họ tên</th>
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
                        </thead>
                        <tbody></tbody>
                    `);


            LoadStatistic($(this).val());
        } else {
            $table.append(`
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">STT</th>
                                    <th rowspan="2" class="align-middle">Mã thành viên</th>
                                    <th rowspan="2" class="align-middle">Tài khoản</th>
                                    <th rowspan="2" class="align-middle">Họ tên</th>
                                    <th rowspan="2" class="align-middle">Số lần làm bài</th>
                                    <th rowspan="2" class="align-middle">Tỉ lệ làm đúng</th>
                                    <th colspan="3" class="text-center">Bài làm nhiều nhất</th>
                                </tr>
                                <tr>
                                    <th>Tên bài</th>
                                    <th>Số lần</th>
                                    <th>Tỉ lệ (%)</th>               
                                </tr>
                            </thead>
                            <tbody></tbody>
                        `);

        }


    })

     // Xử lý khi chọn môn học
     $slSubjects.change(function() {
        Loadlessions(parseInt($slSubjects.val()));
    });

    // Tải danh sách bài học dựa trên môn học đã chọn
    function Loadlessions(subject_id) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/lession/getBySubject`,
            type: 'GET',
            data: {
                subject_id
            },
            dataType: 'json',
            success: function(response) {
                $slLessions.empty().append('<option value="">Chọn bài học</option>');
                $.each(response.lessions, function(index, lession) {
                    $slLessions.append(`<option value="${lession.id}">${lession.name}</option>`);
                });
                $slLessions.trigger('change');
            },
            error: function(err) {
                console.log(err);
            }
        });
    }


    // Tải danh sách môn học
    function LoadSubjects() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/allSubjects',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const {
                    subjects
                } = response;
                subjects.forEach(s => {
                    $slSubjects.append(`<option value="${s.id}">${s.name}</option>`);
                });
                $slSubjects.trigger('change');
            },
            error: function(err) {
                console.log(err.responseText);
            }
        });
    }
</script>