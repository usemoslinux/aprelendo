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


const WordSelection = (() => {
    let $selword = null;
    
    function setupEvents({
        actionBtns,        // TextActionBtns or VideoActionBtns
        controller,        // AudioController or VideoController
        linkBuilder        // LinkBuilder.forTranslationInText or LinkBuilder.forTranslationInVideo
    }) {
        let has_long_pressed = false;
        let start_index = -1;
        let current_index = -1;
        let start_x = null;
        let start_y = null;
        let long_press_timer = null;
    
        const $doc = $(parent.document);
    
        // Disables right-click context menu
        $doc.on("contextmenu", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Only process right-clicks on words inside #text
            if ($(e.target).is("#text .word") && e.pointerType === 'mouse') {
                controller.pause(false);
                $selword = $(e.target);
                const base_uris = Dictionaries.getURIs();
                openInNewTab(linkBuilder(base_uris.translator, $selword));
                cancelLongPress();
            }
            return false;
        });
    
        // On word click, assume single word selection
        $doc.on('click', '#text .word', function (e) {
            if (!has_long_pressed) {
                $selword = $(this);
                TextHighlighter.removeAll();
                $selword.addClass('highlighted');
                actionBtns.hide();
                controller.pause(true);
                actionBtns.show($selword);
            }
        });
    
        // On word mouse/touch down, potential start of selection
        $doc.on('mousedown touchstart', '#text .word', function (e) {
            start_index = TextProcessor.getAnchorIndex($(this));
        });
    
        // Bind container mouse word selection events
        $doc.on('mousedown', '#text .word', function (e) {
            startLongPress(e);
        });
    
        $doc.on('mousemove', '#text .word', function (e) {
            onPointerMove(e);
        });
    
        $doc.on('mouseup', '#text .word', function (e) {
            onPointerUp();
        });
    
        // Bind container touch word selection events
        $doc.on('touchstart', '#text .word', function (e) {
            const touch = e.originalEvent.touches[0];
            startLongPress(touch);
        });
    
        $doc.on('touchmove', '#text .word', function (e) {
            const touch = e.originalEvent.touches[0];
            onPointerMove(touch);
        });
    
        $doc.on('touchend', '#text .word', function (e) {
            onPointerUp();
        });
    
        // Removes selection when user clicks in white-space
        $doc.on("mouseup touchend", function (e) {
            const $action_btns = $("#action-buttons");
            if ($action_btns.is(':visible')) {
                const is_word_clicked = $(e.target).is(".word");
                const is_btn_clicked = $(e.target).closest('.btn').length > 0;
                const is_navigation = $(e.target).closest('.offcanvas').length > 0;
                const is_modal = $(e.target).closest('.modal').length > 0;
                if (!is_word_clicked && !is_btn_clicked && !is_navigation && !is_modal) {
                    e.stopPropagation();
                    TextHighlighter.removeAll();
                    actionBtns.hide();
                    controller.resume();
                }
            }
        });
    
        function startLongPress(e) {
            has_long_pressed = false;
            start_x = e.pageX;
            start_y = e.pageY;
            long_press_timer = setTimeout(function () {
                has_long_pressed = true;
                controller.pause(true);
            }, 500);
        }
    
        function cancelLongPress() {
            clearTimeout(long_press_timer);
            start_x = null;
            start_y = null;
        }
    
        function pointerMovedEnough(e) {
            if (start_x == null || start_y == null) return false;
            const threshold = 10;
            const dx = e.pageX - start_x;
            const dy = e.pageY - start_y;
            return (Math.abs(dx) > threshold || Math.abs(dy) > threshold);
        }
    
        function onPointerMove(e) {
            if (pointerMovedEnough(e)) {
                cancelLongPress();
            }
            if (has_long_pressed) {
                highlightCurrent(e);
            }
        }
    
        function highlightCurrent(e) {
            if (!has_long_pressed || start_index < 0) return;
            const el = document.elementFromPoint(e.clientX, e.clientY);
            const $target_anchor = $(el).closest('a');
            if ($target_anchor.length) {
                current_index = TextProcessor.getAnchorIndex($target_anchor);
                TextHighlighter.removeAll();
                TextHighlighter.addSelection(start_index, current_index);
            }
        }
    
        function onPointerUp() {
            if (has_long_pressed && start_index >= 0 && current_index >= 0) {
                const start_obj_parent = TextProcessor.getAnchorsList().eq(start_index).parent()[0];
                const current_obj_parent = TextProcessor.getAnchorsList().eq(current_index).parent()[0];
                if (start_obj_parent === current_obj_parent) {
                    $selword = TextHighlighter.getSelection(start_index, current_index);
                    actionBtns.show($selword);
                }
            }
            cancelLongPress();
            has_long_pressed = false;
            start_index = -1;
            current_index = -1;
        }
    }

    return {
        setupEvents,
        get: () => $selword,
    };
})();

