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
	    <div class="title">Registrieren</div>

			<div class="signup">
		 <form action= "../private/signup.php" method="post">
		 <input type="text" name="benutzername" placeholder="Username" required>
		 <br>
		 <input type="text" name="email" placeholder="E-mail" required>
		 <br>
		 <input type="password" name="passwort" placeholder="Password" required>
		 <br>
		 <input type="password" name="passwort-repeat" placeholder="Repeat password" required>
		 <br>
		 <button type="submit" name="signup-submit">Signup</button>
		 </form>
	 </div>


        </main>


				    </main>


				    <footer>

				    </footer>

				  </body>

				</html>
