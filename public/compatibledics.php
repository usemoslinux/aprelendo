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
                        <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="active">List of compatible dictionaries</a>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <small>Non-exhaustive list</small>
                            <h4>
                                English
                            </h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://en.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://en.m.wiktionary.org/wiki/%s</a></li>
                                <li>Wordreference: <a href="https://www.wordreference.com/definition/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/definition/%s</a></li>
                                <li>Dictionary.com: <a href="https://www.dictionary.com/browse/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.dictionary.com/browse/%s</a></li>
                                <li>The Free Dictionary: <a href="https://www.thefreedictionary.com/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.thefreedictionary.com/%s</a></li>
                                <li>Meriam Webster: <a href="https://www.merriam-webster.com/dictionary/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.merriam-webster.com/dictionary/%s</a></li>
                                <li>Forvo: <a href="https://forvo.com/search/%s/en/" target="_blank"
                                        rel="noopener noreferrer">https://forvo.com/search/%s/en/</a></li>
                                <li>Vocabulary.com: <a href="https://www.vocabulary.com/dictionary/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.vocabulary.com/dictionary/%s</a></li>
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
                            <br>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#en/es/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#en/es/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=en-es&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=en-es&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#en/es/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#en/es/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/english-spanish/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/english-spanish/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/english-spanish/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/english-spanish/%s</a>
                                </li>
                                <li>Wordreference: <a
                                        href="https://www.wordreference.com/es/translation.asp?tranword=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/es/translation.asp?tranword=%s</a>
                                </li>
                                <small><i>(*) Only English-&gt;Spanish dictionaries are listed.<br>To make them work in
                                        your native language make sure to replace "es" or "spanish" with the ISO code or
                                        name corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                        <br>
                        <section>
                            <h4>Spanish</h4>
                            <strong>Monolingual dictionaries</strong>
                            <ul>
                                <li>Wiktionary: <a href="https://es.m.wiktionary.org/wiki/%s" target="_blank"
                                        rel="noopener noreferrer">https://es.m.wiktionary.org/wiki/%s</a></li>
                                <li>Diccionario Real Academia Española: <a href="https://dle.rae.es/%s" target="_blank"
                                        rel="noopener noreferrer">https://dle.rae.es/%s</a></li>
                                <li>Wordreference: <a href="https://www.wordreference.com/definicion/%s" target="_blank"
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
                            <br>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#es/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#es/en/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=es-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=es-en&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#es/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#es/en/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/spanish-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/spanish-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/spanish-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/spanish-english/%s</a>
                                </li>
                                <li>Wordreference: <a href="https://www.wordreference.com/esen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/esen/%s</a></li>
                                <small><i>Only Spanish-&gt;English dictionaries are listed.<br>To make them work in your
                                        native language make sure to replace "en" or "english" with the ISO code or name
                                        corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                        <br>
                        <section>
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
                            <br>
                            <strong>Translation (*)</strong>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#pt/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#pt/en/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=pt-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=pt-en&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#pt/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#pt/en/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/portuguese-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/portuguese-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/portuguese-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/portuguese-english/%s</a>
                                </li>
                                <li>Wordreference: <a href="https://www.wordreference.com/pten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/pten/%s</a></li>
                                <small><i>Only Portuguese-&gt;English dictionaries are listed.<br>To make them work in
                                        your native language make sure to replace "en" or "english" with the ISO code or
                                        name corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                        <br>
                        <section>
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
                            <br>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#fr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#fr/en/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=fr-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=fr-en&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#fr/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#fr/en/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/french-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/french-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/french-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/french-english/%s</a>
                                </li>
                                <li>Wordreference: <a href="https://www.wordreference.com/fren/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/fren/%s</a></li>
                                <small><i>Only French-&gt;English dictionaries are listed.<br>To make them work in your
                                        native language make sure to replace "en" or "english" with the ISO code or name
                                        corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                        <br>
                        <section>
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
                            <br>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#it/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#it/en/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=it-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=it-en&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#it/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#it/en/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/italian-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/italian-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/italian-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/italian-english/%s</a>
                                </li>
                                <li>Wordreference: <a href="https://www.wordreference.com/iten/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/iten/%s</a></li>
                                <small><i>Only Italian-&gt;English dictionaries are listed.<br>To make them work in your
                                        native language make sure to replace "en" or "english" with the ISO code or name
                                        corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                        <br>
                        <section>
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
                            <br>
                            <strong>Translation (*)</strong>
                            <br>
                            <ul>
                                <li>Google Translator: <a href="https://translate.google.com/#de/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://translate.google.com/#de/en/%s</a></li>
                                <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=de-en&text=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://translate.yandex.com/?lang=de-en&text=%s</a>
                                </li>
                                <li>Deepl: <a href="https://www.deepl.com/translator#de/en/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.deepl.com/translator#de/en/%s</a></li>
                                <li>Linguee: <a href="https://mobile.linguee.com/german-english/search?query=%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://mobile.linguee.com/german-english/search?query=%s</a>
                                </li>
                                <li>Cambridge dictionary: <a
                                        href="https://dictionary.cambridge.org/dictionary/german-english/%s"
                                        target="_blank"
                                        rel="noopener noreferrer">https://dictionary.cambridge.org/dictionary/german-english/%s</a>
                                </li>
                                <li>Wordreference: <a href="https://www.wordreference.com/deen/%s" target="_blank"
                                        rel="noopener noreferrer">https://www.wordreference.com/deen/%s</a></li>
                                <li>Dict.cc: <a href="https://deen.dict.cc/?s=%s" target="_blank"
                                        rel="noopener noreferrer">https://deen.dict.cc/?s=%s</a></li>
                                <small><i>Only German-&gt;English dictionaries are listed.<br>To make them work in your
                                        native language. Just make sure to replace "en" or "english" with the ISO code
                                        or name corresponding to your language.</i></small>
                                <br><br>
                                The following have been reported not to work: <a href="https://en.bab.la/dictionary/"
                                    target="_blank" rel="noopener noreferrer">Bab.la</a> (no modal support), <a
                                    href="https://mobile.pons.com" target="_blank" rel="noopener noreferrer">PONS</a>
                                (no modal support), <a href="https://mobile.reverso.net" target="_blank"
                                    rel="noopener noreferrer">Reverso</a> (faulty https version).
                            </ul>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>