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
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/index">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">List of compatible dictionaries</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/ar.svg"
                                alt="Arabic">
                            <h4>Arabic</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ar.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://ar.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ar/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ar/</a></li>
                                <br>
                                The following have been reported not to work: <a
                                    href="https://www.almaany.com/">Almaany</a> (no modal support)
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/arabic-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/arabic-english/%s</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/aren/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/aren/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://mobile.pons.com"
                                    target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#ar/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#ar/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=ar&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=ar&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=ar-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=ar-en&text=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/zh.svg"
                                alt="Chinese">
                            <h4>Chinese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://zh.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://zh.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://zh.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://zh.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/zh/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/zh/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/chinese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/chinese-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/chinese-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/chinese-english/search?query=%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/zhen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/zhen/%s</a></li>
                                <small><i>To make them work in your
                                        native language make sure to replace "en" or "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name
                                        .</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://www.yellowbridge.com/"
                                    target="_blank" rel="noopener noreferrer">Yellow Bridge</a>
                                (no modal support), <a href="https://mobile.pons.com" target="_blank"
                                    rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#zh/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#zh/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=zh&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=zh&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=zh-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=zh-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#zh/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#zh/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/nl.svg"
                                alt="Dutch">
                            <h4>Dutch</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://nl.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://nl.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://nl.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://nl.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/nl/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/nl/</a></li>
                                <br>
                                The following have been reported not to work: <a href="https://www.vandale.nl/"
                                    target="_blank" rel="noopener noreferrer">Van Dale</a> (no modal support)
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/dutch-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/dutch-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/dutch-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/dutch-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/dutch-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/dutch-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/nlen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/nlen/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#nl/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#nl/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=nl&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=nl&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=nl-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=nl-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#nl/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#nl/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/en.svg"
                                alt="English">
                            <h4>
                                English
                            </h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://en.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.m.wiktionary.org/wiki/%s</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/definition/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definition/%s</a></li>
                                <li>Dictionary.com: <a href="https://www.dictionary.com/browse/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dictionary.com/browse/%s</a></li>
                                <li>The Free Dictionary: <a href="https://www.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.thefreedictionary.com/%s</a></li>
                                <li>Meriam Webster: <a href="https://www.merriam-webster.com/dictionary/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.merriam-webster.com/dictionary/%s</a></li>
                                <li>Cambridge Dictionary: <a href="https://dictionary.cambridge.org/us/dictionary/english/%s" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/us/dictionary/english/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/en/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/en/</a></li>
                                <li>Vocabulary.com: <a href="https://www.vocabulary.com/dictionary/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.vocabulary.com/dictionary/%s</a></li>
                                <li>Urban Dictionary: <a href="https://www.urbandictionary.com/define.php?term=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.urbandictionary.com/define.php?term=%s</a></li>
                                <li>Visuwords: <a href="https://visuwords.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://visuwords.com/%s</a></li>
                                <li>Google: <a href="https://www.google.com/search?q=define:%s" target="_blank"
                                        rel="noopener noreferrer">https://www.google.com/search?q=define:%s</a></li>
                                <br>
                                The following have been reported not to work: <a
                                    href="https://www.oxfordlearnersdictionaries.com" target="_blank"
                                    rel="noopener noreferrer">Oxford Learners</a> (no modal support), <a
                                    href="https://en.oxforddictionaries.com" target="_blank"
                                    rel="noopener noreferrer">Oxford compact</a> (no modal support), <a
                                    href="https://www.macmillandictionary.com" target="_blank"
                                    rel="noopener noreferrer">Macmillan</a> (no modal support), <a
                                    href="https://www.ldoceonline.com" target="_blank"
                                    rel="noopener noreferrer">Longman</a> (no modal support), <a
                                    href="https://www.collinsdictionary.com" target="_blank"
                                    rel="noopener noreferrer">Collins</a> (no modal support), <a
                                    href="https://www.wordnik.com" target="_blank" rel="noopener noreferrer">Wordnik</a>
                                (no modal support).
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/english-spanish/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/english-spanish/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/english-spanish/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/english-spanish/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/english-spanish/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/english-spanish/%s</a>
                                </li>
                                <li>Word Reference: <a
                                        href="https://www.wordreference.com/es/translation.asp?tranword=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/es/translation.asp?tranword=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "es" or
                                        "spanish" with the <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or
                                        name .</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#en/es/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#en/es/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=en&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=en&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=en-es&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=en-es&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#en/es/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#en/es/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "es" or
                                        "spanish" with the <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/fr.svg"
                                alt="French">
                            <h4>French</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://fr.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://fr.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://fr.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://fr.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/fr/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/fr/</a></li>
                                <br>
                                The following have been reported not to work: <a
                                    href="http://dictionnaire.sensagent.leparisien.fr" target="_blank"
                                    rel="noopener noreferrer">Le Parisien</a> (bad HTTPS certificate), <a
                                    href="https://www.larousse.fr/" target="_blank"
                                    rel="noopener noreferrer">Larousse</a> (no mobile version), <a
                                    href="http://www.le-dictionnaire.com/" target="_blank" rel="noopener noreferrer">Le
                                    Dictionnaire</a> (no HTTPS site).
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/french-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/french-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/french-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/french-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/french-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/french-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/fren/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/fren/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#fr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#fr/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=fr&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=fr&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=fr-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=fr-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#fr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#fr/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/de.svg"
                                alt="German">
                            <h4>German</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://de.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://de.m.wiktionary.org/wiki/%s</a></li>
                                <li>Duden: <a href="https://www.duden.de/rechtschreibung/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.duden.de/rechtschreibung/%s</a></li>
                                <li>DWDS: <a href="https://www.dwds.de/wb/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dwds.de/wb/%s</a></li>
                                <li>The Free Dictionary: <a href="https://de.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://de.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/de/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/de/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/german-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/german-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/german-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/german-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/german-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/german-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/deen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/deen/%s</a></li>
                                <li>Dict.cc: <a href="https://deen.dict.cc/?s=%s" target="_blank"
                                        rel="noopener noreferrer">https://deen.dict.cc/?s=%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#de/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#de/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=de&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=de&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=de-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=de-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#de/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#de/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/el.svg"
                                alt="Greek">
                            <h4>Greek</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://el.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://el.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://el.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://el.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/el/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/de/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/greek-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/greek-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/greek-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/greek-english/search?query=%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/?t=gr&set=_engr&w=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/?t=gr&set=_engr&w=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#el/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#el/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=el&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=el&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=el-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=el-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#el/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#el/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/he.svg"
                                alt="Hebrew">
                            <h4>Hebrew</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://he.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://he.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://he.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://he.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/he/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/he/</a></li>
                                <li>Milog: <a href="https://milog.co.il/%s" target="_blank"
                                        rel="noopener noreferrer">https://milog.co.il/%s</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Morfix: <a href="https://www.morfix.co.il/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.morfix.co.il/en/%s</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/hebrew-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/hebrew-english/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.glosbe.com/"
                                    target="_blank" rel="noopener noreferrer">Glosbe</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#he/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#he/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=he&to=en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=he&to=en&text=%s</a>
                                </li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=he-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=he-en&text=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/hi.svg"
                                alt="Hindi">
                            <h4>Hindi</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://hi.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://hi.m.wiktionary.org/wiki/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/hi/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/hi/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/hindi-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/hindi-english/%s</a></li>
                                <li>Bolti: <a href="https://www.boltidictionary.com/en/search?s=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.boltidictionary.com/en/search?s=%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://www.shabdkosh.com/"
                                    target="_blank" rel="noopener noreferrer">Shabdkosh</a>
                                (no modal support), <a href="https://en.glosbe.com/"
                                    target="_blank" rel="noopener noreferrer">Glosbe</a>
                                (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#hi/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#hi/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=hi&to=en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=hi&to=en&text=%s</a>
                                </li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=hi-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=hi-en&text=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/it.svg"
                                alt="Italian">
                            <h4>Italian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://it.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://it.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://it.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://it.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/it/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/it/</a></li>
                                <br>
                                The following have been reported not to work: <a
                                    href="http://www.treccani.it/vocabolario/">Trecanni</a> (no HTTPS site)
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/italian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/italian-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/italian-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/italian-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/italian-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/italian-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/iten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/iten/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#it/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#it/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=it&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=it&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=it-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=it-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#it/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#it/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/ja.svg"
                                alt="Japanese">
                            <h4>Japanese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ja.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ja.m.wiktionary.org/wiki/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ja/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ja/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/japanese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/japanese-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/japanese-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/japanese-english/search?query=%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/japanese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/japanese-english/%s</a></li>
                                <li>Jisho: <a href="https://jisho.org/search/%s" target="_blank"
                                        rel="noopener noreferrer">https://jisho.org/search/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://tangorin.com/"
                                    target="_blank" rel="noopener noreferrer">Tangorin</a>
                                (no modal support), <a href="https://dictionary.goo.ne.jp/"
                                    target="_blank" rel="noopener noreferrer">Goo</a>
                                (no modal support), <a href="https://en.glosbe.com/"
                                    target="_blank" rel="noopener noreferrer">Glosbe</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#ja/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#ja/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=ja&to=en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=ja&to=en&text=%s</a>
                                </li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=ja-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=ja-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#ja/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#ja/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/ko.svg"
                                alt="Korean">
                            <h4>Korean</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ko.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ko.m.wiktionary.org/wiki/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ko/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ko/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/korean-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/korean-english/%s</a></li>
                                <li>Naver: <a href="https://en.dict.naver.com/#/search?query=%s" target="_blank"
                                        rel="noopener noreferrer">https://en.dict.naver.com/#/search?query=%s</a></li>
                                <li>ZKorean: <a href="https://zkorean.com/dictionary/search_results?word=%s" target="_blank"
                                        rel="noopener noreferrer">https://zkorean.com/dictionary/search_results?word=%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.glosbe.com/"
                                    target="_blank" rel="noopener noreferrer">Glosbe</a>
                                (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#ko/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#ko/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=ko&to=en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=ko&to=en&text=%s</a>
                                </li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=ko-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=ko-en&text=%s</a>
                                </li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/pt.svg"
                                alt="Portuguese">
                            <h4>Portuguese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://pt.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://pt.m.wiktionary.org/wiki/%s</a></li>
                                <li>Priberam: <a href="https://dicionario.priberam.org/%s" target="_blank"
                                        rel="noopener noreferrer">https://dicionario.priberam.org/%s</a></li>
                                <li>Dicionário Online de Português: <a href="https://www.dicio.com.br/%s"
                                        target="_blank" rel="noopener noreferrer">https://www.dicio.com.br/%s</a></li>
                                <li>Infopédia: <a href="https://www.infopedia.pt/dicionarios/lingua-portuguesa/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.infopedia.pt/dicionarios/lingua-portuguesa/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://pt.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://pt.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/pt/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/pt/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/portuguese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/portuguese-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/portuguese-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/portuguese-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/portuguese-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/portuguese-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/pten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/pten/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#pt/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#pt/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=pt&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=pt&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=pt-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=pt-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#pt/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#pt/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/ru.svg"
                                alt="Russian">
                            <h4>Russian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ru.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ru.m.wiktionary.org/wiki/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ru/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ru/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/russian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/russian-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/russian-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/russian-english/search?query=%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/ruen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/ruen/%s</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/russian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/russian-english/%s</a></li>
                                <li>Openrussian.org: <a href="https://en.openrussian.org/ru/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.openrussian.org/ru/%s</a></li>
                                <li>Academic.ru: <a href="https://translate.academic.ru/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.academic.ru/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://www.russiandict.net/"
                                    target="_blank" rel="noopener noreferrer">Russiandict</a>
                                (no modal support), <a href="https://en.glosbe.com/"
                                    target="_blank" rel="noopener noreferrer">Glosbe</a>
                                (no modal support), <a href="https://mobile.pons.com" target="_blank"
                                    rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#ru/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#ru/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=ru&to=en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=ru&to=en&text=%s</a>
                                </li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=ru-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=ru-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#ru/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#ru/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-right" style="max-width:10%" src="/img/flags/es.svg"
                                alt="Spanish">
                            <h4>Spanish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://es.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://es.m.wiktionary.org/wiki/%s</a></li>
                                <li>Diccionario Real Academia Española: <a href="https://dle.rae.es/%s" target="_blank"
                                        rel="noopener noreferrer">https://dle.rae.es/%s</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/definicion/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definicion/%s</a></li>
                                <li>The Free Dictionary: <a href="https://es.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://es.thefreedictionary.com/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/es/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/es/</a></li>
                                <br>
                                The following have been reported not to work: <a
                                    href="https://es.oxforddictionaries.com/" target="_blank"
                                    rel="noopener noreferrer">Oxford Dictionaries</a> (no modal support).
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/spanish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.bab.la/dictionary/spanish-english/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/spanish-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/spanish-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/spanish-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/spanish-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/esen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/esen/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                                <br><br>
                                The following have been reported not to work: <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (no modal support).
                            </ul>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#es/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#es/en/%s</a></li>
                                <li>Bing Translator: <a href="https://www.bing.com/translator/?from=es&to=en&text=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.bing.com/translator/?from=es&to=en&text=%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=es-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=es-en&text=%s</a>
                                </li>
                                <li>DeepL: <a href="https://www.deepl.com/translator#es/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#es/en/%s</a></li>
                                <small><i>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</i></small>
                            </ul>
                        </section>
                        <br>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>