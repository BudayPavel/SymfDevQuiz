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
                $('#error_signin_text').text("Password doesn't match");
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
            dataType: "json",
            cache: false,
            success: function(response){
                console.log(response);
                $('#error_signup_text').text(response['success']);
            }, // finish error check
            error: function (response) {
                console.log(response);
                $('#error_signup_text').text(response.responseJSON['errorMes']);
            }
        });
    });

    $('#user_forget').submit(function(e){
        e.preventDefault();

        var formData = $(this).serialize();
        $.ajax({
            url: "http://quiz.dev/authorize/forget",
            type: "POST",
            data: formData,
            dataType: "html",
            cache: false,
            success: function(){
                $('#error_forget_text').text('Check your email!');
            },
            error: function () {
                $('#error_forget_text').text('Email not found');
            }
        });
    });
});