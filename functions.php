<?php
  function addParagraphs($text) // Add paragraph elements to text
  {
      $lf = chr(10);
      $text = preg_replace('/\n/', '</p><p>', $text);
      $text = '<p>'.$text.'</p>';

      return $text;
  }

  function addlinks($text)
  {
      $text = preg_replace('/\w+(?![^<]*>)/iu',
    "<a href='' data-toggle='modal' data-target='#myModal'>$0</a>", $text);

      return $text;
  }

  function colorizeWords($text)
  {
      require 'connect.php';

      $result = mysqli_query($con, 'SELECT word, wordStatus FROM words') or die(mysqli_error($con));
      while ($row = mysqli_fetch_assoc($result)) {
          $word = $row['word'];
          $text = preg_replace("/\b".$word."\b(?![^<]*>)/ui",
      "<span class='word learning'>$0</span>", "$text");
      }

      return $text;
  }
