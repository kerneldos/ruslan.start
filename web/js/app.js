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
})(jQuery)
