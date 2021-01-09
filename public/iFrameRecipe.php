<html>
<?php
	session_start();
?>
<link rel="shortcut icon" type="image/x-icon" href="./3475favicon.ico">
<link rel="stylesheet" href="stylesheet2.css">
<?php
  include "../private/crawl.php";
	include "../private/db.php";

    if(isset($_GET['link']))
    {
			$link = urldecode($_GET['link']);
			if($_GET['link'] != "lokalesrezept")
			{


				//file_get_contents link preparation
				$link = str_replace("&amp;", "&", $link);

				$site = file_get_contents($link);
				//Sonderzeichen und Umlaute fixen
				$site = mb_convert_encoding($site, 'HTML-ENTITIES', "iso-8859-1");
			}

      if(strpos($link, 'unix-ag') !== false)
      {

        $titel = copyStringBetween($site,"<H1>","</H1>")['copiedString'];
        $zutaten = copyStringBetween($site, "<PRE>", "\n\n")['copiedString'];
        $beschreibungOffset = copyStringBetween($site, "<PRE>", "\n\n")['lastSearchEndPos'];
        $beschreibungLength = strpos($site, "</PRE>") - $beschreibungOffset;
        $beschreibung = substr($site, $beschreibungOffset, $beschreibungLength);
      }
      elseif(strpos($link, 'jobundfit') !== false)
      {
        $titel = copyStringBetween($site,"<p><b>","</b></p>")['copiedString'];
        $zutaten = copyStringBetween($site, "<tr><th>Menge</th><th>Zutat</th></tr>", "</table>")['copiedString'];
        $beschreibung = copyStringBetween($site, "Zubereitung</h2>", '</div><div class="col-md-6 nutrients">')['copiedString'];
        $zutaten = str_replace("</td>", " ", $zutaten);
        $zutaten = strip_tags($zutaten);
      }
			elseif(strpos($link, 'lokalesrezept') !== false)
      {
				$rezept = dbPullRecipe($_GET['name'], $_SESSION['benutzername']);
        $titel = $rezept->titel;
        $zutaten = $rezept->zutaten;
        $beschreibung = $rezept->beschreibung;
      }




      //echo $site;

      $zutatenArray = explode("\n", $zutaten);

      //Leere Array Einträge und Html Tags löschen und
      foreach ($zutatenArray as $key => $value)
      {
        if (trim($value) == '' || $value == "<PRE>" || $value == "<BR>")
        {
          unset($zutatenArray[$key]);
        }
      }

      //Whitespaces trimmen
      $zutatenArray = array_map("trim", $zutatenArray);


      echo "<div id='iFrameTitel'>".$titel;
      //Wenn eingeloggt und Rezept noch nicht gespeichert, speichern anbieten
      if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true && !dbCheckForRecipe($_SESSION['benutzername'], $titel))
      {
				$encodedTitel = urlencode($titel);
				$encodedZutaten = urlencode($zutaten);
				$encodedBeschreibung = urlencode($beschreibung);
        echo "<div id=favButton><a style='background-color:green;' href='../private/addrecipe.php?titel=".$encodedTitel."&zutaten=".$encodedZutaten."&beschreibung=".$encodedBeschreibung."'>Speichern</a></div>";
      }
			elseif(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true && dbCheckForRecipe($_SESSION['benutzername'], $titel))
			{
				$encodedTitel = urlencode($titel);
				echo "<div id=unFavButton><a style='background-color:red;' href='../private/removerecipe.php?titel=$encodedTitel'>Löschen</a></div>";
			}
      echo "</div><br>";
      echo "<div id='iFrameZutaten'>";
      echo "<ul>";


      //Zutatenliste generieren
      foreach($zutatenArray as $key => $value)
      {
        $zutat = trim($value, ";");
        echo "<li>".$zutat."</li>";
      }
      echo "</ul>";
      echo "</div>";
      echo "<br>";
      echo "<div id=iFrameBeschreibung>";
      echo nl2br($beschreibung);
      echo "</div>";
      echo "<div id='iFrameQuelle'> Quelle: <a target='_blank' rel='noopener noreferrer' href=".$link.">$link</a></div>";

      //echo "<br><br><br>DEBUG: ".print_r($beschreibung);


    }

 ?>
 </html>
