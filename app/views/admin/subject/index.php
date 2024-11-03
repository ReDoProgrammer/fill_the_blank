<div class="container">
    <div class="row justify-content-end mb-4">
        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search subject..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
        <div class="col col-md-2 align-self-end text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa fa-plus text-white" data-bs-toggle="modal" data-bs-target="#modal"></i> Thêm
                mới
            </button>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Subject Name</th>
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

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTittle">Thêm mới môn học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-md-12 form-group">
                            <label for="">Subject Name:</label>
                            <input type="text" name="" id="txtName" placeholder="Subject Name" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSubmit">Submit changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    var keyword = '', page = 1, pageSize = 10, id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    const $btnSearch = $('#btnSearch'), $btnSubmit = $('#btnSubmit'), $keyword = $('#txtKeyword'), $table = $('#tblData'), $pagination = $('.pagination'), $modal = $('#modal'), $modalTittle = $('#modalTittle');
    const $name = $('#txtName');

    $(document).ready(function () {
        LoadData();
        $('.pagination').on('click', '.page-link', function (e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).data('page'); // Lấy giá trị data-page
            console.log({ currentPage, pageNumber });
            if (currentPage !== undefined) {
                if (currentPage !== 0 && currentPage !== pageNumber) {
                    page = currentPage;
                    LoadData();
                } else {
                    if (currentPage === 0 && page > 1) {
                        page--;
                        LoadData();
                    } else if (currentPage == pageNumber && page < pageNumber) {
                        page++;
                        console.log({ page });
                        LoadData();
                    }
                }
            }
        });
    });

    $btnSearch.click(function () {
        page = 1;
        keyword = $keyword.val().trim();
        LoadData();
    });

    $btnSubmit.click(function () {
        const name = $name.val().trim();

        if (name.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Tên môn học không thể để trống',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        const url = `<?php echo BASE_URL; ?>/admin/subject/${id <= 0 ? 'add' : 'update'}`;
        const data = { name: name };
        if (id > 0) data.id = id;

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                const { code, msg } = response;
                $.toast({
                    heading: code === 200 || code === 201 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                    text: msg,
                    icon: code === 200 || code === 201 ? 'success' : 'error',
                    loader: true,
                    loaderBg: '#9EC600'
                });
                if (code == 201 || code == 200) {
                    $modal.modal('hide');
                    LoadData();
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

    function LoadData() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/search',
            method: 'GET',
            data: {
                keyword,
                page,
                pageSize
            },
            dataType: 'json',
            success: function (response) {
                const { subjects, totalPages, currentPage, pageSize, totalRecords } = response;
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $table.empty();
                $pagination.empty();

                subjects.forEach(s => {
                    $table.append(`
                    <tr>
                        <td>${++idx}</td>
                        <td class="fw-bold text-info">${s.name}</td>
                        <td class="text-end">
                        <a href="javascript:void(0)" onClick="UpdateSubject(${s.id})"><i class="fa fa-edit text-warning"></i></a>
                        <a href="javascript:void(0)" onClick="DeleteSubject(${s.id},'${s.name}')"><i class="fa fa-trash-o text-danger"></i></a>
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
        });
    }

    function UpdateSubject(id) {
        this.id = id;
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/detail',
            type: 'GET',
            data: { id },
            dataType: 'json',
            success: function (response) {
                const { code, msg, subject } = response;
                if (code == 200) {
                    $modal.modal('show');
                    $modalTittle.text('Cập nhật thông tin môn học');
                    $name.val(subject.name);
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function DeleteSubject(id, name) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá môn học <span class="text-warning">${name}</span>?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/admin/subject/delete',
                    type: 'POST',
                    data: { id },
                    dataType: 'json',
                    success: function (response) {
                        const { code, msg } = response;
                        $.toast({
                            heading: code == 200 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                            text: msg,
                            icon: code == 200 ? 'success' : 'error',
                            loader: true,
                            loaderBg: '#9EC600'
                        });
                        if (code == 200) {
                            LoadData();
                        }
                    },
                    error: function (err) {
                        console.log(err.responseText);
                    }
                });
            }
        });
    }

    $modal.on('hide.bs.modal', function (e) {
        $modalTittle.text('Thêm mới môn học');
        $name.val('');
        id = -1;
    });
</script>
