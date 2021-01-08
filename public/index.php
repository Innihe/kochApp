<!DOCTYPE html>
<?php
	session_start();
?>
<html lang="en" dir="ltr">
<link rel="shortcut icon" type="image/x-icon" href="./3475favicon.ico">
<link rel="stylesheet" href="stylesheet.css">
  <head>

    <meta charset="utf-8">
    <title>Schmeißrein</title>

  </head>
  <body>

    <header>

    </header>
    <main>

    <div class="title">Schmeißrein</div>
    <div class="msg">Wilkommen auf Schmeißrein.
      Auf dieser Seite kannst du einfach deine Zutaten einwerfen und es werden dir viele tolle Rezepte angezeigt. </div>

      <div class="login">
        <form action="../private/login.php" method="post">
          <input type="text" name="benutzername" placeholder="Username">
          <br>
          <input type="password" name="passwort" placeholder="Password">
          <br>
          <button type="submit" name="login-submit">login</button>
        </form>

        <form action="../private/logout.php" method="post">
          <button type="submit" name="logout-submit">logout</button>
        </form>
        <a href="signup.php">Signup</a>
      </div>

      <div class="search">
        <form action="result.php" method="post">
          <input type="text" name="ingredients" placeholder="Suchen">
          <br>
          <button type="submit" name="query">Suchen</button>

      </div>
    </main>


    <footer>
      <a href="Impressum.php">Impressum</a>
      <a href="Beispiel">Beispiel</a>
      <a href="Beispiel">Beispiel</a>
      <a href="Beispiel">Beispiel</a>
      <a href="Beispiel">Beispiel</a>
    </footer>

  </body>

</html>
