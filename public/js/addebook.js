/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    var book = ePub();
    emptyForm();

    $("#btn-upload-epub").on("click", function () {
        $("#url").trigger("click");
    });

    $('#url').on('change', function () {
        $('#alert-error-msg').addClass('hidden');

        var $epub_file = $(this);
        var file_name = $epub_file[0].files[0].name.split('.');
        var ext = file_name.pop().toLowerCase();

        if ($epub_file[0].files[0].size > 2097152) {
            showError('This file is bigger than the allowed limit (2 MB). Please try again.');
            $epub_file.val('');
        } else if (ext != 'epub') {
            showError('Invalid file extension. Only .epub files are allowed.');
            $epub_file.val('');
        } else {
            if ($('#title').val() == '') {
                if (window.FileReader) {
                    var reader = new FileReader();
                    reader.onload = openBook;
                    reader.readAsArrayBuffer($epub_file[0].files[0]);
                }
            }
        }
    }); // end of #file-upload-epub.on.change


    /**
     * Adds ebook to database
     * This is triggered when user presses the "Save" button & submits the form
     */
    $('#form-addebook').on('submit', function (e) {
        e.preventDefault();
        var $progressbar = $('#upload-progress-bar');
        var form_data = new FormData(document.getElementById("form-addebook"));

        $progressbar.parent().removeClass('hidden');
        $('#btn-upload-epub').addClass('disabled');
        $('#btn-save').addClass('disabled');
        $progressbar.width('33%');
        $progressbar.text('Uploading epub file...')

        $.ajax({
                type: "POST",
                url: "ajax/addtext.php",
                data: form_data,
                dataType: 'json',
                contentType: false,
                processData: false
            })
            .done(function (data) {
                if (typeof data.error_msg !== 'undefined' && data.error_msg.length != 0) {
                    showError(data.error_msg);
                } else {
                    $progressbar.width('66%');
                    $progressbar.text('Validating epub file structure...');

                    $.ajax({
                            type: "POST",
                            url: "ajax/validateepub.php",
                            data: {'filename': data.filename} 
                        })
                        .done(function (data) {
                            if (typeof data.error_msg !== 'undefined' && data.error_msg.length != 0) {
                                $progressbar.width('100%');
                                $progressbar.removeClass('progress-bar-success').addClass('progress-bar-danger');
                                $progressbar.text(data.error_msg);
                                $progressbar.parent().delay(4000).fadeOut('slow', function () {
                                    window.location.replace('texts.php');
                                });
                            } else {
                                $progressbar.width('100%');
                                $progressbar.text('Upload complete...');
                                $progressbar.parent().delay(1500).fadeOut('slow', function () {
                                    window.location.replace('texts.php');
                                });
                            }
                        })
                        .fail(function (xhr, ajaxOptions, thrownError) {
                            showError('Oops2! There was an unexpected error uploading this text.');
                        });
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showError('Oops1! There was an unexpected error uploading this text.');
            }); // end of ajax
    }); // end of #form-addebook.on.submit


    /**
     * Shows custom error message in the top section of the screen
     * @param {string} error_msg 
     */
    function showError(error_msg) {
        $('#upload-progress-bar').parent().addClass('hidden');
        $('#alert-error-msg').html(error_msg)
            .removeClass('hidden')
            .addClass('alert alert-danger');
        $(window).scrollTop(0);
    } // end of showError


    /**
     * Empties form input fields
     */
    function emptyForm() {
        $('#alert-error-msg').addClass('hidden');
        $('#title').val('');
        $('#author').val('');
        $('#url').val('');
    }

    /**
     * Opens and renders an ebook using epub.js
     * @param {arrayBuffer} e 
     */
    function openBook(e) {
        var bookData = e.target.result;
        book.open(bookData);

        window.addEventListener("unload", function () {
            book.destroy();
        });

        book.loaded.metadata.then(function (meta) {
            var $title = document.getElementById("title");
            var $author = document.getElementById("author");

            if ($title != null) {
                $title.value = meta.title;
                $author.value = meta.creator;
            }
        });
    }







});