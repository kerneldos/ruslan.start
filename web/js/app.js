/**
 * Main app js
 */

/* eslint-disable camelcase */

(function ($) {
    'use strict'

    //Initialize Select2 Elements
    $('.select2').select2();

    $('.js-tags').on('click', function () {
        var $values = $('.select2').val();
        $values.push($(this).attr('data-value'));

        $('.select2').val($values);
        $('.select2').trigger('change');

        $('.js-submit').trigger('click');

        return false;
    });

    $('.select').click(function() {
        $('.tree>ul').toggle();

        if (!$('.tree>ul').is(':hidden')) {
            $('.tree>ul').trigger('focusin');
        }
    });

    $(document).mouseup(function(e) {
        var container = $('.tree>ul');

        // If the target of the click isn't the container
        if(!container.is(e.target) && !$(e.target).is('.select')) {
            container.slideUp();
        }
    });

    $('.tree a').click(function() {
        var option = $(this).attr('data-value');

        $('input', '.select').val(option);
        $('span', '.select').text($(this).text());

        $('.tree>ul').slideUp();

        console.log($('input', '.select').val())

        return false;
    });

    bsCustomFileInput.init();

    $('.js-content-search').select2({
        minimumInputLength: 2,
        tags: [],
        ajax: {
            url: '/site/search',
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            data: function (query) {
                return {
                    DocumentSearch: {content: query.term}
                };
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            text: item.name,
                            id: item.name
                        }
                    })
                };
            },
            results: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { text: item.name, id: item.name }
                    })
                };
            }
        }
    });
})(jQuery)
