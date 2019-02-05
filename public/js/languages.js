$(document).ready(function () {
    $(".btn-link").on("click", function () {
        $sel_card = $(".fas", this);

        $sel_card.toggleClass("fa-chevron-right")
                 .toggleClass("fa-chevron-down");

        $('.fas', '#accordion').each(function () {
            if ($(this).hasClass('fa-chevron-down') && $(this)[0] !== $sel_card[0]) {
                $(this).toggleClass("fa-chevron-right")
                       .toggleClass("fa-chevron-down");
            }
        });
    });
});