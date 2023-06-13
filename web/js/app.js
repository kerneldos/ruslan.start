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
})(jQuery)
