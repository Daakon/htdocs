/*function checkEmail() {
    var email = $('#email').val();
    var regex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}*/

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
 /*   if (!checkEmail()) {
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
    }*/

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
        $('#form-group-birthday .error-text').html('Please select the month, day, and year of your birthday.');
        request_successful = false;
    }

    // check day
    if (!checkRequest('ddDay', 'day')) {
        $('#form-group-birthday').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select the month, day, and year of your birthday.');
        request_successful = false;
    }

    // check year
    if (!checkRequest('ddYear', 'year')) {
        $('#form-group-birthday').addClass('has-error');
        $('#form-group-birthday .error-text').html('Please select the month, day, and year of your birthday.');
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

