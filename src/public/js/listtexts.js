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
    $("#search").focus();
    $("input:checkbox").prop("checked", false);

    if ($('#modal-achievements').length) {
        $('#modal-achievements').modal('show');
    }

    $('form').submit(function(e) {
        e.preventDefault();

        reloadPage();
    });

    /**
     * Deletes selected texts from the database
     * Trigger: when user selects "Delete" in the global or individual action menus
     */
    $("#mDelete, .imDelete").on("click", function() {
        if (confirm("Really delete?")) {
            let ids = [];

            if ($(this).attr("id") === "mDelete") {
                $("input.chkbox-selrow:checked").each(function() {
                    ids.push($(this).attr("data-idText"));
                });
            } else if ($(this).hasClass("imDelete")) {
                ids.push($(this).closest('tr').find('input').attr("data-idText"));
            }

            if (ids.length === 0) {
                return;
            }

            const uri_params = getCurrentURIParameters();
            const is_archived = uri_params.sa == "1";

            $.ajax({
                url: "ajax/removetext.php",
                type: "POST",
                data: {
                    textIDs: JSON.stringify(ids),
                    is_archived: is_archived ? 1 : 0
                }
            })
                .done(function() {
                    reloadPage();
                })
                .fail(function() {
                    alert(
                        "There was an error when trying to delete the selected texts. Refresh the page and try again."
                    );
                });
        }
    }); // end #mDelete.on.click

    /**
     * Archives selected texts
     * Trigger: when user selects "Archive" in the global or individual action menus
     */
    $("#mArchive, .imArchive").on("click", function() {
        const archivetxt = $(this).text().trim() === "Archive";
        let ids = [];

        if ($(this).attr("id") === "mArchive") {
            $("input.chkbox-selrow:checked").each(function() {
                ids.push($(this).attr("data-idText"));
            });    
        } else if ($(this).hasClass("imArchive")) {
            ids.push($(this).closest('tr').find('input').attr("data-idText"));
        }

        if (ids.length === 0) {
            return;
        }

        $.ajax({
            url: "ajax/archivetext.php",
            type: "POST",
            data: {
                textIDs: JSON.stringify(ids),
                archivetext: archivetxt
            }
        })
            .done(function() {
                reloadPage();
            })
            .fail(function() {
                alert(
                    "There was an error when trying to archive the selected texts. Refresh the page and try again."
                );
            }); // end ajax
    }); // end mArchive.on.click

    /**
     * Shares selected text
     * Trigger: when user selects "Share" in the individual action menu
     */
    $(".imShare").on("click", function() {
        if (confirm("Sharing this text is irreversible. Once shared, it cannot be made private again. Are you sure you want to proceed?")) {
            let id = $(this).closest('tr').find('input').attr("data-idText");

            if (id === undefined) {
                return;
            }

            $.ajax({
                url: "ajax/sharetext.php",
                type: "POST",
                data: {
                    textID: id,
                }
            })
                .done(function(data) {
                    if (data.error_msg != null) {
                        alert(data.error_msg)
                    } else {
                        reloadPage();
                    }
                })
                .fail(function() {
                    alert(
                        "There was an error when trying to share the selected text. Refresh the page and try again."
                    );
                }); // end ajax    
        }
    }); // end imShare.on.click

    /**
     * Edits selected text
     * Trigger: when user selects "Edit" in the individual action menu
     */
    $(".imEdit").on("click", function() {
        let id = $(this).closest('tr').find('input').attr("data-idText");

        if (id === undefined) {
            return;
        }

        window.location.href = "addtext?id=" + encodeURIComponent(id);
    }); // end imEdit.on.click

    /**
     * Reloads current page passing the correct URI parameters
     */
    function reloadPage() {
        const filename = getCurrentFileName();
        const uri_params = getCurrentURIParameters();
        const cur_page_nr = uri_params.p ? uri_params.p : "1";

        const params = {    
            p: cur_page_nr,
            ft: $('.ft.active').data('value') || 0, // filter type
            fl: $('.fl.active').data('value') || 0, // filter level
            s: $("#s").val().trim(),                // search text
            sa: $('.sa').hasClass('active') ? '1' : '0', // is shared
            o: $('.o.active').data('value') || 0    // order
        };

        const uri_str = buildQueryString(params);
        window.location.replace(filename + uri_str);
    }

    /**
     * Enables/Disables action menu based on the number of selected elements.
     * If there is at least 1 element selected, it enables it. Otherwise, it is disabled.
     */
    function toggleActionMenu() {
        if ($("input[type=checkbox]:checked").length === 0) {
            $("#actions-menu").addClass("disabled");
        } else {
            $("#actions-menu").removeClass("disabled");
        }
    } // end toggleActionMenu

    $(document).on("change", ".chkbox-selrow", toggleActionMenu);

    /**
     * Selects/Unselects all texts from the list
     */
    $(document).on("click", "#chkbox-selall", function(e) {
        e.stopPropagation();

        const $chkboxes = $(".chkbox-selrow");
        $chkboxes.prop("checked", $(this).prop("checked"));

        toggleActionMenu();
    }); // end #chkbox-selall.on.click

    /**
     * Shows selection in Filter menu
     */
    $("#btn-filter + div > a").on("click", function() {
        const $item = $(this);

        if ($item.is('.sa')) {
            $item.toggleClass("active");
        } else {
            $item.addClass("active");

            let filter = '';
            if ($item.is('.ft')) {
                filter = '.ft';
            } else if ($item.is('.fl')) {
                filter = '.fl';
            }

            $item.siblings(".active" + filter).each(function() {
                $(this).toggleClass("active");
            });
        }

        $('form').submit();
    }); // end #btn-filter + div > a.on.click

    /**
     * Selects sorting
     */
    $("#dropdown-menu-sort .o").on("click", function(e) {
        const filename = getCurrentFileName();
        const params = {    ft: $('.ft.active').data('value') || 0, // filter type
                            fl: $('.fl.active').data('value') || 0, // filter level
                            s: $("#s").val().trim(),                // search text
                            sa: $('.sa').hasClass('active') ? '1' : '0', // is shared
                            o: $(this).data('value') || 0    // order
                        };

        const uri_str = buildQueryString(params);
        window.location.replace(filename + uri_str);
    }); // end #dropdown-menu-sort.on.click

    /**
     * Turn off clicking for disabled navigation items
     */
    $(document).on("click", "li.disabled", function() {
        return false;
    }); // end li.disabled.on.click

    /**
     * Hides welcome message for this and future sessions
     */
    $("#welcome-close").on("click", function(e) {
        e.preventDefault();

        setCookie("hide_welcome_msg", true, 365 * 10);
    }); // end .close.on.click
});
