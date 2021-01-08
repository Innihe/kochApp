<?php
  include "db.php";
  session_start();

  if(isset($_GET['titel']) && isset($_GET['zutaten']) && isset($_GET['beschreibung']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
  {
    //Input holen und sanitizen weil er in die DB kommt
    $titel = filter_var($_GET['titel'], FILTER_SANITIZE_STRING);
    $zutaten = filter_var($_GET['zutaten'], FILTER_SANITIZE_STRING);
    $beschreibung = filter_var($_GET['beschreibung'], FILTER_SANITIZE_STRING);
    $benutzer = $_SESSION['benutzername'];

    dbNewFav($titel, $zutaten, $beschreibung, $benutzer);
  }
  $favs = file_get_contents("../public/favs.php");
  echo $favs;
 ?>
