// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    create_achievements_modal();
});

function create_achievements_modal() {
    $(".modal").each(function () {
        const pages = $(this).find('.modal-split');

        if (pages.length > 1) {
            pages.addClass('d-none');
            pages.eq(0).removeClass('d-none');

            let b_button = document.createElement("button");
            b_button.setAttribute("type", "button");
            b_button.setAttribute("class", "btn btn-primary");
            b_button.setAttribute("style", "display: none;");
            b_button.innerHTML = "Back";

            let n_button = document.createElement("button");
            n_button.setAttribute("type", "button");
            n_button.setAttribute("class", "btn btn-primary");
            n_button.innerHTML = "Next";

            $(this).find('.modal-footer').append(b_button).append(n_button);

            let page_track = 0;

            $(n_button).on("click", function () {
                if (page_track == 0) {
                    $(b_button).removeClass('d-none');
                }

                if (page_track == pages.length - 2) {
                    $(n_button).text("Close")
                               .removeClass("btn-primary")
                               .addClass("btn-secondary");
                }

                if (page_track < pages.length - 1) {
                    page_track++;

                    pages.addClass('d-none');
                    pages.eq(page_track).removeClass('d-none');
                } else {
                    $("#modal-achievements").modal('hide');
                }
            });

            $(b_button).on("click", function () {
                if (page_track == 1) {
                    $(b_button).addClass('d-none');
                }

                if (page_track == pages.length - 1) {
                    $(n_button).text("Next")
                               .removeClass("btn-secondary")
                               .addClass("btn-primary")
                }

                if (page_track > 0) {
                    page_track--;

                    pages.addClass('d-none');
                    pages.eq(page_track).removeClass('d-none');
                }
            });
        } else {
            let c_button = document.createElement("button");
            c_button.setAttribute("type", "button");
            c_button.setAttribute("class", "btn btn-secondary");
            c_button.innerHTML = "Close";

            $(this).find('.modal-footer').append(c_button);

            $(c_button).on("click", function() {
                $("#modal-achievements").modal('hide');
            });
        }
    });
}