<?php

//$searchParamArray = array mit strings der suchbegriffe
//durchsucht unix kochbuch mit übergebenen suchparametern
//und liefert assoc array rezeptname=>rezeptlink
function searchUnixKochbuch($searchParamArray)
{
  $searchString = "";
  foreach($searchParamArray as $value)
  {
    $searchString .= "+".$value;
  }
  $searchString = ltrim($searchString,"+");
  echo "DEBUG: searchString: ".$searchString;

  //TO DO searchstring einbinden
  $url = "http://kochbuch.unix-ag.uni-kl.de/bin/stichwort.php?suche=tomate+gurke&andor=AND&submit=Anfrage+abschicken";
  $site = file_get_contents($url);

  //Liefert statt Suchergebnis Hauptseite, allerdings ist dort im Quelltext eine
  //"PHPSESSID" zu finden, sie hat eine Länge von 32 Zeichen,
  //BeispieL PHPSESSID=a5082c7a686ea6e1b0f0f3bd72ad9b92)
  $sessionIDStart = strpos($site, "PHPSESSID=") + 10;

  //Session ID extrahieren
  $sessionID = substr($site, $sessionIDStart, 32);
  echo $sessionID."<br>";


  //Anfrage nochmal mit Session ID über stream_context_create() als Cookie senden
  //Method, Header und Cookies einstellen
  $streamOptions = array('http'=>array('method'=>"GET","header"=>"Cookie: PHPSESSID=".$sessionID)); //im PHP Manual ."\r\n", falls nicht geht

  //Optionen zum mitsenden vorbereiten
  $context = stream_context_create($streamOptions);

  //Erneute Suchanfrage mit übermittlung des Cookies
  $site = file_get_contents($url, false, $context);

  //Links fixen, so dass sie auf den original Server zeigen
  $site = str_replace('<A HREF="/', '<A HREF="http://kochbuch.unix-ag.uni-kl.de/', $site);

  //Sonderzeichen und Umlaute ReflectionExtension
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

  echo $site;

  //TO DO assoc array raus
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
  return array('copiedString' => $subString, 'lastSearchEndPos' => $kopiereAbPosition);
}


?>
