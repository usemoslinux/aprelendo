    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        let alert = {
            'alert-success': {'title': 'Success', 'image': 'fa-circle-check'},
            'alert-info': {'title': 'Information', 'image': 'fa-circle-info'},
            'alert-warning': {'title': 'Careful', 'image': 'fa-triangle-exclamation'},
            'alert-danger': {'title': 'Oops!', 'image': 'fa-circle-exclamation'}
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
        
        let div_flag_html = '<i class="fa-solid ' + image + '"></i>' + title;
        let $div_flag = $("<div>").addClass("alert-flag fs-5").html(div_flag_html);
        let $div_msg = $("<div>").addClass("alert-msg").html(html);
        
        $("#alert-box")
            .empty()
            .removeClass()
            .addClass("alert " + type)
            .append($div_flag, $div_msg);
        $(window).scrollTop(0);
    } // end showMessage