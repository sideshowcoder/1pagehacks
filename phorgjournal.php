<?php

require_once "orgile.php";

define("JOURNALDIR", "/Users/pfehre/org/journal/");
define("JOURNALFILES", array_reverse(glob(JOURNALDIR."*.org")));

function newEntry(): void {
  $content = $_POST["content"];
  if (empty($content)) return; // nothing todo

  $journalFileName = JOURNALDIR.date("Ymd").".org";

  if (in_array($journalFileName, JOURNALFILES)) {
    echo "FOO";
    // Create new entry at the end of file
  } else {
    echo "BAR";
    // Create new file with Date heading and entry
  }
}

function readJournal(): string {
  $journalContent = "";
  foreach(JOURNALFILES as $dayEntry) {
    $journalContent = $journalContent . file_get_contents($dayEntry) . "\n";
  }
  return $journalContent;
};

function renderJournal(): string {
  $orgile = new orgile();
  return $orgile->orgileThis(readJournal());
}


?>

<html>
  <head>
    <meta charset="utf-8">
    <title>Org Journal</title>
    <style>
     body {
       width: 100%;
       text-align: center;
       font-family: Calibri, sans-serif;
     }
     #container {
       width: 600px;
       text-align: left;
       margin: 0px auto;
     }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <body>

    <div id="container">
      <form action="/phorgjournal.php" method="post">
        <textarea id="content" name="content" rows="12" cols="70" placeholder="Journal entry, first line becomes title."></textarea>
        <input type="submit" value="Add new entry">
      </form>

      <?php echo newEntry(); ?>
      <?php echo renderJournal(); ?>
    </div>
  </body>
</html>
