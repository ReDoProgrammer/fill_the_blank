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
    $(document).ready(function () {
        // Lấy URL hiện tại
        var url = window.location.href;

        // Tạo đối tượng URLSearchParams
        var urlParams = new URLSearchParams(window.location.search);

        // Lấy giá trị của tham số "s" và "l"
        var s = urlParams.get('s'); // "html-25"

        // Tách số từ tham số "s" và "l"
        teachingId = s.split('-')[1]; // 25
        ListStudents(teachingId);
    })
    var page = 1, pageSize = 10, keyword = '';
    function ListStudents(teachingId) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/teacher/classroom/getStudentsByClassId`,
            type: 'GET',
            dataType: 'json',
            data: {
                page,
                pageSize,
                keyword,
                teachingId
            },
            success: function (response) {
                console.log(response);
                const { users, totalPages, currentPage, pageSize, totalRecords } = response.data;
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $table.empty(); // Đảm bảo bảng được làm mới trước khi thêm dữ liệu
                $pagination.empty(); // Đảm bảo phân trang được làm mới trước khi thêm dữ liệu

                users.forEach(u => {
                    $table.append(`
                    <tr id = "${u.id}">                   
                        <td>${++idx}</td>
                        <td class="fw-bold">${u.username}</td>
                        <td class="fw-bold text-secondary">${u.user_code}</td>
                        <td>${u.fullname}</td>
                        <td>${u.phone}</td>
                        <td>${u.email}</td>
                        <td class="fw-bold text-success">
                        </td>
                    </tr>
                `);
                });

                $pagination.append(`<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
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
            error: function (err) {
                console.log(err.responseText);

            }
        })
    }
</script>