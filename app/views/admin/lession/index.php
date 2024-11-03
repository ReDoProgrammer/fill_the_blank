<div class="container">
    <div class="row justify-content-end mb-4">
        <div class="col-md-6 form-group">
            <select id="ddlSubject" class="form-control"></select>
        </div>
        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm kiếm bài học..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Tìm kiếm
                </button>
            </div>
        </div>
        <div class="col col-md-2 align-self-end text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa fa-plus text-white" data-bs-toggle="modal" data-bs-target="#modal"></i> Thêm mới
            </button>
        </div>
    </div>
</div>



<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tên bài học</th>
            <th scope="col">Môn học</th>
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
                <h5 class="modal-title" id="modalTittle">Thêm mới bài học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-md-12 form-group">
                            <label for="">Tên bài học:</label>
                            <input type="text" id="txtName" placeholder="Tên bài học" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnSubmit">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
    var keyword = '', page = 1, pageSize = 10, id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    const $btnSearch = $('#btnSearch'), $btnSubmit = $('#btnSubmit'), $keyword = $('#txtKeyword'), $table = $('#tblData'), $pagination = $('.pagination'), $modal = $('#modal'), $modalTittle = $('#modalTittle');
    const $name = $('#txtName'), $subject = $('#ddlSubject');

    $(document).ready(function () {
        LoadSubjects();
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
        console.log({ keyword });
        LoadData();
    });

    $btnSubmit.click(function () {
        const name = $name.val().trim();
        const subjectId = $subject.val();

        if (name.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Tên bài học không thể để trống',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        const url = `<?php echo BASE_URL; ?>/admin/lession/${id <= 0 ? 'add' : 'update'}`;
        const data = { name: name, subject_id: subjectId };
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
            url: '<?php echo BASE_URL; ?>/admin/lession/getAll',
            method: 'GET',
            data: {
                keyword,
                page,
                pageSize,
                subject_id: $subject.val()
            },
            dataType: 'json',
            success: function (response) {
                const { lessions, totalPages, currentPage, pageSize, totalRecords } = response;
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $table.empty();
                $pagination.empty();

                lessions.forEach(l => {
                    $table.append(`
                    <tr>
                        <td>${++idx}</td>
                        <td class="text-info">${l.name}</td>
                        <td class="fw-bold text-warning">${l.subject_name}</td>
                        <td class="text-end">
                        <a href="javascript:void(0)" onClick="UpdateLession(${l.id})"><i class="fa fa-edit text-warning"></i></a>
                        <a href="javascript:void(0)" onClick="DeleteLession(${l.id},'${l.name}')"><i class="fa fa-trash-o text-danger"></i></a>
                        <a href="javascript:void(0)" onClick="ExporExcel(${l.id},'${l.name}')"><i class="fa fa-file-excel-o text-success" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    `);
                });

                $pagination.append(`<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true" data-page="0">Trang trước</a>
                </li>`);
                for (let i = 1; i <= totalPages; i++) {
                    $pagination.append(`<li class="page-item ${currentPage === i ? "active" : ""}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`);
                }
                $pagination.append(`<li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
                    <a class="page-link" href="#" data-page="${totalPages}">Trang sau</a>
                </li>`);
            },
            error: function (err) {
                console.log(err.responseText);
            }
        });
    }

    function LoadSubjects() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/allSubjects',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                const { subjects } = response;
                $subject.empty();
                subjects.forEach(s => {
                    $subject.append(`<option value="${s.id}">${s.name}</option>`);
                });
                LoadData();
            },
            error: function (err) {
                console.log(err.responseText);
            }
        });
    }

    function UpdateLession(id) {
        this.id = id;
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/lession/detail',
            type: 'GET',
            data: { id },
            dataType: 'json',
            success: function (response) {
                const { code, msg, lession } = response;
                if (code == 200) {
                    $modal.modal('show');
                    $modalTittle.text('Cập nhật thông tin bài học');
                    $name.val(lession.name);
                    $subject.val(lession.subject_id);
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function DeleteLession(id, name) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá bài học <span class="text-warning">${name}</span>?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/admin/lession/delete',
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

    function ExporExcel(id, name) {
        console.log({ id, name });
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/question/all',
            type: 'GET',
            dataType: 'json',
            data: { lession_id: id },
            success: function (response) {
                const { code, msg, data } = response;
                if (code === 200 && data.length > 0) {
                    console.log(data);
                    // Xử lý dữ liệu để loại bỏ thẻ HTML
                    exportToExcel(data, name);
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }
    function htmlToText(html) {
        // Tạo đối tượng DOMParser để phân tích HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        // Lấy nội dung text mà không có các thẻ HTML
        return doc.body.textContent || "";
    }

    function exportToExcel(data, name) {
      

        const formattedData = data.map((item, index) => ({
            STT: index + 1,
            "Câu hỏi": htmlToFormattedText(item.question_text),
            "Bài học": item.lession_name
        }));

        const ws = XLSX.utils.json_to_sheet(formattedData);

        ws['!rows'] = [{ hpt: 30 }];
        ws['A1'] = { v: 'DANH SÁCH CÂU HỎI', s: { font: { sz: 14, bold: true }, alignment: { horizontal: 'center' } } };
        ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 2 } }];

        ws['!cols'] = [
            { width: 10 },
            { width: 50 },
            { width: 30 }
        ];

        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_address = { c: C, r: R };
                const cell_ref = XLSX.utils.encode_cell(cell_address);
                if (!ws[cell_ref]) ws[cell_ref] = {};
                ws[cell_ref].s = {
                    border: {
                        top: { style: "thin", color: { rgb: "000000" } },
                        right: { style: "thin", color: { rgb: "000000" } },
                        bottom: { style: "thin", color: { rgb: "000000" } },
                        left: { style: "thin", color: { rgb: "000000" } }
                    }
                };
            }
        }

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, name);

        XLSX.writeFile(wb, name+".xlsx");
    };

    function htmlToFormattedText(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const paragraphs = Array.from(doc.querySelectorAll('p')).map(p => p.innerText);
        return paragraphs.map(p => p.replace(/\t/g, '    ')).join('\n');
    }

    function htmlToFormattedText(html) {
        // Thay thế các thẻ HTML và ký tự đặc biệt
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Thay thế các thẻ <p> bằng xuống hàng
        const paragraphs = Array.from(doc.querySelectorAll('p')).map(p => p.innerText);

        // Thay thế tab và các ký tự đặc biệt
        return paragraphs.map(p => p.replace(/\t/g, '    ')).join('\n');
    }


    $modal.on('hide.bs.modal', function (e) {
        $modalTittle.text('Thêm mới bài học');
        $name.val('');
        id = -1;
    });
</script>