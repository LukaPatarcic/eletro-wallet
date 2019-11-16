import toastr from 'toastr'
window.toast = toastr;

function addActiveClass() {
    var pathname = window.location.pathname;
    var match = /\/user\/dashboard\/?((\w)*)/.exec(pathname);
    if(match[1] == '') {
        $('.home').addClass('active');
    } else {
        $('.'+match[1]).addClass('active');

    }
}
export {addActiveClass}

$('#contact-form').submit(function (e) {
    e.preventDefault();
    var name = $.trim($('#name').val());
    var email = $.trim($('#email').val());
    var subject = $.trim($('#subject').val());
    var text = $.trim($('#message').val());
    var message = $('#displayMessage');
    $.ajax({
        url: '/api/contact',
        type: 'POST',
        data: $(this).serialize(),
        beforeSend: function () {
            message.css('display','block');
            message.text('');
            if(name === '' || email === '' || subject === '' || text === '') {
                message.addClass('alert-danger');
                message.text('Please fill in all fields');
                return false;
            }

            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!regex.test(email)) {
                message.addClass('alert-danger');
                message.text('Please enter a valid email address');
                return false;
            }
            message.removeClass('alert-danger').addClass('alert-warning');
            message.append('<i class="fa fa-spin fa-spinner"></i> Sending message');

        },
        success: function (response) {
            message.text('');
            if(response.sent) {
                message.append('Email has been sent. We will contact you soon!');
                message.removeClass('alert-warning alert-danger').addClass('alert-success');
                $('#contact-form')[0].reset();
                setTimeout(function (){
                    message.fadeOut();
                },2000);
                return;
            } else {
                message.removeClass('alert-warning alert-danger').addClass('alert-danger');
                message.append('Oops. There was a problem, please try again later!');
                return;
            }


        },
        error: function () {
            message.text('');
            message.removeClass('alert-warning alert-danger').addClass('alert-danger');
            message.append('Oops. There was a problem, please try again later!');
        }
    })
});