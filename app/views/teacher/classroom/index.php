<div class="container">
    <div class="row justify-content-end mb-4 mt-2">
        <div class="col col-md-12 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm kiếm học viên..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tài khoản</th>
            <th scope="col">Mã học viên</th>
            <th scope="col">Họ và tên</th>
            <th scope="col">Điện thoại</th>
            <th scope="col">Email</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody id="tblData"></tbody>
</table>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-end pagination-sm">

        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>

    </ul>
</nav>
<script>
    const $table = $('#tblData'),
        $pagination = $('.pagination');
        var teachingId = 0;
        let currentPage =1 ;
    $(document).ready(function() {
        var url = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        teachingId = parseInt(urlParams.get('r'));
        if (typeof teachingId !== 'undefined' && Number.isInteger(teachingId)) {
            ListStudents(teachingId);
        }

        $('.pagination').on('click', '.page-link', function(e) {

            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).data('page'); // Lấy giá trị data-page
            if (currentPage !== undefined) {
                if (currentPage !== 0 && currentPage !== pageNumber) {
                    page = currentPage;
                    ListStudents(teachingId);
                } else {

                    if (currentPage === 0 && page > 1) {
                        page--;
                        ListStudents(teachingId);
                    } else if (currentPage == pageNumber && page < pageNumber) {
                        page++;                       
                        ListStudents(teachingId);
                    }
                }
            }

        });

    })
    var page = 1,
        pageSize = 10,
        keyword = '';

$('#btnSearch').click(function(){
    ListStudents(teachingId);
})

    function ListStudents(teachingId) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/teacher/classroom/getStudentsByClassId`,
            type: 'GET',
            dataType: 'json',
            data: {
                page:currentPage,
                pageSize,
                keyword:$('#txtKeyword').val().trim(),
                teachingId
            },
            success: function(response) {
                const {
                    users,
                    totalPages,
                    currentPage,
                    pageSize,
                    totalRecords
                } = response.data;
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $table.empty(); // Đảm bảo bảng được làm mới trước khi thêm dữ liệu
                $pagination.empty(); // Đảm bảo phân trang được làm mới trước khi thêm dữ liệu

                users.forEach(u => {
                    $table.append(`
                    <tr id = "${u.id}">                   
                        <td class = "text-center">${++idx}</td>
                        <td class="fw-bold">${u.username}</td>
                        <td class="fw-bold text-secondary">${u.user_code}</td>
                        <td class = "text-info fw-bold">${u.fullname}</td>
                        <td>${u.phone}</td>
                        <td>${u.email}</td>
                        <td class="text-success text-center">
                            <button class="btn btn-sm text-white btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-gear"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/history/index?u=${u.id}&c=${u.user_code}">Lịch sử luyện tập</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/history/quiz?u=${u.id}&c=${u.user_code}">Lịch sử thi</a></li>
                            </ul>
                        </td>
                    </tr>
                `);
                });

                $pagination.append(`<li class="page-item ${page === 1 ? "disabled" : ""}">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true" data-page="0">Previous</a>
                            </li>`);
                for (let i = 1; i <= totalPages; i++) {
                    $pagination.append(`<li class="page-item ${currentPage === i ? "active" : ""}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
                }
                $pagination.append(`<li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
                    <a class="page-link" href="#" data-page="${totalPages}">Next</a>
                </li>`);

            },
            error: function(err) {
                console.log(err.responseText);

            }
        })
    }
</script>