<h1>Ngân hàng câu hỏi</h1>


<script>
    $(document).ready(function() {
        // Lấy URL hiện tại
        var url = window.location.href;

        // Tạo đối tượng URLSearchParams
        var urlParams = new URLSearchParams(window.location.search);

        // Lấy giá trị của tham số "s" và "l"
        var s = urlParams.get('s'); // "html-25"
        var l = urlParams.get('l'); // "the-tieu-de-va-doan-van-23"

        // Tách số từ tham số "s" và "l"
        var subjectId = s.split('-')[1]; // 25
        var lessonId = l.split('-').pop(); // 23

        console.log("Subject ID:", subjectId); // 25
        console.log("Lesson ID:", lessonId); // 23
    });
</script>