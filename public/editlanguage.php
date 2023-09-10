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
require_once APP_ROOT . 'includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

if (empty($error_msg)) {
    echo '<div id="alert-msg" class="d-none"></div>';
} else {
    echo '<div id="alert-msg" class="alert alert-danger">' . $error_msg .'</div>';
}

?>

<form id="form-editlanguage" method="post">
    <input type="hidden" name="id" value="<?php echo $lang->id; ?>">
    <input type="hidden" name="language" class="form-control" value="<?php echo $lang->name; ?>">
    <fieldset>
        <div class="card">
            <div class="card-header">Dictionary & Translator</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="dict-uri">Dictionary URI:</label>
                    <input type="url" id="dict-uri" name="dict-uri" class="form-control"
                        value="<?php echo htmlspecialchars($lang->dictionary_uri); ?>">
                </div>
                <div class="mb-3">
                    <label for="translator-uri">Translator URI:</label>
                    <input type="url" id="translator-uri" name="translator-uri" class="form-control"
                        value="<?php echo htmlspecialchars($lang->translator_uri); ?>">
                </div>
                <div class="text-end">
                    <a href="javascript:;" title="Help" data-bs-toggle="collapse" data-bs-target="#help-dictionary">Help
                        <span class="far fa-question-circle"></span></a>
                </div>

                <div id="help-dictionary" class="collapse small">
                    <hr>
                    <p>
                        URLs should meet the following requirements to work properly:
                    </p>
                    <ul>
                        <li>Some websites ensure that their content is not embedded into other sites. Therefore, some
                            dictionaries may not work with Aprelendo. For further help, check the list of <a
                                href="/compatibledics">compatible dictionaries</a>.</li>
                        <li>For security reasons, only HTTPS websites are supported. Make sure you use URL addresses
                            that start with HTTPS, not HTTP.</li>
                        <li>As the dictionary is going to be shown inside a modal window, it is highly recommended to
                            use websites that support smaller screens. In case that support is not automatic, look for
                            the mobile version of that website (if there is one) and use that one instead.</li>
                        <li>Don't forget to indicate the position of the lookup phrase by using "%s" (without quotation
                            marks).</li>
                    </ul>
                </div>
            </div>
        </div>
    </fieldset>
    <br>
    <fieldset>
        <div class="card">
            <div class="card-header">Learning level</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="level">Show texts of this level by default:</label>
                    <select name="level" id="level" class="form-control form-select">
                        <option value="0" <?php echo $lang->level == 0 ? 'selected' : ''; ?>>All</option>
                        <option value="1" <?php echo $lang->level == 1 ? 'selected' : ''; ?>>Beginner</option>
                        <option value="2" <?php echo $lang->level == 2 ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="3" <?php echo $lang->level == 3 ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
            </div>
        </div>
    </fieldset>
    <br>
    <fieldset>
        <div class="card">
            <div class="card-header">RSS feeds</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="rss-feed1-uri">RSS feed URI 1:</label>
                    <input type="url" id="rss-feed1-uri" name="rss-feed1-uri" class="form-control"
                        value="<?php echo htmlspecialchars($lang->rss_feed1_uri); ?>">
                </div>
                <div class="mb-3">
                    <label for="rss-feed2-uri">RSS feed URI 2:</label>
                    <input type="url" id="rss-feed2-uri" name="rss-feed2-uri" class="form-control"
                        value="<?php echo htmlspecialchars($lang->rss_feed2_uri); ?>">
                </div>
                <div class="mb-3">
                    <label for="rss-feed3-uri">RSS feed URI 3:</label>
                    <input type="url" id="rss-feed3-uri" name="rss-feed3-uri" class="form-control"
                        value="<?php echo htmlspecialchars($lang->rss_feed3_uri); ?>">
                </div>
            </div>
        </div>
    </fieldset>
    <br>
    <fieldset>
        <div class="card">
            <div class="card-header">Frequency list</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="show-freq-words">Underline high frequency words:</label>
                    <select name="show-freq-words" id="show-freq-words" class="form-control form-select">
                        <option value="1" <?php echo $lang->show_freq_words ? 'selected' : ''; ?>>Yes</option>
                        <option value="0" <?php echo !$lang->show_freq_words ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
            </div>
        </div>
    </fieldset>
    <br>
    <div class="text-end">
        <button id="cancelbtn" name="cancel" type="button" class="btn btn-link"
            onclick="window.location='texts'">Cancel</button>
        <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
    </div>
</form>
