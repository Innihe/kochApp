<?php
  include "db.php";
  session_start();

  if(isset($_GET['titel']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
  {
    //Input holen und sanitizen weil er in die DB kommt
    $titel = filter_var($_GET['titel'], FILTER_SANITIZE_STRING);
    $benutzer = $_SESSION['benutzername'];
    $titel = urldecode($titel);
    $titel = htmlentities($titel);

    dbRemoveFav($titel, $benutzer);
  }
  ?>
<html>
<script>
window.history.back();
</script>
