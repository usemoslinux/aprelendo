/**
 * Copyright (C) 2019 Pablo Castagnino
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

$(document).ready(function() {
    resetControls(true);

    /**
     * Triggers when user clicks the upload button
     */
    $("#btn-upload-epub").on("click", function() {
        $("#url").trigger("click");
    }); // end #btn-upload-epub.on.click

    /**
     * Checks if selected file meets our requirements for upload
     * Triggers after user selects the file to upload
     */
    $("#url").on("change", function() {
        $("#alert-msg").addClass("d-none");

        var $epub_file = $(this);
        var file_name = $epub_file[0].files[0].name.split(".");
        var ext = file_name.pop().toLowerCase();

        if ($epub_file[0].files[0].size > 2097152) {
            showMessage(
                "This file is bigger than the allowed limit (2 MB). Please try again.",
                "alert-danger"
            );
            resetControls(true);
        } else if (ext != "epub") {
            showMessage(
                "Invalid file extension. Only .epub files are allowed.",
                "alert-danger"
            );
            resetControls(true);
        } else {
            if (window.FileReader) {
                var reader = new FileReader();
                reader.onload = openBook;
                reader.readAsArrayBuffer($epub_file[0].files[0]);
            }
        }
    }); // end #file-upload-epub.on.change

    /**
     * Adds ebook to database
     * This is triggered when user presses the "Save" button & submits the form
     * @param e {Event}
     */
    $("#form-addebook").on("submit", function(e) {
        e.preventDefault();
        var $progressbar = $("#upload-progress-bar");
        var form_data = new FormData(document.getElementById("form-addebook"));

        $('#alert-msg').addClass('d-none');

        $progressbar.parent().removeClass("d-none");
        $("#btn-upload-epub").addClass("disabled");
        $("#btn-save").addClass("disabled");
        $progressbar.width("33%");
        $progressbar.text("Uploading epub file...");

        // try to upload file
        $.ajax({
            type: "POST",
            url: "ajax/addtext.php",
            data: form_data,
            dataType: "json",
            contentType: false,
            processData: false
        })
            .done(function(data) {
                // in case of error while trying to upload, show error message
                if (data.error_msg != null) {
                    showMessage(data.error_msg, "alert-danger");
                    resetControls(false);
                }
                // if upload succeeds, validate epub file structure & integrity
                else {
                    $progressbar.width("66%");
                    $progressbar.text("Validating epub file...");

                    $.ajax({
                        type: "POST",
                        url: "ajax/validateepub.php",
                        data: { filename: data.filename }
                    })
                        // validate epub
                        .done(function(data) {
                            // if epub file fails integrity checks, show error message
                            if (data.error_msg != null) {
                                $progressbar.width("100%");
                                $progressbar
                                    .removeClass("progress-bar-success")
                                    .addClass("progress-bar-danger");
                                $progressbar.text(data.error_msg);
                                $progressbar
                                    .parent()
                                    .delay(4000)
                                    .fadeOut("slow", function() {
                                        window.location.replace("/texts");
                                    });
                            }
                            // if epub file has no errors...
                            else {
                                $progressbar.width("100%");
                                $progressbar.text("Upload complete...");
                                $progressbar
                                    .parent()
                                    .delay(1500)
                                    .fadeOut("slow", function() {
                                        window.location.replace("/texts");
                                    });
                            }
                        })
                        // if validation fails, show error message
                        .fail(function(xhr, ajaxOptions, thrownError) {
                            showMessage(
                                "Oops! There was an unexpected error uploading this text.",
                                "alert-danger"
                            );
                            resetControls(false);
                        });
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error uploading this text.",
                    "alert-danger"
                );
                resetControls(false);
            }); // end of ajax
    }); // end #form-addebook.on.submit

    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        $("#alert-msg")
            .html(html)
            .removeClass()
            .addClass("alert " + type);
        $(window).scrollTop(0);
    } // end showMessage

    /**
     * Empties form input fields
     */
    function resetControls(empty_values) {
        $("#upload-progress-bar")
            .parent()
            .addClass("d-none");
        $("#btn-upload-epub").removeClass("disabled");
        $("#btn-save").removeClass("disabled");
        if (empty_values) {
            $("#title").val("");
            $("#author").val("");
            $("#url").val("");
        }
    } // end resetControls

    /**
     * Opens and renders an ebook using epub.js
     * @param {arrayBuffer} e
     */
    function openBook(e) {
        var book = ePub();
        var bookData = e.target.result;
        book.open(bookData);

        book.loaded.metadata.then(function(meta) {
            var $title = document.getElementById("title");
            var $author = document.getElementById("author");

            if ($title != null) {
                $title.value = meta.title;
                $author.value = meta.creator;
            }
        });

        window.addEventListener("unload", function() {
            book.destroy();
        });
    } // end openBook
});
