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
                                <li>Armany: <a href="https://www.almaany.com/ar/dict/ar-ar/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.almaany.com/ar/dict/ar-ar/%s</a>
                                </li>
                                <li>Wiktionary: <a href="https://ar.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.m.wiktionary.org/wiki/%s</a></li>
                                <li>The Free Dictionary: <a href="https://ar.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.thefreedictionary.com/%s</a></li>
                                <li>Sensagent: <a href="https://dictionary.sensagent.com/%s/ar-ar/" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.sensagent.com/%s/ar-ar/</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/ar/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/ar/</a></li>
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
                                
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/arabic-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/arabic-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://ar.glosbe.com/ar/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://ar.glosbe.com/ar/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/arabic-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/arabic-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=ara&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=ara&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/bg/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/bg/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/bulgarian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/bulgarian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=bul&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=bul&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Gran Diccionari de la Llengua Catalana: <a href="https://www.diccionari.cat/GDLC/%s" target="_blank" rel="noopener noreferrer">https://www.diccionari.cat/GDLC/%s</a>
                                </li>
                            </ul>
                            <strong>Bilingual dictionaries (*)</strong>
                            <ul>
                                <li>Dict.com: <a href="https://www.dict.com/catalan-english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dict.com/catalan-english/%s</a> (English
                                    only)</li>
                                <li>Glosbe: <a href="https://en.glosbe.com/ca/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/ca/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/catalan-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/catalan-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=cat&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=cat&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/zh/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/zh/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/chinese-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/chinese-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=cmn&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=cmn&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/hr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/hr/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/croatian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/croatian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=hrv&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=hrv&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Seznam: <a href="https://slovnik.seznam.cz/preklad/cesky_anglicky/%s" target="_blank"
                                        rel="noopener noreferrer">https://slovnik.seznam.cz/preklad/cesky_anglicky/%s</a>
                                </li>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/czech-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/czech-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/cs/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/cs/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/czech-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/czech-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=ces&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=ces&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Sproget.dk: <a href="https://sproget.dk/lookup?SearchableText=%s" target="_blank"
                                        rel="noopener noreferrer">https://sproget.dk/lookup?SearchableText=%s</a>
                                </li>
                                <li>Den Danske Ordbog: <a href="https://ordnet.dk/ddo/ordbog?query=f%C3%B8rste" target="_blank" rel="noopener noreferrer">https://ordnet.dk/ddo/ordbog?query=f%C3%B8rste</a>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/da/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/da/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/danish-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/danish-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=dan&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=dan&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/dutch-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/dutch-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/nl/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/nl/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/dutch-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/dutch-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=nld&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=nld&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Oxford Learners: <a href="https://www.oxfordlearnersdictionaries.com/us/definition/english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.oxfordlearnersdictionaries.com/us/definition/english/%s</a>
                                </li>
                                <li>Longman: <a href="https://www.ldoceonline.com/dictionary/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.ldoceonline.com/dictionary/%s</a>
                                </li>
                                <li>Collins: <a href="https://www.collinsdictionary.com/dictionary/english/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.collinsdictionary.com/dictionary/english/%s</a>
                                </li>
                                <li>Wordnik: <a href="https://www.wordnik.com/words/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordnik.com/words/%s</a>
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
                                <li>Cambridge dictionary: <a href="https://dictionary.cambridge.org/dictionary/english/%s" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/english/%s</a>
                                </li>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/english-spanish/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/english-spanish/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/en/es/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/en/es/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/english-spanish/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/english-spanish/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=eng&query=%s&to=spa" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=eng&query=%s&to=spa</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Larousse: <a href="https://www.larousse.fr/dictionnaires/francais/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.larousse.fr/dictionnaires/francais/%s</a>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/french-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/french-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/fr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/fr/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/french-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/french-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=fra&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=fra&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Duden: <a href="https://www.duden.de/rechtschreibung/%s" target="_blank" rel="noopener noreferrer">https://www.duden.de/rechtschreibung/%s</a>
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

                                <li>Bab.la: <a href="https://en.bab.la/dictionary/german-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/german-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/de/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/de/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/german-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/german-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=deu&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=deu&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/greek-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/greek-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/el/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/el/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/greek-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/greek-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=ell&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=ell&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/he/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/he/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/hebrew-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/hebrew-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=heb&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=heb&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/hindi-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/hindi-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/hi/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/hi/en/%s</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/hungarian-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/hungarian-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/hu/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/hu/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/hungarian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/hungarian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=hun&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=hun&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Trecanni: <a href="https://www.treccani.it/vocabolario/ricerca/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.treccani.it/vocabolario/ricerca/%s</a></li>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/italian-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/italian-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/it/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/it/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/italian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/italian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=ita&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=ita&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Goo: <a href="https://dictionary.goo.ne.jp/word/%s" target="_blank"
                                        rel="noopener noreferrer">https://dictionary.goo.ne.jp/word/%s</a>
                                </li>
                                <li>Tangorin: <a href="https://tangorin.com/definition/%s" target="_blank"
                                        rel="noopener noreferrer">https://tangorin.com/definition/%s</a>
                                </li>
                                <li>Jisho: <a href="https://jisho.org/search/%s" target="_blank"
                                        rel="noopener noreferrer">https://jisho.org/search/%s</a>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/japanese-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/japanese-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/ja/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/ja/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/japanese-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/japanese-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=jpn&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=jpn&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/korean-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/korean-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/ko/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/ko/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/korean-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/korean-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=kor&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=kor&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/norwegian-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/norwegian-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/nb/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/nb/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/norwegian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/norwegian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=nob&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=nob&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/polish-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/polish-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/pl/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/pl/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/polish-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/polish-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=pol&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=pol&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Infopédia: <a href="https://www.infopedia.pt/dicionarios/lingua-portuguesa/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.infopedia.pt/dicionarios/lingua-portuguesa/%s</a>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/portuguese-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/portuguese-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/pt/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/pt/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/portuguese-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/portuguese-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=por&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=por&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/romanian-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/romanian-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/ro/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/ro/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/romanian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/romanian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=ron&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=ron&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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

                                <li>Russiandict: <a href="https://www.russiandict.net/translate/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.russiandict.net/translate/%s</a>
                                </li>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/russian-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/russian-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/ru/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/ru/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/russian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/russian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=rus&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=rus&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Slovnik: <a href="https://slovnik.juls.savba.sk/?w=%s&s=exact" target="_blank"
                                        rel="noopener noreferrer">https://slovnik.juls.savba.sk/?w=%s&s=exact</a>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/sk/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/sk/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/slovak-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/slovak-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=slk&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=slk&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Fran: <a href="https://fran.si/iskanje?View=1&Query=%s" target="_blank" rel="noopener noreferrer">https://fran.si/iskanje?View=1&Query=%s</a>
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
                                <li>Glosbe: <a href="https://en.glosbe.com/sl/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/sl/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/slovenian-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/slovenian-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=slv&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=slv&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Diccionario Real Academia Española: <a href="https://dle.rae.es/%s" target="_blank" rel="noopener noreferrer">https://dle.rae.es/%s</a>
                                </li>
                                <li>Diccionario Panhispánico de Dudas: <a href="https://www.rae.es/dpd/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.rae.es/dpd/%s</a>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/spanish-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/spanish-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/es/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/es/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/spanish-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/spanish-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=spa&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=spa&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/swedish-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/swedish-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/sv/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/sv/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/swedish-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/swedish-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=swe&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=swe&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Sesli Sözlük: <a href="https://www.seslisozluk.net/%s-nedir-ne-demek/" target="_blank"
                                        rel="noopener noreferrer">https://www.seslisozluk.net/%s-nedir-ne-demek/</a>
                                </li>
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/turkish-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/turkish-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/tr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/tr/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/turkish-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/turkish-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=tur&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=tur&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
                                <li>Bab.la: <a href="https://en.bab.la/dictionary/vietnamese-english/%s" target="_blank" rel="noopener noreferrer">https://en.bab.la/dictionary/vietnamese-english/%s</a>
                                </li>
                                <li>Glosbe: <a href="https://en.glosbe.com/vi/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.glosbe.com/vi/en/%s</a>
                                </li>
                                <li>PONS: <a href="https://en.pons.com/translate-2/vietnamese-english/%s" target="_blank" rel="noopener noreferrer">https://en.pons.com/translate-2/vietnamese-english/%s</a>
                                </li>
                                <li>Tatoeba: <a href="https://tatoeba.org/en/sentences/search?from=vie&query=%s&to=eng" target="_blank" rel="noopener noreferrer">https://tatoeba.org/en/sentences/search?from=vie&query=%s&to=eng</a>
                                </li>
                                <small><em>To make them work in your native language make sure to replace "en" or
                                        "english" with the corresponding <a
                                            href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"
                                            rel="noopener noreferrer">ISO code</a> or name.</em>
                                </small>
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
