<?php declare(strict_types=1);

require_once "orgile.php";
$orgile = new orgile();

session_start();
define("JOURNALCONFIG", parse_ini_file("journal.ini"));

function handlePostRedirect(): void {
  if(!empty($_POST)) {
    $result = newEntry();
    $_SESSION["msg"] = $result["msg"];
    if($result["success"]) {
      header( 'HTTP/1.1 303 See Other' );
      header( 'Location: '.$_SERVER["PHP_SELF"] );
      exit();
    }
  }
}

function renderMessage(): void {
  if(isset($_SESSION["msg"])) {
    echo $_SESSION["msg"];
    session_destroy();
  }
}

function renderTextArea(): void {
  $content = isset($_POST["content"]) ? $_POST["content"] : "";
  echo '<textarea id="content" name="content" rows="12" cols="70" placeholder="Journal entry, first line becomes title.">'.$content.'</textarea>';
}

function journalFiles(): array {
  return array_reverse(glob(JOURNALCONFIG["path"] . "*.org"));
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

  $journalFileName = JOURNALCONFIG["path"] . date("Ymd") . ".org";

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
  global $orgile;
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
       max-width: 600px;
       text-align: left;
       margin: 20px auto;
     }
    </style>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <body>
    <div id="container">
      <?php renderMessage() ?>

      <form method="post">
        <?php renderTextArea() ?>
        <input type="submit" value="Add new entry">
      </form>

      <?php renderJournal(); ?>
    </div>
  </body>
</html>
