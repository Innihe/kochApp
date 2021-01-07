<?php

function grabRecipe($link)
{

}

function crawl()
{
  if(isset($_POST['query']))
  {
    //Übergebene Suchparameter zu Arrays und Seiten crawlen
    //Zuerst userinput säubern
    $ingredients = filter_var($_POST['ingredients'], FILTER_SANITIZE_STRING);

    //Suchbegriffe in Array umwandeln
    $searchParamArray = explode(" ", $ingredients);

    //Webseiten durchsuchen und Ergebnis speichern
    $unixKBRecipeArray = searchUnixKochbuch($searchParamArray);
    $jobUndFitRecipeArray = searchJobUndFitKochbuch($searchParamArray);

    //Wenn mehr als eine Suche erfolgreich die Ergebnisse mergen und alphabetisch sortieren
    if ($unixKBRecipeArray !== NULL && $jobUndFitRecipeArray !== NULL)
    {
      $resultArray = array_merge($unixKBRecipeArray, $jobUndFitRecipeArray);
      ksort($resultArray);
    }
    elseif ($unixKBRecipeArray == NULL && $jobUndFitRecipeArray !== NULL)
    {
      $resultArray = $jobUndFitRecipeArray;
    }
    elseif ($unixKBRecipeArray !== NULL && $jobUndFitRecipeArray == NULL)
    {
      $resultArray = $unixKBRecipeArray;
    }
    else
    {
      $resultArray = array("Leider kein passendes Rezept gefunden. Klicke hier um zurück zur Suche zu gelanden." => "./index.php");
    }

    //assoc array zu html ungeordneter liste
    echo "<ul><br>";
    foreach($resultArray as $recipeName => $link)
    {
      $link = urlencode($link);
      echo "<li><a href='./iFrameRecipe.php?link=$link' target='iframe1'>$recipeName</a></li><br>";
    }
    echo "</ul><br>";
  }
}
//$searchParamArray = array mit strings der suchbegriffe
//durchsucht unix kochbuch mit übergebenen suchparametern
//und liefert assoc array rezeptname=>rezeptlink
//                                 todo =>rezeptzutaten
//                                 todo =>rezeptanweisungen
function searchUnixKochbuch($searchParamArray)
{
  $searchString = "";
  foreach($searchParamArray as $value)
  {
    $searchString .= "+".$value;
  }
  $searchString = ltrim($searchString,"+");
  //echo "DEBUG: searchString: ".$searchString;


  //TO DO searchstring einbinden
  $url = "http://kochbuch.unix-ag.uni-kl.de/bin/stichwort.php?suche=".$searchString."&andor=AND&submit=Anfrage+abschicken";
  $site = file_get_contents($url);
  //echo "DEBUG URL: ".$url;

  //Liefert statt Suchergebnis Hauptseite, allerdings ist dort im Quelltext eine
  //"PHPSESSID" zu finden, sie hat eine Länge von 32 Zeichen,
  //BeispieL PHPSESSID=a5082c7a686ea6e1b0f0f3bd72ad9b92)
  $sessionIDStart = strpos($site, "PHPSESSID=") + 10;

  //Session ID extrahieren
  $sessionID = substr($site, $sessionIDStart, 32);

  //Session ID in globaler Variable speichern um HTML UL mit Session ID in den
  // Links generieren
  global $unixKochbuchPHPSessID;
  $unixKochbuchPHPSessID = $sessionID;
  //echo $sessionID."<br>";


  //Anfrage nochmal mit Session ID über stream_context_create() als Cookie senden
  //Method, Header und Cookies einstellen
  $streamOptions = array('http'=>array('method'=>"GET","header"=>"Cookie: PHPSESSID=".$sessionID)); //im PHP Manual ."\r\n", falls nicht geht

  //Optionen zum mitsenden vorbereiten
  $context = stream_context_create($streamOptions);

  //Erneute Suchanfrage mit übermittlung des Cookies
  $site = file_get_contents($url, false, $context);

  //Links fixen, so dass sie auf den original Server zeigen
  $site = str_replace('<A HREF="/', '<A HREF="http://kochbuch.unix-ag.uni-kl.de/', $site);

  //Sonderzeichen und Umlaute fixen
  $site = mb_convert_encoding($site, 'HTML-ENTITIES', "iso-8859-1");

  ///////** Links in ein Array **\\\\\\\\\\
  //Anzahl der Rezeptlinks ermitteln, wird auf der Seite mit "Anzahl Treffer:" ausgegeben
  $anzahlRezepte = copyStringBetween($site, "Anzahl Treffer: ", "<")['copiedString'];
  //echo "DEBUG: AnzahlRezepte: ".$anzahlRezepte;

  //Durch gelieferte <UL> loopen und jeden Namen + zugehörigen Link in Array speichern
  $linkListOffset = 0;
  for($i = $anzahlRezepte; $i > 0; $i--)
  {
    //Rezeptlink holen
    $rezeptLinkErgebnis = copyStringBetween($site, '<LI><A HREF="', '">', $linkListOffset);
    //PHP Session ID anhägen sonst Problem
    $rezeptLinkErgebnis['copiedString'] .= "&PHPSESSID=".$unixKochbuchPHPSessID;

    //Offset anpassen um Suche nach dem zuletzt gespeicherten Link zu beginnen
    $linkListOffset = $rezeptLinkErgebnis['lastSearchEndPos'];
    //Rezeptnamen holen
    $rezeptNameErgebnis = copyStringBetween($site, '">', '</A>', $linkListOffset);

    //Ergebnis an assoc array anhängen array[rezeptname]=>[rezeptlink]
    $rezeptArray[$rezeptNameErgebnis['copiedString']] = $rezeptLinkErgebnis['copiedString'];

    //Offset zurücksetzen wenn alle Links aufgenommen
    if($i == 0){$linkListOffset = 0;}
  }
  //echo "DEBUG2: Link1: ".print_r($rezeptArray);

  //echo $site;

//check ob es rezepte gab wenn nicht NULL zurückliefern
  if(!is_numeric($anzahlRezepte) || $anzahlRezepte == 0)
  {
    $rezeptArray = NULL;
  }

  //assoc array raus
  return $rezeptArray;
}


