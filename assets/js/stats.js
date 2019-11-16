function getTotalData() {
    $.ajax({
        url: '/api/stats/all',
        type: 'GET',
        success: function (response) {
            if(!response.income) {
                $('#incomeMessage').text('You have not made any transactions')
            } else if(response.income) {
                pieChart(response.income.names,response.income.amounts,'income');
                $('#other-stats-2').html('<p>Total transactions made: <span class="text-primary font-weight-bold">'+response.income.number+'</span></p>')
            }
            if(!response.outcome) {
                $('#incomeMessage').text('You have not made any transactions')
            } else if(response.outcome) {
                pieChart(response.outcome.names,response.outcome.amounts,'outcome');
                $('#other-stats-1').html('<p>Total transactions made: <span class="text-primary font-weight-bold">'+response.outcome.number+'</span></p>')
            }


        }
    })
}

$('select[name="chartType"]').on('change',function (e) {
    var chart = e.currentTarget.value;
    $.ajax({
        url: '/api/user/chart',
        type: 'GET',
        data: 'chart='+chart,
        beforeSend: function () {
            $('select[name="chartType"]').addClass('disabled');
        },
        success: function (response) {

            if(response.error) {
                toastr.error(response.error);
            } else {
                toastr.success(response.success);
            }
            setTimeout(function () {
                $('select[name="chartType"]').removeClass('disabled');
            },1000)

        }
    })
});

function getCustomData(url,data) {
    $.ajax({
        url: url,
        type: 'GET',
        data: data,
        success: function (response) {

            $('#customStats').remove();
            $('#customStatsMessage').empty();
            $('#statsChartDiv').append(' <canvas id="customStats"></canvas>');
            if(!response) {
                $('#customStatsMessage').text('There are no transactions made')
                $('#other-stats-3').empty();
                return false;
            }
            pieChart(response.names,response.amounts,'customStats');
            $('#other-stats-3').html('<p>Total transactions made: <span class="text-primary font-weight-bold">'+response.number+'</span></p>')
        }
    })
}


function getYears(year = '#year', month = '#month') {
    $.ajax({
        url: '/api/stats/get/year',
        type: 'GET',
        success: function (response) {
            $(year).empty();
            $(year).append('<option selected>Years</option>');
            $.each(response.years, function (key,value) {
                $(year).append('<option>'+value.year+'</option>')
            });
            $(month).append('<option selected>Months</option>');

        }
    })
}

function getMonths(year,monthSelector = '#month') {
    $.ajax({
        url: '/api/stats/get/month',
        type: 'GET',
        data: 'year='+year,
        success: function (response) {
            $(monthSelector).empty();
            $(monthSelector).append('<option value="">Month</option>')
            $.each(response.months,function (key,value) {
                $(monthSelector).append('<option value="'+key+'">'+value+'</option>')
            })
        }
    })
}


function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function hideAllFormElements() {

    $('#date-picker-div').hide();
    $('#month-picker').hide();
    $('#year-picker').hide();
}

