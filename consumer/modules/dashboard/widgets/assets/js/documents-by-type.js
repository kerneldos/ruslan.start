/**
 * docByType chart
 */
$(function () {
    'use strict'

    const docByTypeChartCanvas = document.getElementById('doc-by-type-chart-canvas').getContext('2d');

    /**
     *
     * @type {*|jQuery}
     */
    const data = $('#doc-by-type-chart-canvas').data('chartData');

    /**
     *
     * @type {{datasets: [{spanGaps: boolean, pointBackgroundColor: string, borderColor: string, pointHoverRadius: number, data, borderWidth: number, fill: boolean, lineTension: number, pointRadius: number, pointColor: string}], labels}}
     */
    const docByTypeChartData = {
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

    const docByTypeChartOptions = {
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
    new Chart(docByTypeChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: docByTypeChartData,
        options: docByTypeChartOptions
    })
})