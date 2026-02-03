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

use Aprelendo\Dictionaries;


if (empty($error_msg)) {
    echo '<div id="alert-box" class="d-none"></div>';
} else {
    echo '<div id="alert-box" class="alert alert-danger">' . $error_msg .'</div>';
}

$dictionaries = new Dictionaries($pdo, $user->native_lang, $lang->name);
$monolingual_dics = $dictionaries->getMonolingual(false);
$bilingual_dics = $dictionaries->getBilingual(false);
$visual_dics = $dictionaries->getVisual();
$translators = $dictionaries->getTranslators();

?>

<form id="form-editlanguage" method="post">
    <input type="hidden" name="id" value="<?php echo $lang->id; ?>">
    <input type="hidden" name="language" class="form-control" value="<?php echo $lang->name; ?>">
    <fieldset>
        <div class="card">
            <div class="card-header">Default dictionaries & Translator</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="dict-uri">Default dictionary URI:</label>
                    <div class="dict-select input-group mb-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-lightning-fill"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Monolingual</h6></li>
                            <?php
                                $html = '';
                                foreach ($monolingual_dics as $monolingual_dic) {
                                    $html .= '<li><a class="dropdown-item" href="#" ';
                                    $html .= 'value="' . $monolingual_dic['uri'] . '">';
                                    $html .= $monolingual_dic['name'];
                                    $html .= '</a></li>';
                                }
                                echo $html;
                            ?>
                            <li><h6 class="dropdown-header">Bilingual</h6></li>
                            <?php
                                $html = '';
                                foreach ($bilingual_dics as $bilingual_dic) {
                                    $html .= '<li><a class="dropdown-item" href="#" ';
                                    $html .= 'value="' . $bilingual_dic['uri'] . '">';
                                    $html .= $bilingual_dic['name'];
                                    $html .= '</a></li>';
                                }
                                echo $html;
                            ?>
                        </ul>
                        <input type="url" id="dict-uri" name="dict-uri" class="form-control"
                            value="<?php echo htmlspecialchars($lang->dictionary_uri); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="img-dict-uri">Default visual dictionary URI:</label>
                    <div class="dict-select input-group mb-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-lightning-fill"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                                $html = '';
                                foreach ($visual_dics as $visual_dic) {
                                    $html .= '<li><a class="dropdown-item" href="#" ';
                                    $html .= 'value="' . $visual_dic['uri'] . '">';
                                    $html .= $visual_dic['name'];
                                    $html .= '</a></li>';
                                }
                                echo $html;
                            ?>
                        </ul>
                        <input type="url" id="img-dict-uri" name="img-dict-uri" class="form-control"
                            value="<?php echo htmlspecialchars($lang->img_dictionary_uri); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="translator-uri">Default translator URI:</label>
                    <div class="dict-select input-group mb-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-lightning-fill"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                                $html = '';
                                foreach ($translators as $translator) {
                                    $html .= '<li><a class="dropdown-item" href="#" ';
                                    $html .= 'value="' . $translator['uri'] . '">';
                                    $html .= $translator['name'];
                                    $html .= '</a></li>';
                                }
                                echo $html;
                            ?>
                        </ul>
                        <input type="url" id="translator-uri" name="translator-uri" class="form-control"
                            value="<?php echo htmlspecialchars($lang->translator_uri); ?>">
                    </div>
                </div>
                <div class="text-end">
                    <a href="javascript:;" title="Help" data-bs-toggle="collapse" data-bs-target="#help-dictionary">Help
                        <span class="bi bi-question-circle"></span></a>
                </div>

                <div id="help-dictionary" class="collapse small">
                    <hr>
                    <ul>
                        <li>The dictionaries and translators that can be loaded with the
                            <kbd><i class="bi bi-lightning-fill"></i></kbd> button are merely indicative.
                            You can load any other dictionary you want. To do so, perform a search in your favorite
                            dictionary. Then copy and paste the URL here replacing the search term with "%s"
                            (without quotation marks).
                        </li>
                        <li>For further assistance, we've compiled a list of
                            <a href="/exampledics" target="_blank" rel="noopener noreferrer">popular dictinaries</a>
                            you can use and how to load them here.
                        </li>
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
