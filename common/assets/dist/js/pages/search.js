$(function () {
    'use strict'

    //Infinite scroll search page
    $('.list-view').infiniteScroll({
        // options
        path: '.next .page-link',
        append: '.list-group-item',
        history: false,
        prefill: true,
        fetchOptions: {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
    });
})