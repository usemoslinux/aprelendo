<?php
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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Includes\Classes\Reader;

$reader = new Reader($pdo, $user->getId(), $user->getLangId());
$prefs = $reader->getPrefs();

$font_family = $prefs->getFontFamily();
$font_size = $prefs->getFontSize();
$line_height = $prefs->getLineHeight();
$text_align = $prefs->getTextAlignment();
$display_mode = $prefs->getDisplayMode();
$assisted_learning = $prefs->getAssistedLearning();

$video_pages = array('showvideo', 'showofflinevideo');
$is_video_page = in_array(basename($_SERVER['PHP_SELF']), $video_pages);

?>


<!-- Reader Settings Modal -->
<div class="modal fade" id="reader-settings-modal" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="reader-settings-modal-label"
    aria-hidden="true" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reader-settings-modal-label">Reader preferences</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="prefs-modal-form" method="post">
                    <div class="form-group">
                        <label for="fontfamily">Font Family:</label>
                        <div>
                            <select name="fontfamily" id="fontfamily" class="form-control custom-select"
                                autocomplete="off">
                                <option value="Arial, sans-serif" <?php echo $font_family=='Arial, sans-serif' ? ' selected ' : ''; ?>>Arial</option>
                                <option value="Courier, monospace" <?php echo $font_family=='Courier, monospace' ? ' selected ' : ''; ?>>Courier</option>
                                <option value="Georgia, serif" <?php echo $font_family=='Georgia, serif' ? ' selected ' : ''; ?>>Georgia</option>
                                <option value="Roboto, sans-serif" <?php echo $font_family=='Roboto, sans-serif' ? ' selected ' : ''; ?>>Roboto</option>
                                <option value="Times New Roman, serif" <?php echo $font_family=='Times New Roman, serif' ? ' selected ' : ''; ?>>Times New Roman</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fontsize">Font Size:</label>
                        <div>
                            <select name="fontsize" id="fontsize" class="form-control custom-select" autocomplete="off">
                                <option value="12pt" <?php echo $font_size == '12pt' ? ' selected ' : ''; ?>>12 pt
                                </option>
                                <option value="14pt" <?php echo $font_size == '14pt' ? ' selected ' : ''; ?>>14 pt
                                </option>
                                <option value="16pt" <?php echo $font_size == '16pt' ? ' selected ' : ''; ?>>16 pt
                                </option>
                                <option value="18pt" <?php echo $font_size == '18pt' ? ' selected ' : ''; ?>>18 pt
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group <?php echo $is_video_page ? 'd-none' : ''; ?> ">
                        <label for="lineheight">Line height:</label>
                        <div>
                            <select name="lineheight" id="lineheight" class="form-control custom-select"
                                autocomplete="off">
                                <option value="1.5" <?php echo $line_height == '1.5' ? ' selected ' : ''; ?>>1.5 Lines
                                </option>
                                <option value="2" <?php echo $line_height == '2' ? ' selected ' : ''; ?>>2</option>
                                <option value="2.5" <?php echo $line_height == '2.5' ? ' selected ' : ''; ?>>2.5
                                </option>
                                <option value="3" <?php echo $line_height == '3' ? ' selected ' : ''; ?>>3</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group <?php echo $is_video_page ? 'd-none' : ''; ?> ">
                        <label for="alignment">Text alignment:</label>
                        <div>
                            <select name="alignment" id="alignment" class="form-control custom-select"
                                autocomplete="off">
                                <option value="left" <?php echo $text_align == 'left' ? ' selected ' : ''; ?>>Left
                                </option>
                                <option value="center" <?php echo $text_align == 'center' ? ' selected ' : ''; ?>>Center
                                </option>
                                <option value="right" <?php echo $text_align == 'right' ? ' selected ' : ''; ?>>Right
                                </option>
                                <option value="justify" <?php echo $text_align == 'justify' ? ' selected ' : ''; ?>>
                                    Justify</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mode">Display mode:</label>
                        <div>
                            <select name="mode" id="mode" class="form-control custom-select" autocomplete="off">
                                <option value="light" <?php echo $display_mode == 'light' ? ' selected ' : ''; ?>>Light
                                </option>
                                <option value="sepia" <?php echo $display_mode == 'sepia' ? ' selected ' : ''; ?>>Sepia
                                </option>
                                <option value="dark" <?php echo $display_mode == 'dark' ? ' selected ' : ''; ?>>Dark
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group d-none">
                        <label for="assistedlearning">Mode:</label>
                        <div>
                            <select name="assistedlearning" id="assistedlearning" class="form-control custom-select">
                                <option value="1" <?php echo $assisted_learning == true ? ' selected ' : ''; ?>>Assisted
                                </option>
                                <option value="0" <?php echo $assisted_learning == false ? ' selected ' : ''; ?>>Free
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button id="btn-save-reader-prefs" type="button" class="btn btn-primary" data-dismiss="modal">Save
                    changes</button>
            </div>
        </div>
    </div>
</div>

<script defer src="js/showreadersettingsmodal-min.js"></script>