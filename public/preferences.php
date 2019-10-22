<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Includes\Classes\Reader;

$reader = new Reader($con, $user->getId(), $user->getLangId());
?>

    <div class="container mtb">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="texts.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="active">Preferences</a>
                    </li>
                </ol>
                <div class="row">
                    <div class="col-12">
                        <div id="msgbox"></div>
                        <form id="prefs-form" method="post">
                            <div class="card">
                                <div class="card-header">Reader</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="fontfamily">Font Family:</label>
                                        <div>
                                            <select name="fontfamily" id="fontfamily" class="form-control custom-select">
                                                <option value="Helvetica" <?php echo $reader->getFontFamily()=='Helvetica' ? ' selected ' : ''; ?>>Helvetica</option>
                                                <option value="Open Sans" <?php echo $reader->getFontFamily()=='Open Sans' ? ' selected ' : ''; ?>>Open Sans</option>
                                                <option value="Times New Roman" <?php echo $reader->getFontFamily()=='Times New Roman' ? ' selected ' : ''; ?>>Times New Roman</option>
                                                <option value="Georgia" <?php echo $reader->getFontFamily()=='Georgia' ? ' selected ' : ''; ?>>Georgia</option>
                                                <option value="Lato" <?php echo $reader->getFontFamily()=='Lato' ? ' selected ' : ''; ?>>Lato</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="fontsize">Font Size:</label>
                                        <div>
                                            <select name="fontsize" id="fontsize" class="form-control custom-select">
                                                <option value="12pt" <?php echo $reader->getFontSize()=='12pt' ? ' selected ' : ''; ?>>12 pt</option>
                                                <option value="14pt" <?php echo $reader->getFontSize()=='14pt' ? ' selected ' : ''; ?>>14 pt</option>
                                                <option value="16pt" <?php echo $reader->getFontSize()=='16pt' ? ' selected ' : ''; ?>>16 pt</option>
                                                <option value="18pt" <?php echo $reader->getFontSize()=='18pt' ? ' selected ' : ''; ?>>18 pt</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="lineheight">Line height:</label>
                                        <div>
                                            <select name="lineheight" id="lineheight" class="form-control custom-select">
                                                <option value="1.5" <?php echo $reader->getLineHeight()=='1.5' ? ' selected ' : ''; ?>>1.5 Lines</option>
                                                <option value="2" <?php echo $reader->getLineHeight()=='2' ? ' selected ' : ''; ?>>2</option>
                                                <option value="2.5" <?php echo $reader->getLineHeight()=='2.5' ? ' selected ' : ''; ?>>2.5</option>
                                                <option value="3" <?php echo $reader->getLineHeight()=='3' ? ' selected ' : ''; ?>>3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="alignment">Text alignment:</label>
                                        <div>
                                            <select name="alignment" id="alignment" class="form-control custom-select">
                                                <option value="left" <?php echo $reader->getTextAlign()=='left' ? ' selected ' : ''; ?>>Left</option>
                                                <option value="center" <?php echo $reader->getTextAlign()=='center' ? ' selected ' : ''; ?>>Center</option>
                                                <option value="right" <?php echo $reader->getTextAlign()=='right' ? ' selected ' : ''; ?>>Right</option>
                                                <option value="justify" <?php echo $reader->getTextAlign()=='justify' ? ' selected ' : ''; ?>>Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="mode">Display mode:</label>
                                        <div>
                                            <select name="mode" id="mode" class="form-control custom-select">
                                                <option value="light" <?php echo $reader->getDisplayMode()=='light' ? ' selected ' : ''; ?>>Light</option>
                                                <option value="sepia" <?php echo $reader->getDisplayMode()=='sepia' ? ' selected ' : ''; ?>>Sepia</option>
                                                <option value="dark" <?php echo $reader->getDisplayMode()=='dark' ? ' selected ' : ''; ?>>Dark</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="card">
                                <div class="card-header">
                                    Learning
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="assistedlearning">Mode:</label>
                                        <div>
                                            <select name="assistedlearning" id="assistedlearning" class="form-control custom-select">
                                                <option value="1" <?php echo $reader->getAssistedLearning()==true ? ' selected ' : ''; ?>>Assisted</option>
                                                <option value="0" <?php echo $reader->getAssistedLearning()==false ? ' selected ' : ''; ?>>Free</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="javascript:;" title="Help" data-toggle="collapse" data-target="#help-learning-mode">
                                            <i class="far fa-question-circle"></i>
                                        </a>
                                    </div>
                                    <div id="help-learning-mode" class="collapse small">
                                        <hr>
                                        <p>
                                            Assisted mode is designed to aid you in your language learning process. It typically consists of 4 phases:
                                        </p>
                                        <ol>
                                            <li>Reading: try to understand what the text is about. If you see words or phrases that you don&#39;t understand, look them up in the built-in dictionary.</li>
                                            <li>Listening: listen to the recording and pay attention to the different sounds.</li>
                                            <li>Speaking: speak on top of the recording, trying to imitate the pronunciation of each word. You can reduce the speed of the recording if necessary.</li>
                                            <li>Dictation: type the words you marked for learning as they are spoken.</li>
                                        </ol>
                                        Remember: assisted mode only works for simple texts (articles, lyrics, conversation transcripts, etc.), not videos or ebooks.
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="text-right">
                                <button id="cancelbtn" name="cancel" type="button" class="btn btn-link" onclick="window.location='/'">Cancel</button>
                                <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="js/preferences.js"></script>
    <?php require_once 'footer.php'; ?>