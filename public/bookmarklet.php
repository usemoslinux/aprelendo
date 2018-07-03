<?php require_once 'header.php';?>

<div class="container mtb">
  <div class="row">
    <div class="col-xs-12">
      <ol class="breadcrumb">
        <li>
          <a href="texts.php">Home</a>
        </li>
        <li>
          <a class="active">Bookmarklet</a>
        </li>
      </ol>
      <div id="more_content" class="row flex">
        <div class="col-xs-12">
          <h4>What are bookmarklets?</h4>
          <p>Bookmarklets are a "one-click" tool which add functionality to the browser. From a user perspective, they work very much like regular bookmarks.</p>
          <p>For more information on bookmarklets, I suggest reading <a href="https://en.wikipedia.org/wiki/Bookmarklet">Wikipedia's article</a> on this subject.</p>
          <br>
            <h4>How are bookmarklets different from extensions?</h4>
          <ul>
            <li>They do basic tasks on clicking.</li>
            <li>They are universal, i.e. they usually work on any browser and whatever the platform, mobile or desktop.</li>
            <li>They are managed as any bookmarks.</li>
          </ul>
          <br>
          <h4>What does LangX use bookmarklets for?</h4>
          <p>LangX uses bookmarklets to automagically parse the text of the current web page and add it to your library.</p>
          <p>It's an alternative to creating specific addons for different browsers. It's easier to implement and has the added advantage that it works in almost any device and/or browser.</p>
          <br>
          <h4>How do I install LangX's bookmarklet in my web browser?</h4>
          <p>To install the bookmarklet, simply:</p>
          <ol>
            <li>Show the Bookmarks Toolbar:
              <p>In Firefox: Go to <kbd>View</kbd> > <kbd>Toolbars</kbd> > <kbd>Bookmarks toolbar</kbd></p>
              <p>In Google Chrome: Go to <kbd>View</kbd> > <kbd>Show bookmarks bar</kbd></p></li>
            <li>Drag the following link (<a href="javascript:void(location.href='https://localhost/addtext.php?url='+encodeURIComponent(location.href));">LangX</a> bookmarklet) to your Bookmarks Toolbar. It should now appear on the toolbar. </li>
          </ol>
          <br>
          <h4>How to use LangX's bookmarklet once installed</h4>
          <p>Simply go to the web page you would like to add to you library and click on LangX's bookmarklet. It's as easy as it gets.</p>
          <p>You'll be redirected to LangX so that you can include audio and do other changes to the text before uploading it to your library.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'footer.php';?>