<?php declare(strict_types=1);

session_start();

require_once "orgile.php";

define("JOURNALDIR", "/Users/pfehre/org/journal/");
$result = array();

function handlePostRedirect(): void {
  global $result;

  if(!empty($_POST)) {
    $result = newEntry();
    if($result["success"]) {
      $_SESSION["msg"] = $result["msg"];
      header( 'HTTP/1.1 303 See Other' );
      header( 'Location: '.$_SERVER["PHP_SELF"].'/phorgjournal.php' );
      exit();
    }
  }
}

function renderForm(): void {
  global $result;
  if(!empty($_POST)) {
    if(!$result["success"]) {
      echo $result["msg"];
      ?>
      <form method="post">
        <textarea id="content" name="content" rows="12" cols="70" placeholder="Journal entry, first line becomes title."><?php echo $_POST["content"]; ?></textarea>
        <input type="submit" value="Add new entry">
      </form>
      <?php
    } else {
      // handled in handlePostRedirect()
      // noop
    }
  } else {
    if(isset($_SESSION["msg"])) {
      echo $_SESSION["msg"];
      unset($_SESSION["msg"]);
    }
    ?>
    <form method="post">
      <textarea id="content" name="content" rows="12" cols="70" placeholder="Journal entry, first line becomes title."></textarea>
      <input type="submit" value="Add new entry">
    </form>
    <?php
  }
}

function journalFiles(): array {
  return array_reverse(glob(JOURNALDIR."*.org"));
}

function cleanText(string $text): string {
  $text = str_replace("\r\n", "\n", $text); // windows -> unix
  $text = str_replace("\r", "\n", $text);   // remaining -> unix
  return $text;
}

function newEntry(): array {
  $content = $_POST["content"];
  if (empty($content)) {
    return array(
      "msg" => "Entry needs at least 2 lines: 1st title rest content of the entry.",
      "success" => false
    );
  }

  $journalFileName = JOURNALDIR . date("Ymd") . ".org";

  $contents = explode("\n", cleanText($content), 2);
  if (!$contents || count($contents) < 2) {
    return array(
      "msg" => "Entry needs at least 2 lines: 1st title rest content of the entry.",
      "success" => false
    );
  }

  $title = "** " . date("h:i") . " " . rtrim(strtok($contents[0], "\n"));
  $text = $contents[1];

  if (!in_array($journalFileName, journalFiles())) {
    // start new file for the day
    $header = "* " . date("l, j F Y");
    $fileContent = implode("\n", array($header, $title, $text));
    file_put_contents($journalFileName, $fileContent);
    return array(
      "msg" => "Added first entry " . date("h:i") . " for day " . date("l, j F Y") . ".",
      "success" => true
    );
  } else {
    // append to current day
    $fileContent = implode("\n", array("\n", $title, $text));
    file_put_contents($journalFileName, $fileContent, FILE_APPEND | LOCK_EX);
    return array(
      "msg" => "Added new entry " . date("h:i") . " for day " . date("l, j F Y") . ".",
      "success" => true
    );
  }
}

function readJournal(): string {
  $journalContent = "";
  foreach(journalFiles() as $dayEntry) {
    $journalContent = $journalContent . file_get_contents($dayEntry) . "\n";
  }
  return $journalContent;
};

function renderJournal(): void {
  $orgile = new orgile();
  echo $orgile->orgileThis(readJournal());
}

handlePostRedirect();

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
      <?php renderForm() ?>
      <?php renderJournal(); ?>
    </div>
  </body>
</html>
