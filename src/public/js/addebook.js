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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
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
        $("#alert-box").addClass("d-none");

        const $epub_file = $(this);
        const file_name = $epub_file[0].files[0].name.split(".");
        const ext = file_name.pop().toLowerCase();

        if (ext != "epub") {
            showMessage(
                "Invalid file extension. Only .epub files are allowed.",
                "alert-danger"
            );
            resetControls(true);
        } else if (window.FileReader) {
            const reader = new FileReader();
            reader.onload = openBook;
            reader.readAsArrayBuffer($epub_file[0].files[0]);
        }
    }); // end #file-upload-epub.on.change

    /**
     * Adds ebook to database
     * This is triggered when user presses the "Save" button & submits the form
     * @param e {Event}
     */
    $("#form-addebook").on("submit", async function(e) {
        e.preventDefault();
        const $progressbar = $("#upload-progress-bar");
        const form_data = new FormData(document.getElementById("form-addebook"));
        const audio_uri = $("#audio-uri").val();

        try {
            $('#alert-box').addClass('d-none');

            // validate audio URL
            if (audio_uri != "" && !isValidHttpUrl(audio_uri)) {
                throw new Error("Invalid audio URL.");
            }

            // show progress bar & disable buttons
            $progressbar.parent().removeClass("d-none");
            $("#btn-upload-epub").addClass("disabled");
            $("#btn-save").addClass("disabled");
            $progressbar.width("33%");
            $progressbar.text("Uploading & converting ebook...");

            const response = await fetch("ajax/addtext.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to add ebook');                
            }

            $progressbar.width("100%");
            $progressbar.text("Upload complete...");
            setTimeout(() => {
                window.location.replace("/texts");
            }, 1500);
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
            resetControls(false);
        }
    }); // end #form-addebook.on.submit

    /**
     * Checks if audio URL is a Google Drive link
     */
    $('#audio-uri').on('input', function() {
        const audio_url = $(this).val();
        const help_text = $('#audio-url-helptext');
    
        if (audio_url.includes('drive.google.com')) {
            help_text.html('<i class="bi bi-cloud-fill"></i> Remember to <a href="https://support.google.com/drive/answer/2494822?hl=en&co=GENIE.Platform%3DDesktop#zippy=%2Cshare-a-file-publicly" target="_blank" rel="noopener noreferrer" class="alert-link">share this file publicly</a>, allowing access to anyone with the link.');
        } else {
            help_text.text('Accepts URLs from YouTube, Google Drive, M3U playlists, RSS feeds, or any standard audio source.');
        }
    }); // end #audio-url.on.input

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
        const book = ePub();
        const bookData = e.target.result;
        book.open(bookData);

        book.loaded.metadata.then(function(meta) {
            const $title = document.getElementById("title");
            const $author = document.getElementById("author");

            if ($title != null) {
                $title.value = meta.title;
                $author.value = meta.creator;
            }
        });

        window.addEventListener("unload", function() {
            book.destroy();
        });
    } // end openBook

    function isValidHttpUrl(string) {
        let url;
        try {
            url = new URL(string);
        } catch (_) {
            return false;
        }
        return url.protocol === "http:" || url.protocol === "https:";
    }
});
