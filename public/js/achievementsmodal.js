$(document).ready(function () {
    create_achievements_modal();
});

function create_achievements_modal() {
    $(".modal").each(function () {
        var pages = $(this).find('.modal-split');

        if (pages.length > 1) {
            pages.hide();
            pages.eq(0).show();

            var b_button = document.createElement("button");
            b_button.setAttribute("type", "button");
            b_button.setAttribute("class", "btn btn-primary");
            b_button.setAttribute("style", "display: none;");
            b_button.innerHTML = "Back";

            var n_button = document.createElement("button");
            n_button.setAttribute("type", "button");
            n_button.setAttribute("class", "btn btn-primary");
            n_button.innerHTML = "Next";

            $(this).find('.modal-footer').append(b_button).append(n_button);

            var page_track = 0;

            $(n_button).click(function () {
                if (page_track == 0) {
                    $(b_button).show();
                }

                if (page_track == pages.length - 2) {
                    $(n_button).text("Close")
                               .removeClass("btn-primary")
                               .addClass("btn-secondary");
                }

                if (page_track < pages.length - 1) {
                    page_track++;

                    pages.hide();
                    pages.eq(page_track).show();
                } else {
                    $("#modal-achievements").modal('hide');
                }
            });

            $(b_button).click(function () {
                if (page_track == 1) {
                    $(b_button).hide();
                }

                if (page_track == pages.length - 1) {
                    $(n_button).text("Next")
                               .removeClass("btn-secondary")
                               .addClass("btn-primary")
                }

                if (page_track > 0) {
                    page_track--;

                    pages.hide();
                    pages.eq(page_track).show();
                }
            });
        } else {
            var c_button = document.createElement("button");
            c_button.setAttribute("type", "button");
            c_button.setAttribute("class", "btn btn-secondary");
            c_button.innerHTML = "Close";

            $(this).find('.modal-footer').append(c_button);

            $(c_button).click(function() {
                $("#modal-achievements").modal('hide');
            });
        }
    });
}