import {addActiveClass} from './main';
import '../css/dashboard.css'
import '../../node_modules/flatpickr/dist/flatpickr.min'
import '../../node_modules/flatpickr/dist/flatpickr.min.css'
import toastr from "toastr";

$(document).ready(function () {
    addActiveClass();

    var editId;

    $('#addCustomTransactionType').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/customTransaction/add',
            type: 'POST',
            data: $(this).serialize(),
            success: function() {
                toastr.success('New custom transaction added.');
                $('.radio').prop('checked', false);
                $('.name').val('');
            },
            error: function() {
                toastr.error('Something went wrong, try again');
                $('.radio').prop('checked', false);
                $('.name').val('');
            }
        });
    });

    $('.editRow').click(function(e) {
        let id = e.currentTarget.getAttribute('data-id');
        $.ajax({
            url: '/api/transaction/'+id,
            type: 'GET',
            success: function(response) {
                $('#modal-title').text("Transaction name: " + response.transactionType.name);
                $('#modal-amount').val(response.amount);
                window.editId = id;
            }
        })
    });

    $('.deleteRow').click(function(e) {
        if(confirm("Are you sure?")) {
            let id = e.currentTarget.getAttribute('data-id');

            $.ajax({
                url: '/api/transaction/'+id,
                type: 'DELETE',
                success: function (response) {
                    if(response.success) {
                        toastr.success('Successfully deleted transaction');
                        e.currentTarget.closest('tr').remove()
                    } else {
                        toastr.error('Oops something went wrong...');
                    }
                },
                error: function () {
                    toastr.error('Oops something went wrong...');
                }
            })
        }
    })

    $('#modalEditButton').click(function(e) {
        e.preventDefault();
        let amount = $("#modal-amount").val();
        let id = window.editId;
        $.ajax({
            url: '/api/transaction/edit/'+id,
            type: 'PUT',
            data: {amount : amount},
            success: function (response) {
                if(response.success) {
                    toastr.success('Successfully updated transaction');
                } else {
                    toastr.error('Oops something went wrong...');
                }
            },
            error: function() {
                toastr.error('Oops something went wrong...');
            }
        })
    });

});