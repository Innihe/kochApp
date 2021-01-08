<?php
	session_start();
	if(isset($_SESSION['loggedIn'])) //Wenn User eingeloggt
	{
		session_destroy(); //Session zerstören um User auszuloggen
		header('Location: ../public/index.php'); //Zurückleiten auf Hauptseite
		die();
	}
	else //Fallback falls uneingeloggter User irgendwie auf diese Seite kommt
	{
		header('Location: ../public/index.php');
		die();
	}
?>
