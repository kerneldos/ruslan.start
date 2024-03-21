/**
 * jQuery UI sortable for the todo list
 */
$(function () {
    'use strict'

    $('.todo-list').sortable({
        placeholder: 'sort-highlight',
        handle: '.handle',
        forcePlaceholderSize: true,
        zIndex: 999999,
    })
})