<form class="" action="languages.php" method="post">
  <input type="hidden" name="id" value="<?php echo $lang->id; ?>">
  <h3>
    <?php echo ucfirst($user->getLanguageName($lang->name)) ?>
  </h3>
  <input type="hidden" name="language" class="form-control" value="<?php echo $lang->name; ?>">
  <div class="form-group">
    <label for="dictionaryURI">Dictionary URI:</label>
    <input type="url" name="dictionaryURI" class="form-control" value="<?php echo $lang->dictionary_uri; ?>">
  </div>
  <div class="form-group">
    <label for="translatorURI">Translator URI:</label>
    <input type="url" name="translatorURI" class="form-control" value="<?php echo $lang->translator_uri; ?>">
  </div>
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
  <div class="form-group">
    <label for="freq-list">Underline 5000 most used words (frequency lists):</label>
    <select name="freq-list" id="freq-list">
      <option value="1" <?php echo $lang->show_freq_list==true ? 'selected' : ''; ?>>Yes</option>
      <option value="0" <?php echo $lang->show_freq_list==false ? 'selected' : ''; ?>>No</option>
    </select>
  </div>
  <button type="button" id="cancelbtn" name="cancel" class="btn btn-default" onclick="window.location='languages.php'">Cancel</button>
  <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
</form>