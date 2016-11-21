<?php
/* @var $this yii\web\View */
?>
<h1>Login exar aper</h1>


<form action="" method="post">
    username:<br>
    <input type="text" name="username" value="">
    <br>
    pass:<br>
    <input type="text" name="password" value="">
    <input type="submit" value="Submit">
    <button type="button"> aaa</button>
</form>


<script>



    $("button").click(function(){
        $.ajax({
            url:'/user/userList',
            type:"POST",
            success:function(data) {
                console.log(data);
            },
            error:function (xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
            }
        });
    });
</script>