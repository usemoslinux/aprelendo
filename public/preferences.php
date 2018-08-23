<?php
    require_once('header.php');
    require_once(PUBLIC_PATH . '/classes/reader.php'); // load Reader class

    $reader = new Reader($con, $user->id, $user->learning_lang_id);
?>

    <div class="container mtb">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li>
                        <a href="texts.php">Home</a>
                    </li>
                    <li>
                        <a class="active">Preferences</a>
                    </li>
                </ol>
                <div class="row flex">
                    <div class="col-xs-12">
                        <div id="msgbox"></div>
                        <form id="prefs-form" class="form-horizontal" action="" method="post">
                            <div class="panel panel-default">
                                <div class="panel-heading">Reader</div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="fontfamily" class="control-label col-sm-2">Font Family:</label>
                                        <div class="col-sm-10">
                                            <select name="fontfamily" id="fontfamily" class="form-control">
                                                <option value="Helvetica" <?php echo $reader->font_family=='Helvetica' ? ' selected ' : ''; ?>>Helvetica</option>
                                                <option value="Open Sans" <?php echo $reader->font_family=='Open Sans' ? ' selected ' : ''; ?>>Open Sans</option>
                                                <option value="Times New Roman" <?php echo $reader->font_family=='Times New Roman' ? ' selected ' : ''; ?>>Times New Roman</option>
                                                <option value="Georgia" <?php echo $reader->font_family=='Georgia' ? ' selected ' : ''; ?>>Georgia</option>
                                                <option value="Lato" <?php echo $reader->font_family=='Lato' ? ' selected ' : ''; ?>>Lato</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="fontsize" class="control-label col-sm-2">Font Size:</label>
                                        <div class="col-sm-10">
                                            <select name="fontsize" id="fontsize" class="form-control">
                                                <option value="12pt" <?php echo $reader->font_size=='12pt' ? ' selected ' : ''; ?>>12 pt</option>
                                                <option value="14pt" <?php echo $reader->font_size=='14pt' ? ' selected ' : ''; ?>>14 pt</option>
                                                <option value="16pt" <?php echo $reader->font_size=='16pt' ? ' selected ' : ''; ?>>16 pt</option>
                                                <option value="18pt" <?php echo $reader->font_size=='18pt' ? ' selected ' : ''; ?>>18 pt</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="lineheight" class="control-label col-sm-2">Line height:</label>
                                        <div class="col-sm-10">
                                            <select name="lineheight" id="lineheight" class="form-control">
                                                <option value="1.5" <?php echo $reader->line_height=='1.5' ? ' selected ' : ''; ?>>1.5 Lines</option>
                                                <option value="2" <?php echo $reader->line_height=='2' ? ' selected ' : ''; ?>>2</option>
                                                <option value="2.5" <?php echo $reader->line_height=='2.5' ? ' selected ' : ''; ?>>2.5</option>
                                                <option value="3" <?php echo $reader->line_height=='3' ? ' selected ' : ''; ?>>3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="alignment" class="control-label col-sm-2">Text alignment:</label>
                                        <div class="col-sm-10">
                                            <select name="alignment" id="alignment" class="form-control">
                                                <option value="left" <?php echo $reader->text_align=='left' ? ' selected ' : ''; ?>>Left</option>
                                                <option value="center" <?php echo $reader->text_align=='center' ? ' selected ' : ''; ?>>Center</option>
                                                <option value="right" <?php echo $reader->text_align=='right' ? ' selected ' : ''; ?>>Right</option>
                                                <option value="justify" <?php echo $reader->text_align=='justify' ? ' selected ' : ''; ?>>Justify</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="mode" class="control-label col-sm-2">Display mode:</label>
                                        <div class="col-sm-10">
                                            <select name="mode" id="mode" class="form-control">
                                                <option value="light" <?php echo $reader->display_mode=='light' ? ' selected ' : ''; ?>>Light</option>
                                                <option value="sepia" <?php echo $reader->display_mode=='sepia' ? ' selected ' : ''; ?>>Sepia</option>
                                                <option value="dark" <?php echo $reader->display_mode=='dark' ? ' selected ' : ''; ?>>Dark</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Learning
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="assistedlearning" class="control-label col-sm-2">Mode:</label>
                                        <div class="col-sm-10">
                                            <select name="assistedlearning" id="assistedlearning" class="form-control">
                                                <option value="1" <?php echo $reader->assisted_learning==true ? ' selected ' : ''; ?>>Assisted</option>
                                                <option value="0" <?php echo $reader->assisted_learning==false ? ' selected ' : ''; ?>>Free</option>
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
                                            Assisted mode is designed to aid you in your language learning process. It typically consists of 3 phases:
                                        </p>
                                        <ol>
                                            <li>First read (skimming and general comprehension of what the text is about) + listening
                                                (only if audio is available).</li>
                                            <li>Second read (to acquire a deeper understanding of the text) + opportunity to
                                                look up words in the dictionary or use the translator.</li>
                                            <li>Dictation (only if audio is available).</li>
                                        </ol>
                                        Remember: assisted mode only works for simple texts (articles, songs, conversation transcripts, etc.), not videos or ebooks.
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <a type="button" id="cancelbtn" name="cancel" class="btn btn-static" onclick="window.location='/'">Cancel</a>
                                <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/preferences.js"></script>
    <?php require_once('footer.php') ?>