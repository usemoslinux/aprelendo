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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Reader;

$reader = new Reader($pdo, $user->id, $user->lang_id);
$prefs = $reader->prefs;

$font_family = $prefs->font_family;
$font_size = $prefs->font_size;
$line_height = $prefs->line_height;
$text_align = $prefs->text_alignment;
$display_mode = $prefs->display_mode;
$assisted_learning = $prefs->assisted_learning;

$video_pages = ['showvideo', 'showofflinevideo'];
$is_video_page = in_array(basename($_SERVER['PHP_SELF']), $video_pages);

$sel = ' selected ';
?>


<!-- Reader Settings Modal -->
<div class="modal fade" id="reader-settings-modal" data-keyboard="true" tabindex="-1"
    aria-labelledby="reader-settings-modal-label" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reader-settings-modal-label">Reader preferences</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="prefs-modal-form" method="post">
                    <div class="mb-3">
                        <label for="fontfamily">Font Family:</label>
                        <div>
                            <select name="fontfamily" id="fontfamily" class="form-control form-select"
                                autocomplete="off">
                                <option value="var(--bs-body-font-family)" <?php echo $font_family=="var(--bs-body-font-family)"
                                    ? $sel                                     : ''; ?>>
                                    System default
                                </option>
                                <option value="Arial, sans-serif" <?php echo $font_family=='Arial, sans-serif'
                                    ? $sel                                     : ''; ?>>
                                    Arial
                                </option>
                                <option value="Courier, monospace" <?php echo $font_family=='Courier, monospace'
                                    ? $sel                                     : ''; ?>>
                                    Courier
                                </option>
                                <option value="Georgia, serif" <?php echo $font_family=='Georgia, serif'
                                    ? $sel                                     : ''; ?>>
                                    Georgia
                                </option>
                                <option value="Roboto, sans-serif" <?php echo $font_family=='Roboto, sans-serif'
                                    ? $sel                                     : ''; ?>>
                                    Roboto
                                </option>
                                <option value="Times New Roman, serif" <?php echo $font_family=='Times New Roman, serif'
                                    ? $sel                                     : ''; ?>>
                                    Times New Roman
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fontsize">Font Size:</label>
                        <div>
                            <select name="fontsize" id="fontsize" class="form-control form-select" autocomplete="off">
                                <option value="12pt" <?php echo $font_size == '12pt' ? $sel : ''; ?>>12 pt
                                </option>
                                <option value="14pt" <?php echo $font_size == '14pt' ? $sel : ''; ?>>14 pt
                                </option>
                                <option value="16pt" <?php echo $font_size == '16pt' ? $sel : ''; ?>>16 pt
                                </option>
                                <option value="18pt" <?php echo $font_size == '18pt' ? $sel : ''; ?>>18 pt
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 <?php echo $is_video_page ? 'd-none' : ''; ?> ">
                        <label for="lineheight">Line height:</label>
                        <div>
                            <select name="lineheight" id="lineheight" class="form-control form-select"
                                autocomplete="off">
                                <option value="1.5" <?php echo $line_height == '1.5' ? $sel : ''; ?>>1.5 Lines
                                </option>
                                <option value="2" <?php echo $line_height == '2' ? $sel : ''; ?>>2</option>
                                <option value="2.5" <?php echo $line_height == '2.5' ? $sel : ''; ?>>2.5
                                </option>
                                <option value="3" <?php echo $line_height == '3' ? $sel : ''; ?>>3</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 <?php echo $is_video_page ? 'd-none' : ''; ?> ">
                        <label for="alignment">Text alignment:</label>
                        <div>
                            <select name="alignment" id="alignment" class="form-control form-select" autocomplete="off">
                                <option value="left" <?php echo $text_align == 'left' ? $sel : ''; ?>>Left
                                </option>
                                <option value="center" <?php echo $text_align == 'center' ? $sel : ''; ?>>Center
                                </option>
                                <option value="right" <?php echo $text_align == 'right' ? $sel : ''; ?>>Right
                                </option>
                                <option value="justify" <?php echo $text_align == 'justify' ? $sel : ''; ?>>
                                    Justify</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mode">Display mode:</label>
                        <div>
                            <select name="mode" id="mode" class="form-control form-select" autocomplete="off">
                                <option value="light" <?php echo $display_mode == 'light' ? $sel : ''; ?>>Light
                                </option>
                                <option value="sepia" <?php echo $display_mode == 'sepia' ? $sel : ''; ?>>Sepia
                                </option>
                                <option value="dark" <?php echo $display_mode == 'dark' ? $sel : ''; ?>>Dark
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 d-none">
                        <label for="assistedlearning">Mode:</label>
                        <div>
                            <select name="assistedlearning" id="assistedlearning" class="form-control form-select">
                                <option value="1" <?php echo $assisted_learning ? $sel : ''; ?>>Assisted
                                </option>
                                <option value="0" <?php echo !$assisted_learning ? $sel : ''; ?>>Free
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                <button id="btn-save-reader-prefs" type="button" class="btn btn-primary" data-bs-dismiss="modal">Apply changes</button>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/showreadersettingsmodal.min.js"></script>
