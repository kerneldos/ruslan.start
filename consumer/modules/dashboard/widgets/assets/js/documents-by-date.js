/**
 * docByDate chart
 */
$(function () {
    'use strict'

    const docByDateChartCanvas = document.getElementById('doc-by-date-chart-canvas').getContext('2d');

    /**
     *
     * @type {*|[*]|jQuery}
     */
    const chartData = $('#doc-by-date-chart-canvas').data('chartData');

    /**
     *
     * @type {{datasets: [{backgroundColor: string, borderColor: string, pointStrokeColor: string, data: (jQuery.data|*), pointHighlightStroke: string, pointHighlightFill: string, pointRadius: boolean, pointColor: string}], labels: (function(): (*))}}
     */
    const docByDateChartData = {
        labels: chartData.labels,
        datasets: [
            {
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: chartData.data,
            }
        ]
    };

    /**
     *
     * @type {{legend: {display: boolean}, responsive: boolean, scales: {yAxes: [{gridLines: {display: boolean}}], xAxes: [{gridLines: {display: boolean}}]}, maintainAspectRatio: boolean}}
     */
    const docByDateChartOptions = {
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
    new Chart(docByDateChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: docByDateChartData,
        options: docByDateChartOptions
    })
})