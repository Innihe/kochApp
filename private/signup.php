<?php
	session_start();
	require 'db.php'; //DB Funktionalität einbinden

	if(trim($_REQUEST['benutzername']) && trim($_REQUEST['passwort'])) //Wenn Benutzername und Passwort sowohl Zeichen enthalten als auch nicht nur Whitespace sind
	{
    if($_REQUEST['passwort'] !== $_REQUEST['passwort-repeat'])
    {
      header("Refresh: 5; url=../public/signup.php"); // In 5 Sekunden zurückleiten auf signup.php
      echo "Passwörter stimmen nicht überein!<br>Sie werden weitergeleitet...";       //fail meldung
      die();
    }
		$benutzername = trim($_REQUEST['benutzername']); //Benutzername übernehmen, Whitespace trimmen
		$passwort = $_REQUEST['passwort'];


//Wenn Nutzer Registrierungsformular verwendet hat

			if(isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) //Wenn Mailadresse eingetragen und augenscheinlich auch eine valide Email Adresse
			{
				$email = $_REQUEST['email']; //Email übernehmen

				//Abfrage an DB ob Nutzername und E-Mail noch frei wenn ja neu registrieren, Rückgabewert ist bool true/false
				$registrationSuccess = dbRegister($benutzername, $passwort, $email);

				if($registrationSuccess)
				{
					header("Refresh: 5; url=../public/index.php"); // In 5 Sekunden zurückleiten auf signup.php
					echo "Registrierung erfolgreich!<br>Sie können sich nun einloggen.<br>Sie werden weitergeleitet...";       //Success meldung
					die();
				}
				else
				{
					header("Refresh: 5; url=../public/signup.php"); // In 5 Sekunden zurückleiten auf signup.php
					echo "Registrierung fehlgeschlagen - Benutzername bereits vergeben oder unter dieser E-Mail Adresse wurde bereits ein Account registriert.";       //Fehlermeldung
					die();
				}
      }
	}
	else
	{
		header("Refresh: 5; url=../public/signup.php"); // In 5 Sekunden zurückleiten auf register.php
		echo "Bitte Benutzernamen und Passwort eingeben.";       //Fehlermeldung
		die();
	}

?>
