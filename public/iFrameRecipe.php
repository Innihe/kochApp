<html>
<link rel="shortcut icon" type="image/x-icon" href="./3475favicon.ico">
<link rel="stylesheet" href="stylesheet2.css">
<?php
  include "../private/crawl.php";

    if(isset($_GET['link']))
    {
      $link = urldecode($_GET['link']);

      //dont ask why lol
      $link = str_replace("amp;", "", $link);
      
      $site = file_get_contents($link);

      //Sonderzeichen und Umlaute fixen
      $site = mb_convert_encoding($site, 'HTML-ENTITIES', "iso-8859-1");
      $titel = copyStringBetween($site,"<H1>","</H1>")['copiedString'];
      $zutaten = copyStringBetween($site, "<PRE>", "\n\n")['copiedString'];
      $beschreibungOffset = copyStringBetween($site, "<PRE>", "\n\n")['lastSearchEndPos'];
      $beschreibungLength = strpos($site, "</PRE>") - $beschreibungOffset;
      $beschreibung = substr($site, $beschreibungOffset, $beschreibungLength);



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





      echo "<div id='iFrameTitel'>".$titel."</div>";
      echo "<br>";
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
      echo "<div id='iFrameQuelle'> Quelle: <a href=".$link.">$link</a></div>";

      //echo "<br><br><br>DEBUG: ".print_r($beschreibung);


    }

 ?>
 </html>
