<?php


function searchUnixKochbuch()
{
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

  //Links in ein Array
  //Anzahl der Rezeptlinks ermitteln, wird auf der Seite mit "Anzahl Treffer:" ausgegeben
  $sucheAbPosition = strpos($site, "Anzahl Treffer: ") + 16;
  $sucheBisPosition = strpos($site, "<",strpos($site, "Anzahl Treffer: ") + 16);
  $laenge = $sucheBisPosition - $sucheAbPosition;

  $anzahlRezepte = substr($site, $sucheAbPosition, $laenge);
  echo "AnzahlRezepte: ".$anzahlRezepte;

  echo $site;
}



?>
