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
            dataType: "html",
            cache: false,
            success: function(){
                $('#error_signup_text').text('Success! Check your email!!');
            },
            error: function () {
                $('#error_signup_text').text('Check entered data');
            }
        });
    });
});