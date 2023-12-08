    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        let alert = {
            'alert-success': {'title': 'Success', 'image': 'bi-check-circle-fill'},
            'alert-info': {'title': 'Information', 'image': 'bi-info-circle-fill'},
            'alert-warning': {'title': 'Careful', 'image': 'bi-exclamation-triangle-fill'},
            'alert-danger': {'title': 'Oops!', 'image': 'bi-exclamation-circle-fill'}
        }

        let title = '';
        let image = '';

        for (const key in alert) {
            if (key == type) {
                title = alert[key].title;
                image = alert[key].image;
                break;
            }
        }
        
        let div_flag_html = '<i class="bi ' + image + '"></i>' + title;
        let $div_flag = $("<div>").addClass("alert-flag fs-5").html(div_flag_html);
        let $div_msg = $("<div>").addClass("alert-msg").html(html);
        
        $("#alert-box")
            .empty()
            .removeClass()
            .addClass("alert " + type)
            .append($div_flag, $div_msg);
        $(window).scrollTop(0);
    } // end showMessage