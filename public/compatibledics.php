<?php 

require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . 'classes/users.php'); // load Users class

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once('simpleheader.php');
} else {
    require_once('header.php');
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li>
                    <a class="active">List of compatible dictionaries</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-xs-12">
                    <small>Non-exhaustive list</small>
                    <h4>
                        English
                    </h4>
                    <strong>Monolingual dictionaries</strong>
                    <ul>
                        <li>Wiktionary: <a href="https://en.m.wiktionary.org/wiki/%s" target="_blank">https://en.m.wiktionary.org/wiki/%s</a></li>
                        <li>Wordreference: <a href="https://www.wordreference.com/definition/%s" target="_blank">https://www.wordreference.com/definition/%s</a></li>
                        <li>Dictionary.com: <a href="https://www.dictionary.com/browse/%s" target="_blank">https://www.dictionary.com/browse/%s</a></li>
                        <li>The Free Dictionary: <a href="https://www.thefreedictionary.com/%s" target="_blank">https://www.thefreedictionary.com/%s</a></li>
                        <li>Meriam Webster: <a href="https://www.merriam-webster.com/dictionary/%s" target="_blank">https://www.merriam-webster.com/dictionary/%s</a></li>
                        <li>Forvo: <a href="https://forvo.com/search/%s/en/" target="_blank">https://forvo.com/search/%s/en/</a></li>
                        <li>Vocabulary.com: <a href="https://www.vocabulary.com/dictionary/%s" target="_blank">https://www.vocabulary.com/dictionary/%s</a></li>
                        <li>Google: <a href="https://www.google.com/search?q=definition+%s" target="_blank">https://www.google.com/search?q=definition+%s</a></li>
                        <br>
                        The following have been reported not to work: <a href="https://www.oxfordlearnersdictionaries.com" target="_blank">Oxford Learners</a> (no modal support), <a href="https://en.oxforddictionaries.com" target="_blank">Oxford compact</a> (no modal support), <a href="https://www.macmillandictionary.com" target="_blank">Macmillan</a> (no modal support), <a href="https://www.ldoceonline.com" target="_blank">Longman</a> (no modal support), <a href="https://www.collinsdictionary.com" target="_blank">Collins</a> (no modal support), <a href="https://www.wordnik.com" target="_blank">Wordnik</a> (no modal support).
                    </ul>
                    <br/>
                    <strong>Translation</strong>
                    <br/>
                    <ul>
                    <small><i>Only English-&gt;Spanish dictionaries are listed, but you can easily make the necessary changes to make them work in your native language. Just make sure to replace "es" or "spanish" with the ISO code or name corresponding to your language.</i></small>
                        <li>Google Translator: <a href="https://translate.google.com/#en/es/%s" target="_blank">https://translate.google.com/#en/es/%s</a></li>
                        <li>Yandex Translator: <a href="https://translate.yandex.com/?lang=en-es&text=%s" target="_blank">https://translate.yandex.com/?lang=en-es&text=%s</a></li>
                        <li>Deepl: <a href="https://www.deepl.com/translator#en/es/%s" target="_blank">https://www.deepl.com/translator#en/es/%s</a></li>
                        <li>Linguee: <a href="https://mobile.linguee.com/english-spanish/search?query=%s" target="_blank">https://mobile.linguee.com/english-spanish/search?query=%s</a></li>
                        <li>Cambridge dictionary: <a href="https://dictionary.cambridge.org/dictionary/english-spanish/%s" target="_blank">https://dictionary.cambridge.org/dictionary/english-spanish/%s</a></li>
                        <li>Wordreference: <a href="https://www.wordreference.com/es/translation.asp?tranword=%s" target="_blank">https://www.wordreference.com/es/translation.asp?tranword=%s</a></li>
                        <br>
                        The following have been reported not to work: <a href="https://mobile.reverso.net" target="_blank">Reverso</a> (faulty https version), 
                    </ul>
                    Spanish
                    Portuguese
                    French
                    Italian
                    German
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>