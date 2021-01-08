<!DOCTYPE html>
<?php
	session_start();
	include "../private/db.php";
?>
<html lang="en" dir="ltr">
<link rel="shortcut icon" type="image/x-icon" href="./3475favicon.ico">
<link rel="stylesheet" href="stylesheet1.css">
  <head>

    <meta charset="utf-8">
    <title>Schmeißrein</title>

  </head>
  <body>

    <header>

      <div id="title">Schmeißraus</div>

    </header>
    <main>
      <iframe name="iframe1" src="iframelandingpage.html" frameborder="0"></iframe>
      <?php

			//favoriten aus db holen
			$favObject = dbPullFavs($_SESSION['benutzername']);
			//ergebnisobjekt zu liste

				echo "<ul>"; //Liste beginnen
				while ($rezept = $favObject->fetch_object()) // Solange mittels fetch_object() ein weiteres Objekt (eine weitere Zeile) aus dem Objekt $ergebnis der Datei db_pull_all.php
			{		                                           //der Variable $datensatz zugeordnet werden kann
				echo "<li><a href='./iFrameRecipe.php?link=lokalesrezept&name=$rezept->titel' target='iframe1'>$rezept->titel</a></li><br>";
			}
				echo "</ul>"; //Liste zuende


       ?>

       <footer>
          <div id="top"><a href="#title">Top</a></div>
          <div id="home"><a href="index.php">Start</a></div>
       </footer>

    </main>



  </body>

</html>