//Kopiert einen Bereich aus einem String heraus und gibt ein assoc array wieder das den extrahierten Substring sowie
//parentstring = zu durchsuchender string, $leftDelimiter = ab welchem Ausdruck soll kopiert werden (exklusiv)
// $rightDelimiter = bis zu welchem Ausdruck soll kopiert werden (exklusiv)
//$offset = Beginnt erst ab Zeichen X mit der Suche
function copyStringBetween (String $parentString, String $leftDelimiter, String $rightDelimiter, int $offset = 0)
{
  $kopiereAbPosition = strpos($parentString, $leftDelimiter, $offset) + strlen($leftDelimiter);
  $kopiereBisPosition = strpos($parentString, $rightDelimiter,$kopiereAbPosition);
  $laenge = $kopiereBisPosition - $kopiereAbPosition;
  $subString = substr($parentString, $kopiereAbPosition, $laenge);
  return array('copiedString' => $subString, 'lastSearchEndPos' => $kopiereBisPosition);
}




function crawltest()
{
  if(isset($_POST['query']))
  {
    $searchParamArray = explode(" ", $_POST['ingredients']);
    $resultArray = jobUndFit($searchParamArray);

    echo "<ul><br>";
    foreach($resultArray as $recipe => $link)
    {
      echo "<li><a href='$link'>$recipe</a></li><br>";
    }
    echo "</ul><br>";
  }
}

function searchJobUndFitKochbuch($searchParamArray)
{
  $searchString = "";
  foreach($searchParamArray as $value)
  {
    $searchString .= "+".$value;
  }
  $searchString = ltrim($searchString,"+");
  //echo "DEBUG: searchString: ".$searchString;


  //searchstring einbinden
  $url = "https://www.jobundfit.de/rezepte/rezeptdatenbank/?tx_wwrecipe_fe1%5Bcat%5D%5B4%5D=0&tx_wwrecipe_fe1%5Bingredients%5D=0&tx_wwrecipe_fe1%5Bsearchtext%5D=".$searchString."&tx_wwrecipe_fe1%5Bsearch%5D=1";
  $site = file_get_contents($url);

  //Sonderzeichen und Umlaute fixen
  $site = mb_convert_encoding($site, 'HTML-ENTITIES', "iso-8859-1");
 //  echo $site;

  //Anzahl der Rezeptlinks ermitteln, wird auf der Seite vor " Suchergebnis(se):" ausgegeben
  //dont touch this shit ^^
  //die Formatierung unten ist wie gewollt auch wenns broken aussieht
  $anzahlRezepte = copyStringBetween($site, "</div>
</form></div>

<h2>", " Suchergebnis(se):</h2>")['copiedString'];

  //Rezepte Beginn ermitteln
  $offsetStart = strpos($site, " Suchergebnis(se):</h2>");

//Durch geliefertes HTML gehen und Titel und Links herauskopieren
$linkListOffset = $offsetStart;
for($i = $anzahlRezepte; $i > 0; $i--)
{
  //Rezeptlink holen
  $rezeptLinkErgebnis = copyStringBetween($site, '<a class="title" href="', '">', $linkListOffset);
  //Offset anpassen um Suche nach dem zuletzt gespeicherten Link zu beginnen
  $linkListOffset = $rezeptLinkErgebnis['lastSearchEndPos'];
  //Rezeptnamen holen
  $rezeptNameErgebnis = copyStringBetween($site, '">', '</a>', $linkListOffset);

  //Ergebnis an assoc array anhängen array[rezeptname]=>[rezeptlink]
  $rezeptArray[$rezeptNameErgebnis['copiedString']] = $rezeptLinkErgebnis['copiedString'];

  //Offset zurücksetzen wenn alle Links aufgenommen
  if($i == 0){$linkListOffset = 0;}
}


//check ob es rezepte gab wenn nicht NULL zurückliefern
  if(!is_numeric($anzahlRezepte) || $anzahlRezepte == 0)
{
  $rezeptArray = NULL;
}

//assoc array raus

return $rezeptArray;
}
?>
