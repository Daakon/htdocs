function checkEmail() {

    if (!pattern.test(email)) {
        return false;
    } else {
        return true;
    }
}

function checkRequest(field_id, invalid_input) {
    var request = $('#' + field_id).val();
    if (request == invalid_input) {
        return false;
    } else {
        return true;
    }
}

$('#request_message').modal({backdrop: false, show: false});

$('#signup').on("click", function (event) {
    event.preventDefault();

    var request_successful = true;
    $('#form-group-email').removeClass('has-error');
    $('#form-group-firstName').removeClass('has-error');
    $('#form-group-lastName').removeClass('has-error');
    $('#form-group-ddGender').removeClass('has-error');
    $('#form-group-birthday').removeClass('has-error');
    $('#form-group-password').removeClass('has-error');
    $('#form-group-username').removeClass('has-error');
    $('.error-text').html('');

    // check email address
    // check if blank, if it fits email regex, and check if it already exists in db
    var email = $('#email').val();
    var pattern = /^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/;
    if (!pattern.test(email)) {
        $('#form-group-email').addClass('has-error');
        $('#form-group-email .error-text').html('Please enter a valid email address.');
        request_successful = false;
    } else {
        // ajax call to check if email address already exists in db
        // if so, don't let them use it again
        var serializedData = $('#email').serialize();
        request = $.ajax({
            url: "helpers/check_email.php",
            type: "post",
            data: serializedData
        });
        if (!request) {
            request_successful = false;
            $('#form-group-email').addClass('has-error');
            $('#form-group-email .error-text').html('That email address has been previously used on this site. Please log in or use another email address.');
        }
    }

    // check first name
    if (!checkRequest('firstName', '')) {
        $('#form-group-firstName').addClass('has-error');
        $('#form-group-firstName .error-text').html('Please enter your first name.');
        request_successful = false;
    }

    // check last name
    if (!checkRequest('lastName', '')) {
        $('#form-group-lastName').addClass('has-error');
        $('#form-group-lastName .error-text').html('Please enter your last name.');
        request_successful = false;
    }

    // check gender
    if (!checkRequest('ddGender', '')) {
        $('#form-group-ddGender').addClass('has-error');
        $('#form-group-ddGender .error-text').html('Please select your gender');
        request_successful = false;
    }

    // check month
    if (!checkRequest('ddMonth', 'month')) {
        $('#form-group-birthday').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select the month of your birthday.');
        request_successful = false;
    }

    // check day
    if (!checkRequest('ddDay', 'day')) {
        $('#form-group-birthday').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select the day of your birthday.');
        request_successful = false;
    }

    // check year
    if (!checkRequest('ddYear', 'year')) {
        $('#form-group-year').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select the year of your birthday.');
        request_successful = false;
    }

    // check city
    if (!checkRequest('ddCity', 'city')) {
        $('#form-group-city').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select your city.');
        request_successful = false;
    }

    // check city
    if (!checkRequest('ddState', 'state')) {
        $('#form-group-city').addClass('has-error');
        $('#form-group-state .error-text').html('Please select your state.');
        request_successful = false;
    }

    // check username
    // check if blank, and check if it already exists in db
    if (!checkRequest('username', '')) {
        $('#form-group-username').addClass('has-error');
        $('#form-group-username .error-text').html('Please enter a username.');
        request_successful = false;
    } else {
        // ajax call to check if username already exists in db
        // if so, don't let them use it again
        var serializedData = $('#username').serialize();
        request = $.ajax({
            url: "helpers/check_username.php",
            type: "post",
            data: serializedData
        });
        if (!request) {
            request_successful = false;
            $('#form-group-username').addClass('has-error');
            $('#form-group-username .error-text').html('That username is already in use. Please select another.');
        }
    }

    // check password
    if (!checkRequest('password', '')) {
        $('#form-group-password').addClass('has-error');
        $('#form-group-password .error-text').html('Please enter a password.');
        request_successful = false;
    }

    if (request_successful) {
        $('#rb_signup_form').submit();
    } else {
        var message = "There was a problem submitting your request. Please correct the form and resubmit.";
        $('#request_message .modal-body').html(message);
        $('#request_message').modal('show');
    }

});

$('input#submit').click(function(){
    if ($('#flPostMedia').val()) {
        $('#post-loading-box').css('display', 'block');
    }
});
$('input#btnComment').click(function(){
    if ($('#flPostMedia').val()) {
        $('#post-loading-box').css('display', 'block');
    }
});
