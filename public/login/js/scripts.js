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
            error: function (response) {
                console.log(response);
                if (response['status'] === 403) {
                    $('#error_signin_text').text("Need to activate user first").prop('style', "color:red");
                } else {
                    $('#error_signin_text').text("Password doesn't match").prop('style', "color:red");
                }
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
                $('#error_signup_text').text(response['success']).prop('style', "color:green");
            }, // finish error check
            error: function (response) {
                console.log(response);
                $('#error_signup_text').text(response.responseJSON['errorMes']).prop('style', "color:red");
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