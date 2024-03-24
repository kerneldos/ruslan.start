/**
 * Chart widgets
 */
$(function () {
    'use strict'

    $('.chart-container').each(function (i, elem) {
        const chartCanvas = $('canvas', elem)[0].getContext('2d');

        /**
         *
         * @type {*|jQuery}
         */
        const data = $('canvas', elem).data('chartData');

        /**
         *
         * @type {{datasets: [{spanGaps: boolean, pointBackgroundColor: string, borderColor: string, pointHoverRadius: number, data, borderWidth: number, fill: boolean, lineTension: number, pointRadius: number, pointColor: string}], labels}}
         */
        const chartData = {
            labels: data.labels,
            datasets: [
                {
                    fill: false,
                    borderWidth: 2,
                    lineTension: 0,
                    spanGaps: true,
                    borderColor: '#000',
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointColor: '#efefef',
                    pointBackgroundColor: '#efefef',
                    data: data.data
                }
            ]
        };

        const chartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        };

        // This will get the first returned node in the jQuery collection.
        // eslint-disable-next-line no-unused-vars
        new Chart(chartCanvas, { // lgtm[js/unused-local-variable]
            type: 'line',
            data: chartData,
            options: chartOptions
        })
    });
})