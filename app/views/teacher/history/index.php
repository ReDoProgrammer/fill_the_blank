<div class="card">
    <div class="card-header" id="cardTitle">
        HỌC VIÊN KHÔNG TỒN TẠI
    </div>
    <div class="card-body">
       
    </div>
</div>

<script>
    $(document).ready(function(){
        var url = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var u =parseInt(urlParams.get('u')); 
        var c = urlParams.get('c'); 

        GetUserById(u,c);
       
    })

    function GetUserById(id,code){
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/user/detail',
            type:'GET',
            dataType:'json',
            data:{id},
            success:function(response){
                console.log(response);
                const {user} = response;
                if(user.user_code == code){
                    $('#cardTitle').html(`Lịch sử ôn tập của học viên: ${user.user_code}. Họ tên: <span class = "fw-bold text-info">${user.fullname}</span>. Lớp: <span class="text-warning">${user.name}</span>`);
                }
            },
            error:function(err){
                console.log(err.responseText);
                
            }
        })
    }
</script>