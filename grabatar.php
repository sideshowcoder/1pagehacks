<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Display Gravatar image for email with size</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>

  <body>

    <?php

    function gravatarUrl(string $email, int $size): string
    {
      $emailHash = md5($email);
      return "https://www.gravatar.com/avatar/".$emailHash."?s=".$size;
    };

    if (!empty($_GET["email"]) && !empty($_GET["size"]))
    {
      $imgUrl = gravatarUrl($_GET["email"], $_GET["size"]);
      echo '<a download="'.$imgUrl.'" href="'.$imgUrl.'">';
      echo '<img src="'.$imgUrl.'" />';
      echo '</a>';
    }

    ?>

    <form action="grabatar.php">
      <label for="email">Gravatar email</label><br>
      <input type="text" autofocus="autofocus" id="email" name="email"><br>
      <label for="size">Size in pixel</label><br>
      <input type="number" id="size" name="size" value="600"><br>
      <input type="submit" value="Fetch Gravatar">
    </form>

  </body>
</html>
