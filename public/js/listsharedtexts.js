$(document).ready(function () {
    $('i.fa-heart').on('click', function () {
        var like_btn = $(this);
        var text_id = like_btn.attr('data-idText');
        $.ajax({
            type: "POST",
            url: "db/togglelike.php",
            data: {id: text_id}
            // dataType: "dataType"
        })
        .done(function (data) {
            if (data.error_msg) {
                alert('error!');
            } else {
                like_btn.toggleClass('fas far');
                var total_likes = parseInt(like_btn.siblings('small').text());
                if (like_btn.hasClass('fas')) {
                    like_btn.siblings('small').text(total_likes+1);      
                } else {
                    like_btn.siblings('small').text(total_likes-1);  
                }
            }
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
        });
        
    });
});