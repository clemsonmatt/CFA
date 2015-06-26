$(function() {

    /* get the json data */
    var chartData = [];
    $.each($.parseJSON($('#menu-chart').attr('data-chart')), function(key, value) {
        chartData.push([key, value]);
    });

    PieChart.chart(chartData);

});


var PieChart = {
    chart: function(chartData) {
        $('#menu-chart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, sans-serif'
                },
                margin: [0, 0, 0, 0],
                spacingTop: 0,
                spacingBottom: 0,
                spacingLeft: 0,
                spacingRight: 0,
            },
            title: false,
            tooltip: {
                // formatter: function() {
                //     return '<b>' + this.y + '</b> '
                // },
                pointFormat: '{point.y} purchased; <b>{point.percentage:.1f}%</b>'
            },
            legend: false,
            plotOptions: {
                pie: {
                    size:'100%',
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        distance: -65,
                        // format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        format: '{point.name}',
                        color: 'white',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                            //width: 200
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Menu Breakdown',
                data: chartData
            }],
            colors: ['#4572A7', '#AA4643', '#5F5F5F', '#80699B', '#3D96AE', '#DB843D', '#92A8CD', '#A47D7C', '#B5CA92'],
            credits: {
                enabled: false
            },
        });
    }
}
