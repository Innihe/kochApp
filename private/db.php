<?php
	//Fehlermeldungen behandeln
	error_reporting(E_ALL); //Alle Fehlermeldungen anzeigen, aus Sicherheitsgründen ausschalten wenn im Produktivbetrieb
	/* error_reporting(0);     //Alle Fehlermeldungen ausblenden, aus Sicherheitsgründen anschalten wenn im Produktivbetrieb */


	/// connectDB() baut eine Verbindung zur Datenbank auf und liefert das erstellte Objekt als Rückgabewert
	function connectDB()
	{
		//Datenbank Login Daten
		$servername = "localhost";
		$username = "root";
		$password = "";
		$db       = "schmeissrein";

		//Verbindung zur Datenbank aufbauen
		$conn = new mysqli($servername, $username, $password, $db);

		//Verbindung überprüfen
		if ($conn->connect_error)
		{														 		//!Ersetzen durch $db->connect_errno und exakte Fehlermeldung nicht ausgeben
			die("Verbindung fehlgeschlagen: ".$conn->connect_error); //Falls es Fehlermeldung gibt abbrechen und Fehlermeldung ausgeben
		}
		//echo "DEBUG db_access.php: Verbindung läuft<br>";

		$conn->set_charset('utf8'); //Umlaute
		return $conn;
	}

  function dbRegister($benutzername, $passwort, $email)
	{
		$conn = connectDB(); //Funktion connectDB() nutzen um Verbindung herzustellen und Rückgabeobjekt in $conn speichern

		//Abfrage über Prepared Statement weil Nutzerinput im Spiel ist
		//Checken ob sowohl Nutzername als auch Email noch frei sind
		$ergebnis = $conn->prepare("SELECT zugangsberechtigung.Benutzername, kontaktdaten.Email
									FROM zugangsberechtigung, kontaktdaten
									WHERE Benutzername = ?
									OR Email = ?")
						or die($conn->error);
		$ergebnis->bind_param("ss", $benutzername, $email);
		$ergebnis->execute();
		$ergebnis->store_result(); // Sonst geht zweite Query nicht

		if($ergebnis->num_rows == 0)
		{
			//Einstellung für password_hash(), um so höher die Kosten umso sicherer, muss aber auf die jeweilig verwendete Server Hardware eingestellt werden
			//damit alles flüssig läuft -> Kosten/Nutzen Balance
			$options = [ 'cost' => 10,];
			$hashedPasswort = password_hash($passwort, PASSWORD_DEFAULT, $options); //Passwort  hashen und den Hash speichern

			$insertQuery = $conn->prepare("INSERT INTO zugangsberechtigung (Benutzername, Passwort)
											VALUES (?, ?)") //Accountzugangsberechtigung anlegen vorbereiten
							or die($conn->error);
			$insertQuery->bind_param("ss", $benutzername, $hashedPasswort);
			$insertQuery->execute(); //Accountzugangsberechtigung anlegen

			//Vorbereitung für nächste Query
			$insertQuery = $conn->prepare("INSERT INTO kontaktdaten (Email)
											VALUES (?)")
							or die($conn->error);
			$insertQuery->bind_param("s", $email);
			$insertQuery->execute();

			$insertQuery = $conn->prepare("INSERT INTO benutzerkonto (Benutzername, Email)
											VALUES (?, ?)")
							or die($conn->error);
			$insertQuery->bind_param("ss",$benutzername, $email);
			$insertQuery->execute();
			return true;
		}
		else
		{
			return false;
		}
		dbClose($conn);
	}

  function dbLogin($benutzername, $passwort)
	{

		$conn = connectDB(); //Funktion connectDB() nutzen um Verbindung herzustellen und Rückgabeobjekt in $conn speichern
		//Abfrage über Prepared Statement weil Nutzerinput im Spiel ist
		$ergebnis = $conn->prepare("SELECT Benutzername, Passwort
									FROM zugangsberechtigung
									WHERE Benutzername = ?")
						or die($conn->error);
		$ergebnis->bind_param("s", $benutzername);
		$ergebnis->execute();
		$account = $ergebnis->get_result(); //Ergebnis speichern

		if($account->num_rows == 1)
		{
			$account = $account->fetch_object(); //Als Objekt speichern
			if (password_verify($passwort, $account->Passwort)) //Wenn Hashes übereinstimmen
			{
				return true;
			}
		}
		else
		{
			return false;
		}
		dbClose($conn);
	}
