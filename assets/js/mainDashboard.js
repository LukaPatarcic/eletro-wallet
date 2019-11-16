import {addActiveClass} from './main';
import toastr from "toastr";
import numeral from "../../node_modules/numeral/numeral";

$(document).ready(function () {

    var newTransactionForm = $('#newTransaction');
    var nameField = $('#transactionName');
    var typeField = $('#transactionType');
    var balanceField = $('#balance');
    var amountField = $('#amount');

    newTransactionForm.on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/transaction/add',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                toastr.success('Added transaction!');
                addLastTransaction();
                getBalance();
                nameField.val('');
                nameField.attr('disabled', true);
                typeField.val('');
                balanceField.val('');
            },
            error: function () {
                toastr.error('Oops! Something went wrong...');
            }
        });
    });

    function addLastTransaction() {
        let name = $('#transactionName option:selected').text();
        let type = typeField.val();
        let balance = balanceField.val();
        let balanceText = '';

        if(type === 'outcome') {
            balanceText = '<td style="color:red">- $'+balance+'</td>';
        } else {
            balanceText = '<td style="color:green">$'+balance+'</td>';
        }

        $('#listOfTransactions').before(
          '<tr><td>'+name+'</td>'+balanceText+'<td>just now</td></tr>'
        );

    }

    function getBalance() {

        $.ajax({
            url: '/api/user',
            type: 'GET',
            data: $(this).serialize(),
            success: function (response) {
                amountField.text(numeral(response.balance).format('0,0.00'));
            },
            error: function () {
                amountField.text('unknown');
            }
        })

    }

    typeField.change(function() {
        $.ajax({
            url: '/api/transaction/type/'+typeField.val(),
            type: 'GET',
            success: function (response) {
                nameField.empty();
                nameField.removeAttr("disabled");
                nameField.append("<option value=\"\" hidden>Select a name</option>")
                $.each(response, function(keys, values) {
                    $.each(values, function(key, value) {
                        nameField.append("<option value=\""+value.id+"\">"+value.name+"</option>");
                    })
                })

            },
            error: function () {
                toastr.error('Oops! Something went wrong...');
            }
        })
    });

    balanceField.on('keyup', function (e) {
        var allowedValues = [8,37,39,45,46,48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105]
        var value = e.currentTarget.value;
        if (!allowedValues.includes(e.which)) {
            e.preventDefault();
        }

        var newNumber = format_number(value);
        balanceField.val(newNumber);
    });

    function format_number(number, prefix, thousand_separator, decimal_separator)
    {
        var 	thousand_separator = thousand_separator || ',',
            decimal_separator = decimal_separator || '.',
            regex		= new RegExp('[^' + decimal_separator + '\\d]', 'g'),
            number_string = number.replace(regex, '').toString(),
            split	  = number_string.split(decimal_separator),
            rest 	  = split[0].length % 3,
            result 	  = split[0].substr(0, rest),
            thousands = split[0].substr(rest).match(/\d{3}/g);

        if (thousands) {
            var separator = rest ? thousand_separator : '';
            result += separator + thousands.join(thousand_separator);
        }
        result = split[1] != undefined ? result + decimal_separator + split[1] : result;
        return prefix == undefined ? result : (result ? prefix + result : '');
    };

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

    addActiveClass();
});