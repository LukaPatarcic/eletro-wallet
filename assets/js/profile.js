import toastr from 'toastr'
window.toast = toastr;
$('select[name="chartType"]').on('change',function (e) {
    var chart = e.currentTarget.value;
    $.ajax({
        url: '/api/chart/change',
        type: 'PATCH',
        data: 'chart='+chart,
        beforeSend: function () {
            $('select[name="chartType"]').addClass('disabled');
        },
        success: function () {
            toastr.success('Chart change successfully');

            setTimeout(function () {
                $('select[name="chartType"]').removeClass('disabled');
            },1000)

        },
        error: function () {
            toastr.error('Oops! There was a problem...');
            setTimeout(function () {
                $('select[name="chartType"]').removeClass('disabled');
            },1000)
        }
    })
});
function getTransactionTypes() {
    var liOpen = "<li class=\"list-group-item d-flex justify-content-between align-items-center\">";
    $.ajax({
        url: '/api/transaction/type/' + recipient,
        type: 'GET',
        success: function(response) {
            modal.find('.list-group').empty();
            $.each(response, function(keys, values) {
                $.each(values, function(key, value) {
                    modal.find('.list-group').append(liOpen + value.name + '<button class=\"badge badge-danger delete-transaction\" data-id='+value.id+'> Delete</button></li>');
                })
            })
        }
    });
}
$('#showMoreModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    window.recipient = button.data('whatever');
    window.modal = $(this);

    modal.find('.modal-title').text('Your list of ' + recipient + ' types');
    modal.find('.list-group').empty();
    getTransactionTypes();
});

    $(document).on('click',"button[data-id]",function (e) {
        var id = e.currentTarget.getAttribute('data-id');
        $.ajax({
            url: '/api/transaction/name/'+id,
            type: 'GET',
            success: function (response) {
                if(response.success) {
                    toastr.success('Successfully delete item');
                    getTransactionTypes();
                } else {
                    toastr.error('Oops something went wrong...');
                }
            },
            error: function () {
                toastr.error('Oops something went wrong...');
            }
        })
    });

$('form[name="customTransaction"]').on('submit',function (e) {
    e.preventDefault();
    $.ajax({
        url: '/api/transaction/add',
        type: 'POST',
        data: $(this).serialize(),
        success: function (response) {
            if(response.error) {
                toastr.error(response.error);
            } else {
                toastr.success(response.success);
                $('form[name="customTransaction"]')[0].reset();
            }
        }
    })
});