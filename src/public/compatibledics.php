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
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

if (!$user_auth->isLoggedIn()) {
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
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ar.svg"
                                alt="Arabic">
                            <h4>Arabic</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ar.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://ar.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.thefreedictionary.com/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ar-ar/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ar-ar/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ar/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ar/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.almaany.com/" target="_blank"
                                        rel="noopener noreferrer">Almaany</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/arabic-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/arabic-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/arabic-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/arabic-english/%s</a> (English
                                    only)</li>
                                <li>Word Reference: <a href="https://www.wordreference.com/aren/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/aren/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ar-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ar-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://en.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://ar.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/bg.svg"
                                alt="Bulgarian">
                            <h4>Bulgarian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://bg.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://bg.m.wiktionary.org/wiki/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/bg-bg/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/bg-bg/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/bg/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/bg/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/bulgarian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/bulgarian-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/bg-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/bg-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://bg.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ca.svg"
                                alt="Catalan">
                            <h4>Catalan</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ca.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ca.m.wiktionary.org/wiki/%s</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/definicio/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definicio/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ca/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ca/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.diccionari.cat" target="_blank" rel="noopener noreferrer">Gran
                                        Diccionari de la Llengua Catalana</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/catalan-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/catalan-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://ca.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com/" target="_blank"
                                        rel="noopener noreferrer">PONS</a> (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/zh.svg"
                                alt="Chinese">
                            <h4>Chinese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://zh.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://zh.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://zh.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://zh.thefreedictionary.com/%s</a>
                                </li>
                                <li>ZDic: <a href="https://www.zdic.net/hans/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.zdic.net/hans/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/zh-zh/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/zh-zh/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/zh/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/zh/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/chinese-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/chinese-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/chinese-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/chinese-english/%s</a>
                                </li>
                                <li>Naver: <a
                                        href="https://english.dict.naver.com/english-chinese-dictionary/#/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://english.dict.naver.com/english-chinese-dictionary/#/search?query=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/zh-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/zh-en/</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/zhen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/zhen/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/chinese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/chinese-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your
                                        native language make sure to replace "en" or "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name
                                        .</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.yellowbridge.com/" target="_blank"
                                        rel="noopener noreferrer">Yellow Bridge</a> (no modal support)
                                </li>
                                <li><a href="https://zh.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/hr.svg"
                                alt="Croatian">
                            <h4>Croatian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://hr.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://hr.m.wiktionary.org/wiki/%s</a></li>
                                <li>Rjecnik.hr: <a href="https://rjecnik.hr/search.php?q=%s" target="_blank"
                                        rel="noopener noreferrer">https://rjecnik.hr/search.php?q=%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hr-hr/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hr-hr/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/hr/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/hr/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/croatian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/croatian-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hr-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hr-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://hr.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/cs.svg"
                                alt="Czech">
                            <h4>Czech</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://cs.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://cs.m.wiktionary.org/wiki/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/cs-cs/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/cs-cs/</a></li>
                                <li>Institute for Czech language:
                                    <a href="https://ssjc.ujc.cas.cz/search.php?hledej=Hledat&heslo=%s&sti=EMPTY&where=hesla&hsubstr=no"
                                        target="_blank" rel="noopener noreferrer">
                                        https://ssjc.ujc.cas.cz/search.php?hledej=Hledat&heslo=%s&sti=EMPTY&where=hesla&hsubstr=no
                                    </a>
                                </li>

                                <li>Internetová jazyková příručka: <a href="https://prirucka.ujc.cas.cz/?slovo=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://prirucka.ujc.cas.cz/?slovo=%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/cs/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/cs/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/czech-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/czech-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/cs-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/cs-en/</a></li>
                                <li>Reverso: <a href="https://www.reverso.net/text-translation#sl=cze&tl=eng&text=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.reverso.net/text-translation#sl=cze&tl=eng&text=%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/czen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/czen/%s</a> (English
                                    only)
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://slovnik.seznam.cz/" target="_blank"
                                        rel="noopener noreferrer">Seznam</a> (no modal support)
                                </li>
                                <li><a href="https://cs.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://cs.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/da.svg"
                                alt="Danish">
                            <h4>Danish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://da.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://da.m.wiktionary.org/wiki/%s</a></li>
                                <li>Ord: <a href="https://www.ord.dk/oversaet/dansk/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.ord.dk/oversaet/dansk/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/da-da/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/da-da/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/da/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/da/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://sproget.dk/" target="_blank"
                                        rel="noopener noreferrer">Sproget.dk</a> (no modal support)
                                </li>
                                <li><a href="https://ordnet.dk/" target="_blank" rel="noopener noreferrer">Den Danske
                                        Ordbog</a> (no modal support)
                                </li>

                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/danish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/danish-english/%s</a> (English
                                    only)</li>
                                <li>Reverso: <a href="https://www.reverso.net/text-translation#sl=dan&tl=eng&text=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.reverso.net/text-translation#sl=dan&tl=eng&text=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/da-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/da-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://da.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/nl.svg"
                                alt="Dutch">
                            <h4>Dutch</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://nl.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://nl.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://nl.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://nl.thefreedictionary.com/%s</a>
                                </li>
                                <li>Woorden: <a href="https://www.woorden.org/woord/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.woorden.org/woord/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/nl-nl/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/nl-nl/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/nl/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/nl/</a>
                                </li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.vandale.nl/" target="_blank" rel="noopener noreferrer">Van
                                        Dale</a> (no modal support)</li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/dutch-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/dutch-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/dutch-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/dutch-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/dutch-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/dutch-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/nl-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/nl-en/</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/nlen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/nlen/%s</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://nl.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://nl.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/en.svg"
                                alt="English">
                            <h4>
                                English
                            </h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://en.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.m.wiktionary.org/wiki/%s</a></li>
                                <li>Britannica Dictionary: <a href="https://www.britannica.com/dictionary/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.britannica.com/dictionary/%s</a></li>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/definition/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definition/%s</a></li>
                                <li>Dictionary.com: <a href="https://www.dictionary.com/browse/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dictionary.com/browse/%s</a></li>
                                <li>The Free Dictionary: <a href="https://www.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.thefreedictionary.com/%s</a></li>
                                <li>Meriam Webster: <a href="https://www.merriam-webster.com/dictionary/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.merriam-webster.com/dictionary/%s</a></li>
                                <li>Cambridge Dictionary: <a
                                        href="https://dictionary.cambridge.org/us/dictionary/english/%s" target="_blank"
                                        rel="noopener noreferrer">
                                        https://dictionary.cambridge.org/us/dictionary/english/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/en-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/en-en/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/en/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/en/</a></li>
                                <li>Vocabulary.com: <a href="https://www.vocabulary.com/dictionary/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.vocabulary.com/dictionary/%s</a></li>
                                <li>Urban Dictionary: <a href="https://www.urbandictionary.com/define.php?term=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.urbandictionary.com/define.php?term=%s</a>
                                <li>Thesaurus.com: <a href="https://www.thesaurus.com/browse/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.thesaurus.com/browse/%s</a></li>
                                </li>
                                <li>Visuwords: <a href="https://visuwords.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://visuwords.com/%s</a></li>
                                <li>YourDictionary: <a href="https://www.yourdictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.yourdictionary.com/%s</a></li>
                                </li>
                                <li>Google: <a href="https://www.google.com/search?q=define:%s" target="_blank"
                                        rel="noopener noreferrer">https://www.google.com/search?q=define:%s</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.oxfordlearnersdictionaries.com" target="_blank"
                                        rel="noopener noreferrer">Oxford Learners</a> (no modal support)
                                </li>
                                <li><a href="https://en.oxforddictionaries.com" target="_blank"
                                        rel="noopener noreferrer">Oxford compact</a> (no modal support)
                                </li>
                                <li><a href="https://www.macmillandictionary.com" target="_blank"
                                        rel="noopener noreferrer">Macmillan</a> (no modal support)
                                </li>
                                <li><a href="https://www.ldoceonline.com" target="_blank"
                                        rel="noopener noreferrer">Longman</a> (no modal support)
                                </li>
                                <li><a href="https://www.collinsdictionary.com" target="_blank"
                                        rel="noopener noreferrer">Collins</a> (no modal support)
                                </li>
                                <li><a href="https://www.wordnik.com" target="_blank"
                                        rel="noopener noreferrer">Wordnik</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/english-spanish/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/english-spanish/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/spanish-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/spanish-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/spanish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/spanish-english/%s</a> (English
                                    only)</li>
                                <li>Word Reference: <a
                                        href="https://www.wordreference.com/es/translation.asp?tranword=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.wordreference.com/es/translation.asp?tranword=%s</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "es" or
                                        "spanish" with the <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or
                                        name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://dictionary.cambridge.org/dictionary/" target="_blank"
                                        rel="noopener noreferrer">Cambridge dictionary</a> (no modal support)
                                </li>
                                <li><a href="https://en.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://en.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/fr.svg"
                                alt="French">
                            <h4>French</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://fr.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://fr.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://fr.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://fr.thefreedictionary.com/%s</a>
                                </li>
                                <li>Le Parisien: <a href="https://dictionnaire.sensagent.com/%s/fr-fr/" target="_blank"
                                        rel="noopener noreferrer">https://dictionnaire.sensagent.com/%s/fr-fr/</a>
                                </li>
                                <li>Le Dictionnaire: <a href="https://www.le-dictionnaire.com/definition/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.le-dictionnaire.com/definition/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/fr-fr/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/fr-fr/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/fr/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/fr/</a>
                                </li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.larousse.fr/" target="_blank"
                                        rel="noopener noreferrer">Larousse</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/french-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/french-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/french-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/french-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/french-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/french-english/%s</a> (English
                                    only)</li>
                                <li>Word Reference: <a href="https://www.wordreference.com/fren/%s" target="_blank"
                                        rel="noopener noreferrer">
                                        https://www.wordreference.com/fren/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/fr-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/fr-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://fr.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://fr.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/de.svg"
                                alt="German">
                            <h4>German</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://de.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://de.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>DWDS: <a href="https://www.dwds.de/wb/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dwds.de/wb/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://de.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://de.thefreedictionary.com/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/de-de/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/de-de/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/de/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/de/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/german-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/german-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/german-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/german-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/german-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/german-english/%s</a> (English
                                    only)</li>
                                <li>Word Reference: <a href="https://www.wordreference.com/deen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/deen/%s</a>
                                </li>
                                <li>Dict.cc: <a href="https://deen.dict.cc/?s=%s" target="_blank"
                                        rel="noopener noreferrer">https://deen.dict.cc/?s=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/de-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/de-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.duden.de/" target="_blank" rel="noopener noreferrer">Duden</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://de.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://de.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/el.svg"
                                alt="Greek">
                            <h4>Greek</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://el.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://el.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://el.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://el.thefreedictionary.com/%s</a>
                                </li>
                                <li>Dictionary of Modern Greek: <a
                                        href="https://www.greek-language.gr/greekLang/modern_greek/tools/lexica/triantafyllides/search.html?lq=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.greek-language.gr/greekLang/modern_greek/tools/lexica/triantafyllides/search.html?lq=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/el-el/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/el-el/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/el/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/de/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/greek-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/greek-english/search?query=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/el-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/el-en/</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/greek-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/greek-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://el.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://el.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/he.svg"
                                alt="Hebrew">
                            <h4>Hebrew</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://he.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://he.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://he.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://he.thefreedictionary.com/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/he-he/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/he-he/</a></li>
                                <li>Milog: <a href="https://milog.co.il/%s" target="_blank"
                                        rel="noopener noreferrer">https://milog.co.il/%s</a>
                                </li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/he/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/he/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/hebrew-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/hebrew-english/%s</a>
                                </li>
                                <li>Morfix: <a href="https://www.morfix.co.il/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.morfix.co.il/en/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/hebrew-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/hebrew-english/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/he-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/he-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://he.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/hi.svg"
                                alt="Hindi">
                            <h4>Hindi</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://hi.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://hi.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hi-hi/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hi-hi/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/hi/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/hi/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Bolti: <a href="https://www.boltidictionary.com/en/search?s=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.boltidictionary.com/en/search?s=%s</a>
                                </li>
                                <li>Shabdkosh: <a
                                        href="https://www.shabdkosh.com/search-dictionary?lc=hi&sl=en&tl=hi&e=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.shabdkosh.com/search-dictionary?lc=hi&sl=en&tl=hi&e=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hi-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hi-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://hi.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://hi.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/hu.svg"
                                alt="Hungarian">
                            <h4>Hungarian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://hu.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://hu.m.wiktionary.org/wiki/%s</a></li>
                                <li>Sztaki:
                                    <a href="https://szotar.sztaki.hu/en/search?dict=hun-eng-sztaki-dict&fromlang=hun&tolang=eng&flash=&E=1&vk=&in_form=1&searchWord=%s&M=1&P=0&C=1&T=1"
                                        target="_blank" rel="noopener noreferrer">
                                        https://szotar.sztaki.hu/en/search?dict=hun-eng-sztaki-dict&fromlang=hun&tolang=eng&flash=&E=1&vk=&in_form=1&searchWord=%s&M=1&P=0&C=1&T=1
                                    </a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hu-hu/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hu-hu/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/hu/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/hu/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/hungarian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/hungarian-english/%s</a> (English
                                    only)</li>
                                <li>Reverso: <a href="https://www.reverso.net/text-translation#sl=hun&tl=eng&text=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.reverso.net/text-translation#sl=hun&tl=eng&text=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/hu-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/hu-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://hu.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://hu.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/it.svg"
                                alt="Italian">
                            <h4>Italian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://it.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://it.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://it.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://it.thefreedictionary.com/%s</a>
                                </li>
                                <li>Sabatini Coletti: <a
                                        href="https://dizionari.corriere.it/dizionario_italiano/C/%s.shtml"
                                        target="_blank" rel="noopener noreferrer">
                                        https://dizionari.corriere.it/dizionario_italiano/C/%s.shtml</a>
                                </li>
                                <li>Garzanti: <a href="https://www.garzantilinguistica.it/ricerca/?q=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.garzantilinguistica.it/ricerca/?q=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/it-it/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/it-it/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/it/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/it/</a>
                                </li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.treccani.it/vocabolario/" target="_blank"
                                        rel="noopener noreferrer">Trecanni</a> (no modal support)</li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/italian-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/italian-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/italian-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/italian-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/italian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/italian-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/it-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/it-en/</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/iten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/iten/%s</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://it.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://it.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ja.svg"
                                alt="Japanese">
                            <h4>Japanese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ja.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ja.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ja-ja/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ja-ja/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ja/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ja/</a>
                                </li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://dictionary.goo.ne.jp/" target="_blank"
                                        rel="noopener noreferrer">Goo</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/japanese-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/japanese-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/japanese-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/japanese-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/japanese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/japanese-english/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ja-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ja-en/</a></li>
                                <li>Jisho: <a href="https://jisho.org/search/%s" target="_blank"
                                        rel="noopener noreferrer">https://jisho.org/search/%s</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://tangorin.com/" target="_blank"
                                        rel="noopener noreferrer">Tangorin</a> (no modal support)
                                </li>
                                <li><a href="https://dictionary.goo.ne.jp/" target="_blank"
                                        rel="noopener noreferrer">Goo</a> (no modal support)
                                </li>
                                <li><a href="https://ko.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://ko.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ko.svg"
                                alt="Korean">
                            <h4>Korean</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ko.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ko.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>Naver: <a href="https://ko.dict.naver.com/#/search?query=%s" target="_blank"
                                        rel="noopener noreferrer">https://ko.dict.naver.com/#/search?query=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ko-ko/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ko-ko/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ko/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ko/</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/korean-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/korean-english/%s</a>
                                </li>
                                <li>Naver: <a href="https://en.dict.naver.com/#/search?query=%s" target="_blank"
                                        rel="noopener noreferrer">https://en.dict.naver.com/#/search?query=%s</a>
                                </li>
                                <li>ZKorean: <a href="https://zkorean.com/dictionary/search_results?word=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://zkorean.com/dictionary/search_results?word=%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ko-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ko-en/</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/korean-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/korean-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://ko.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://ko.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/no.svg"
                                alt="Norwegian">
                            <h4>Norwegian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://no.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://no.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://no.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://no.thefreedictionary.com/%s</a></li>
                                <li>Ordbøkene: <a href="https://ordbokene.no/nno/bm,nn/%s" target="_blank"
                                        rel="noopener noreferrer">https://ordbokene.no/nno/bm,nn/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/no-no/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/no-no/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/no/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/no/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/no-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/no-en/</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/norwegian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/norwegian-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://no.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://no.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/pl.svg"
                                alt="Polish">
                            <h4>Polish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://pl.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://pl.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://pl.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://pl.thefreedictionary.com/%s</a></li>
                                <li>Dobry Słownik: <a href="https://dobryslownik.pl/wyszukaj/?q=%s" target="_blank"
                                        rel="noopener noreferrer">https://dobryslownik.pl/wyszukaj/?q=%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/pl-pl/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/pl-pl/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/pl/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/pl/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/polish-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/polish-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/plen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/plen/%s</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/polish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/polish-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/pl-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/pl-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://pl.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://pl.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/pt.svg"
                                alt="Portuguese">
                            <h4>Portuguese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://pt.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://pt.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>Priberam: <a href="https://dicionario.priberam.org/%s" target="_blank"
                                        rel="noopener noreferrer">https://dicionario.priberam.org/%s</a>
                                </li>
                                <li>Dicionário Online de Português: <a href="https://www.dicio.com.br/%s"
                                        target="_blank" rel="noopener noreferrer">https://www.dicio.com.br/%s</a>
                                </li>
                                <li>Michaelis: <a
                                        href="https://michaelis.uol.com.br/moderno-portugues/busca/portugues-brasileiro/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://michaelis.uol.com.br/moderno-portugues/busca/portugues-brasileiro/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://pt.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://pt.thefreedictionary.com/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/pt-pt/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/pt-pt/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/pt/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/pt/</a>
                                </li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.infopedia.pt/dicionarios/lingua-portuguesa/" target="_blank"
                                        rel="noopener noreferrer">Infopédia</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/portuguese-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/portuguese-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/portuguese-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/portuguese-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/portuguese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/portuguese-english/%s</a>
                                    (English only)</li>
                                <li>Word Reference: <a href="https://www.wordreference.com/pten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/pten/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/pt-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/pt-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the
                                        corresponding <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes"
                                            target="_blank" rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://pt.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://pt.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ro.svg"
                                alt="Romanian">
                            <h4>Romanian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ro.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ro.m.wiktionary.org/wiki/%s</a></li>
                                <li>Dexonline: <a href="https://dexonline.ro/definitie/%s" target="_blank"
                                        rel="noopener noreferrer">https://dexonline.ro/definitie/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ro-ro/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ro-ro/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ro/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ro/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/romanian-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/romanian-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/romanian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/romanian-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ro-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ro-en/</a></li>
                                <li>Word Reference: <a href="https://www.wordreference.com/roen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/roen/%s</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://ro.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://ro.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/ru.svg"
                                alt="Russian">
                            <h4>Russian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://ru.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ru.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://ru.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://ru.thefreedictionary.com/%s</a>
                                </li>
                                <li>Academic.ru: <a href="https://translate.academic.ru/%s/ru/ru/" target="_blank"
                                        rel="noopener noreferrer">https://translate.academic.ru/%s/ru/ru/</a>
                                </li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ru/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ru/</a>
                                </li>
                                <li>Gramota.ru: <a href="https://gramota.ru/poisk?query=%s&mode=all&l=1" target="_blank"
                                        rel="noopener noreferrer">https://gramota.ru/poisk?query=%s&mode=all&l=1</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ru-ru/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ru-ru/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/russian-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/russian-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/russian-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/russian-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/ruen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/ruen/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/russian-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/russian-english/%s</a>
                                </li>
                                <li>Openrussian.org: <a href="https://en.openrussian.org/ru/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.openrussian.org/ru/%s</a>
                                </li>
                                <li>Academic.ru: <a href="https://translate.academic.ru/%s/ru/en/" target="_blank"
                                        rel="noopener noreferrer">https://translate.academic.ru/%s/ru/en/</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ru-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ru-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://ru.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://www.russiandict.net/" target="_blank"
                                        rel="noopener noreferrer">Russiandict</a> (no modal support)
                                </li>
                                <li><a href="https://ru.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/sk.svg"
                                alt="Slovak">
                            <h4>Slovak</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://sk.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://sk.m.wiktionary.org/wiki/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sk-sk/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sk-sk/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/sk/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/sk/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://slovnik.juls.savba.sk/" target="_blank"
                                        rel="noopener noreferrer">Slovnik</a> (no modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://www.reverso.net/text-translation#sl=slo&tl=eng&text=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://www.reverso.net/text-translation#sl=slo&tl=eng&text=%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/slovak-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/slovak-english/%s</a> (English
                                    only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sk-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sk-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://sk.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/sl.svg"
                                alt="Slovenian">
                            <h4>Slovenian</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://sl.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://sl.m.wiktionary.org/wiki/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sl-sl/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sl-sl/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ar/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/sl/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://fran.si/" target="_blank" rel="noopener noreferrer">Fran</a> (no
                                    modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sl-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sl-en/</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/slovene-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/slovene-english/%s</a> (English
                                    only)</li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://sl.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/es.svg"
                                alt="Spanish">
                            <h4>Spanish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://es.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://es.m.wiktionary.org/wiki/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/definicion/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definicion/%s</a>
                                </li>
                                <li>The Free Dictionary: <a href="https://es.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://es.thefreedictionary.com/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/es-es/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/es-es/</a></li>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://dle.rae.es/" target="_blank" rel="noopener noreferrer">Diccionario
                                        Real Academia
                                        Española</a> (no modal support)
                                </li>
                                <li><a href="https://www.rae.es/dpd/" target="_blank"
                                        rel="noopener noreferrer">Diccionario Panhispánico de Dudas</a> (no modal
                                    support)
                                </li>
                                <li><a href="https://forvo.com/" target="_blank" rel="noopener noreferrer">Forvo</a> (no
                                    modal support)
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Linguee: <a href="https://mobile.linguee.com/spanish-english/search?query=%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://mobile.linguee.com/spanish-english/search?query=%s</a>
                                </li>
                                <li>Reverso: <a href="https://context.reverso.net/translation/spanish-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/spanish-english/%s</a>
                                </li>
                                <li>Dict.com: <a href="https://www.dict.com/spanish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/spanish-english/%s</a> (English
                                    only)</li>
                                <li>Word Reference: <a href="https://www.wordreference.com/esen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/esen/%s</a>
                                </li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/es-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/es-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://es.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://es.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/sv.svg"
                                alt="Swedish">
                            <h4>Swedish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://sv.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://sv.m.wiktionary.org/wiki/%s</a></li>
                                <li>Lexin: <a href="https://lexin.nada.kth.se/lexin/#searchinfo=both,swe_swe,%s;"
                                        target="_blank"
                                        rel="noopener noreferrer">https://lexin.nada.kth.se/lexin/#searchinfo=both,swe_swe,%s;</a>
                                </li>
                                <li>Svenska Akademien: <a href="https://www.saob.se/artikel/?seek=%s" target="_blank"
                                        rel="noopener noreferrer">https://www.saob.se/artikel/?seek=%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sv-sv/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sv-sv/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/sv/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/sv/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/swedish-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/swedish-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/sven/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/sven/%s</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/swedish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/swedish-english/%s</a> (English
                                    only)</li>
                                <li>Folkets: <a href="https://folkets-lexikon.csc.kth.se/folkets/#lookup&%s&1"
                                        target="_blank"
                                        rel="noopener noreferrer">https://folkets-lexikon.csc.kth.se/folkets/#lookup&%s&1</a>
                                    (English only)</li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/sv-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/sv-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://sv.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://sv.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/tr.svg"
                                alt="Turkish">
                            <h4>Turkish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://tr.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://tr.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://tr.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://tr.thefreedictionary.com/%s</a></li>
                                <li>Kubbealti Lugati: <a href="https://lugatim.com/s/%s" target="_blank"
                                        rel="noopener noreferrer">https://lugatim.com/s/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/tr-tr/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/tr-tr/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/tr/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/tr/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Reverso: <a href="https://context.reverso.net/translation/turkish-english/%s"
                                        target="_blank" rel="noopener noreferrer">
                                        https://context.reverso.net/translation/turkish-english/%s</a>
                                </li>
                                <li>Word Reference: <a href="https://www.wordreference.com/tren/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/tren/%s</a></li>
                                <li>Dict.com: <a href="https://www.dict.com/turkish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/turkish-english/%s</a> (English
                                    only)</li>
                                <li>Turkce Sozluk: <a href="https://www.turkcesozluk.net/index.php?word=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.turkcesozluk.net/index.php?word=%s</a>
                                </li>
                                <li>Zargan: <a href="https://www.zargan.com/tr/q/%s-ceviri-nedir" target="_blank"
                                        rel="noopener noreferrer">https://www.zargan.com/tr/q/%s-ceviri-nedir</a></li>
                                <li>Tureng: <a href="https://tureng.com/en/turkish-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://tureng.com/en/turkish-english/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/tr-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/tr-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://www.seslisozluk.net/" target="_blank"
                                        rel="noopener noreferrer">Sesli Sözlük</a> (no modal support)
                                </li>
                                <li><a href="https://tr.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tr.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
                            </ul>
                        </section>
                        <hr class="my-5">
                        <section>
                            <img class="d-none d-sm-block float-end" style="max-width:10%" src="/img/flags/vi.svg"
                                alt="Vietnamese">
                            <h4>Vietnamese</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://vi.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://vi.m.wiktionary.org/wiki/%s</a></li>
                                <li>VDict: <a href="https://vdict.com/%s,3,0,0.html" target="_blank"
                                        rel="noopener noreferrer">https://vdict.com/%s,3,0,0.html</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/vi-vi/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/vi-vi/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/vi/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/vi/</a></li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <br>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/vietnamese-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/vietnamese-english/%s</a>
                                    (English only)</li>
                                <li>VDict: <a href="https://vdict.com/%s,2,0,0.html" target="_blank"
                                        rel="noopener noreferrer">https://vdict.com/%s,2,0,0.html</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/vi-en/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/vi-en/</a></li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em></small>
                            </ul>
                            The following have been reported to work only as external dictionaries:
                            <ul>
                                <li><a href="https://vi.bab.la/" target="_blank" rel="noopener noreferrer">Bab.la</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://vi.glosbe.com/" target="_blank"
                                        rel="noopener noreferrer">Glosbe</a> (no modal support)
                                </li>
                                <li><a href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                    (no modal support)
                                </li>
                                <li><a href="https://tatoeba.org/" target="_blank" rel="noopener noreferrer">Tatoeba</a>
                                    (no modal support)
                                </li>
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
