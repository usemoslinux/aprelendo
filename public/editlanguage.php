<?php 
    $is_premium_user = $user->isPremium();
?>
<form class="" action="languages.php" method="post">

    <input type="hidden" name="id" value="<?php echo $lang->id; ?>">
    <input type="hidden" name="language" class="form-control" value="<?php echo $lang->name; ?>">
    
    <div class="card">
        <div class="card-header">Dictionary & Translator</div>
        <div class="card-body">
            <div class="form-group">
                <label for="dictionaryURI">Dictionary URI:</label>
                <input type="url" name="dictionaryURI" class="form-control" value="<?php echo $lang->dictionary_uri; ?>">
            </div>
            <div class="form-group">
                <label for="translatorURI">Translator URI:</label>
                <input type="url" name="translatorURI" class="form-control" value="<?php echo $lang->translator_uri; ?>">
            </div>
            <div class="text-right">
                <a href="javascript:;" title="Help" data-toggle="collapse" data-target="#help-dictionary"><i class="far fa-question-circle"></i></a>
            </div>
            
            <div id="help-dictionary" class="collapse small">
                <hr>
                <p>
                    URLs should meet the following requirements to work properly:
                </p>
                <ul>
                    <li>Some websites ensure that their content is not embedded into other sites. Therefore, some dictionaries may not work with Aprelendo. For further help, check the list of <a href="compatibledics.php">compatible dictionaries</a>.</li>
                    <li>For security reasons, only HTTPS websites are supported. Make sure you use URL addresses that start with HTTPS, not HTTP.</li>
                    <li>As the dictionary is going to be shown inside a modal window, it is highly recommended to use websites that support smaller screens. In case that support is not automatic, look for the mobile version of that website (if there is one) and use that one instead.</li>
                    <li>Don't forget to indicate the position of the lookup phrase by using "%s" (without quotation marks).</li>
                </ul>
            </div>
        </div>
    </div>
    <br/>
    <fieldset <?php echo $is_premium_user ? '' : 'disabled'; ?> >
    <div class="card">
        <div class="card-header">RSS feeds <?php echo $is_premium_user ? '' : ' <a href="gopremium.php" class="text-danger">(premium users only)</a>'; ?></div>
        <div class="card-body">
            <div class="form-group">
                <label for="rssfeedURI1">RSS feed URI 1:</label>
                <input type="url" name="rssfeedURI1" class="form-control" value="<?php echo $lang->rss_feed_1_uri; ?>">
            </div>
            <div class="form-group">
                <label for="rssfeedURI2">RSS feed URI 2:</label>
                <input type="url" name="rssfeedURI2" class="form-control" value="<?php echo $lang->rss_feed_2_uri; ?>">
            </div>
            <div class="form-group">
                <label for="rssfeedURI3">RSS feed URI 3:</label>
                <input type="url" name="rssfeedURI3" class="form-control" value="<?php echo $lang->rss_feed_3_uri; ?>">
            </div>
        </div>
    </div>
    </fieldset>
    <br/>
    <fieldset <?php echo $is_premium_user ? '' : 'disabled'; ?> >
    <div class="card">
        <div class="card-header">Frequency list <?php echo $is_premium_user ? '' : ' <a href="gopremium.php" class="text-danger">(premium users only)</a>'; ?></div>
        <div class="card-body">
            <div class="form-group">
                <label for="freq-list">Underline 5000 most used words:</label>
                <select name="freq-list" id="freq-list" class="form-control">
                    <option value="1" <?php echo $lang->show_freq_list==true ? 'selected' : ''; ?>>Yes</option>
                    <option value="0" <?php echo $lang->show_freq_list==false ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
        </div>
    </div>
    </fieldset>
    <br/>
    <div class="text-right">
        <button id="cancelbtn" name="cancel" type="button" class="btn btn-link" onclick="window.location='texts.php'">Cancel</button>
        <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
    </div>
</form>