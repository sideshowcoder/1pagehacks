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

    if ($_GET["email"])
    {
      $size = empty($_GET["size"]) ? 600 : $_GET["size"];
      $imgUrl = gravatarUrl($_GET["email"], $size);
      echo '<a download="'.$imgUrl.'" href="'.$imgUrl.'">';
      echo '<img src="'.$imgUrl.'" />';
      echo '</a>';
    }

    ?>

    <form action="/grabatar.php">
      <label for="email">Gravatar Email</label><br>
      <input type="text" autofocus="autofocus" id="email" name="email"><br>
      <label for="size">Size:</label><br>
      <input type="number" id="size" name="size"><br>
      <input type="submit" value="Fetch Gravatar">
    </form>

  </body>
</html>
