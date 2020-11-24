<?php
$url = "http://kochbuch.unix-ag.uni-kl.de/bin/stichwort.php?suche=tomate+gurke&andor=AND&submit=Anfrage+abschicken";
$site = file_get_contents($url);

echo $site;
