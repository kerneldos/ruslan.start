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

    // DropzoneJS Demo Code Start
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the document template HTML and remove it from the document
    var previewNode = document.querySelector("#template")
    if (previewNode) {
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/files/upload", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            uploadMultiple: true,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
        })

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
        })

        myDropzone.on("sending", function(file) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1"
            // And disable the start button
            file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
        })

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            document.querySelector("#total-progress").style.opacity = "0"
        })

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector("#actions .start").onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
        }
        document.querySelector("#actions .cancel").onclick = function() {
            myDropzone.removeAllFiles(true)
        }
    }
    // DropzoneJS Demo Code End
})(jQuery)
