$(document).ready(function () {
    $('#user_signin').submit(function(e){
        e.preventDefault();

        var formData = $(this).serialize();
        $.ajax({
            url: "http://quiz.dev/authorize/signin",
            type: "POST",
            data: formData,
            dataType: "html",
            cache: false,
            success: function(){
                window.location.replace("/");
            },
            error: function () {
                $('#error_text').text("No such user");
            }
        });
    });

    $('#user_signup').submit(function(e){
        e.preventDefault();

        var formData = $(this).serialize();
        $.ajax({
            url: "http://quiz.dev/authorize/signup",
            type: "POST",
            data: formData,
            dataType: "html",
            cache: false,
            success: function(response){

            },
            error: function (response) {
                $('#error_text').text("No such user");
            }
        });
    });
});