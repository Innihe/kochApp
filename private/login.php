<?php
	session_start();
	require 'db.php'; //DB Funktionalität einbinden

	if(trim($_REQUEST['benutzername']) && trim($_REQUEST['passwort'])) //Wenn Benutzername und Passwort sowohl Zeichen enthalten als auch nicht nur Whitespace sind
	{
		$benutzername = trim($_REQUEST['benutzername']); //Benutzername übernehmen, Whitespace trimmen
		$passwort = $_REQUEST['passwort'];


		//echo "DEBUG:<br>Benutzername: ".$benutzername." Passwort: ".$passwort."<br>";


			//Abfrage an DB ob es Benutzernamen/Hashpasswort Kombi gibt, Rückgabewert ist bool true/false
			$loginSuccess = dbLogin($benutzername, $passwort);

			if($loginSuccess)
			{
				$_SESSION['loggedIn'] = true;
				$_SESSION['benutzername'] = $benutzername;
				header('Location: ../public/index.php'); //Nach erfolgreichem Login Zurückleitung auf Hauptseite
				die();
			}
			else
			{
				header("Refresh: 5; url=../public/login.html"); // In 5 Sekunden zurückleiten auf index.php
					echo "Benutzername und Passwort stimmen nicht überein! Sie werden zurückgeleitet!";       //fail meldung
					die();
			}
	}
	else
	{
		header("Refresh: 5; url=../public/index.php"); // In 5 Sekunden zurückleiten auf index.php
		echo "Bitte Benutzernamen und Passwort eingeben.";       //Fehlermeldung
		die();
	}

?>