$('#selectTime').on('change',function (e) {
    var val = e.currentTarget.value;
    var form = $('form[name="search"]');

    switch (val) {
        case 'day':
            $('#week-month-picker').hide();
            $('#year-picker').hide();
            $('#month-picker').hide();
            $('#date-picker-div').fadeIn();
            form.unbind();
            form.on('submit',function (e) {
                e.preventDefault();
                var type = $('input[name="type"]:checked').val();
                var date = $('input[name="day"]').val();
                date = formatDate(date);
                var data = 'type='+type+'&date='+date;
                var url = '/api/stats/date';
                getCustomData(url,data);

            })
            break;
        case 'week':
        case 'month':
            $('#date-picker-div').hide();
            $('#year-picker').removeClass('col-sm-12').addClass('col-sm-6').fadeIn();
            $('#month-picker').fadeIn();
            getYears();
            $('#year').on('change',function () {
                $.ajax({
                    url: '/api/stats/get/month',
                    type: 'GET',
                    data: 'year='+$(this).val(),
                    success: function (response) {
                        $('#month').empty();
                        $('#month').append('<option value="">Month</option>')
                        $.each(response.months,function (key,value) {
                            $('#month').append('<option value="'+key+'">'+value+'</option>')
                        })
                    }
                })
            });
            form.unbind();
            form.on('submit',function (e) {
                e.preventDefault();
                var type = $('input[name="type"]:checked').val();
                var year = $('select[name="year"]').val();
                var month = $('select[name="month"]').val();

                var data = 'type='+type+'&year='+year+'&month='+month;
                var url = '/api/stats/month';
                getCustomData(url,data);

            })
            break;
        case 'year':
            $('#date-picker-div').hide();
            $('#month-picker').hide();
            $('#year-picker').fadeIn();
            $('#year-picker').removeClass('col-sm-6').addClass('col-sm-12');
            getYears();
            form.unbind();
            form.on('submit',function (e) {
                e.preventDefault();
                var type = $('input[name="type"]:checked').val();
                var year = $('select[name="year"]').val();
                var data = 'type='+type+'&year='+year;
                var url = '/api/stats/year';
                getCustomData(url,data);

            })
            break;
        default:
            hideAllFormElements();

    }
});

document.addEventListener('load', getTotalData());
document.addEventListener('load', getYears('#year-compare-1','#month-compare-1'));
document.addEventListener('load', getYears('#year-compare-2','#month-compare-2'));

$('#year-compare-1').on('change',function (e) {
    var year = e.currentTarget.value;
    getMonths(year,'#month-compare-1');
});
$('#year-compare-2').on('change',function (e) {
    var year = e.currentTarget.value;
    getMonths(year,'#month-compare-2');
});
$('#compare-stats-btn').on('click',function () {
    var jsonData = {
        'year1': $('#year-compare-1').val(),
        'year2': $('#year-compare-2').val(),
        'month1': $('#month-compare-1').val(),
        'month2': $('#month-compare-2').val(),
        'type': $('input[name="type2"]:checked').val()
    };
    console.log(jsonData);
    $.ajax({
        url: '/api/user/compare',
        type: 'POST',
        contentType: "application/json",
        data: JSON.stringify(jsonData),
        success: function (response) {

            console.log(response);
            $('#compareStats').remove();
            $('#compareStatsDiv').append(' <canvas id="compareStats"></canvas>');
            comparePeriods(response);
        }
    })
});

// Data Picker Initialization
$('.datepicker').flatpickr();

hideAllFormElements();
function comparePeriods(data) {

    var ctx = $("#compareStats");
    this.chartCompare = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.label,
            datasets: [{
                type: 'line',
                borderColor: "#E25F5F",
                label: data.date1,
                data: data.amount1,
                borderWidth: 3,
                xAxisID: "x-axis-1",
            },
                {
                    type: 'line',
                    borderColor: "#2793DB",
                    label: data.date2,
                    data: data.amount2,
                    borderWidth: 3,
                    xAxisID: "x-axis-2",

                },
            ]
        },
        options: {
            responsive: true,
            tooltips: {
                mode: 'index',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    tipe: "time",
                    scaleLabel: {
                        display: true,
                        labelString: 'Dates'
                    },
                    time: {
                        displayFormats: {
                            'day': 'MMM DD',
                            'week': 'MMM DD',
                            'month': 'MMM DD',
                            'quarter': 'MMM DD',
                            'year': 'MMM DD',
                        }
                    },
                    id: "x-axis-1",
                    ticks:{
                        callback:function(label) {

                            var label = label.split("#")[0];
                            return label;

                        }
                    }
                },
                    {
                        display: true,
                        tipe: "time",
                        id: "x-axis-2",
                        ticks:{
                            callback:function(label) {

                                var label = label.split("#")[1];
                                return label;

                            }
                        }
                    }
                ],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Amounts'
                    },
                    ticks: {
                        callback: function(value, index, values) {
                            return value.toLocaleString("de-DE",{style:"currency", currency:"EUR"});
                        }
                    }
                }]
            }
        }

    });

}