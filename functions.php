<?php

// optinale Parameter können immer nur am Ende der Parameterliste angegeben werden
error_reporting(-1);
ini_set('log_errors', 1);

/**
* @return ressource
*/
function dbConnect ( array $options )
{
	static $db = null;

	if( $db instanceof mysqli )
	{
		return $db;
	}
	
	// MySql Objekt erzeugen
	$db = new mysqli( $options["Hostname"], $options["Username"], $options["Password"], $options["Databasename"] );

	// Nur bei kritischen Fehlern wie Datenbankverbindung fehlgeschlagen weiterleiten
	// Wenn mysqli_connect_errno() nicht null zurückgibt ist ein Fehler aufgetreten
	if ( mysqli_connect_errno() )
	{
		// entstandener Fehler als Parameter mitgeben
		errorLog();

		if( $db->connect_error )
		{
	    	header("Location: error.php");
		}

	    die('Datenbank nicht erreichbar.');
	}

	return $db;
}

function debug ()
{
	// liefert Funktionsargumente als Array
	$argumentList = func_get_args();
	
	foreach( $argumentList as $argument )
	{
		// speichert Fehlermeldungen in Datei
		error_log( var_export($argument, true), 3, "log/error.log" );
	}
}

/**
 * @return array
 */
function sanitizeData ( array $params )
{	
	$data = array(
		// eingaben nach ungültigen Zeichen filtern
		"firstname" => trim( filter_var($params["firstname"], FILTER_SANITIZE_STRING) ),
		"lastname" => trim( filter_var($params["lastname"], FILTER_SANITIZE_STRING) ),
		"email" => filter_var( trim($params["email"]), FILTER_VALIDATE_EMAIL ),
		"textinput" => trim( filter_var($params["textinput"], FILTER_SANITIZE_STRING) ),
		"date" => date('m/d/Y, H:i:s')
	);

	return $data;
}

/**
 * @return array
 */
function validateForm ( array $params )
{
	// wenn vorher gefilterte Eingaben leer sein sollten oder false gib Array mit falschen Schlüssel zurück
	$invalidKeys = array();

	foreach ($params as $key => $value)
	{
		if( empty($value) )
		{
			$invalidKeys[] = $key;
		}
	}

	return $invalidKeys;
}

/**
 * @return int
 */
function savePosts ( array $params, mysqli $db )
{
	$insert = 'INSERT INTO
					guestbook(firstname, lastname, email, content)
				VALUES 
				(
					"'. $db->real_escape_string( $params["firstname"] ) .'",
					"'. $db->real_escape_string( $params["lastname"] ). '",
					"'. $db->real_escape_string( $params["email"] ) .'",
					"'. $db->real_escape_string( $params["textinput"] ) .'"
				)';

	$db->query( $insert );

	$lastId = $db->insert_id;

	return $lastId;
}

/**
 * @return int
 */
function totalEntries( mysqli $db )
{
   // wie viele Zeilen hat Tabelle
   $sql = "SELECT COUNT(*) as anzahl FROM guestbook";
   $result = $db->query($sql);
   $row = mysqli_fetch_row($result);

	// Soll alle Zeilen als int zurückgeben
   $count = (int) $row[0];

   return $count;
}

/**
 * @return float
 */
function totalPages( $count, $rowsperpage )
{
   // Maximale Seitenzahl berechnen
   $totalpages = ceil($count / $rowsperpage);

   return $totalpages;
}

/**
 * @return array
 */
function getPosts ( mysqli $db, $rowsperpage, $currentpage )
{
	$offset = ($currentpage - 1) * $rowsperpage;

	// Ausgabe in Variable speichern
	$sql = "SELECT
				firstname, lastname, email, content, created
			FROM
				guestbook

			ORDER BY created DESC
			LIMIT " . (int) $offset . ", ". (int) $rowsperpage ."";

	$dbRead = $db->query( $sql );

	$postings = array();

	while( $row = $dbRead->fetch_assoc() )
	{
		array_push($postings, $row);
	}

	return $postings;
}

/**
 * @return string
 */
function displayPosts ( $post )
{
	$output = "";

	foreach($post as $entrie)
	{
		$firstname = $entrie["firstname"];
		$lastname = $entrie["lastname"];
		$content = $entrie["content"];
		$email = $entrie["email"];
		$created = $entrie["created"];

		$output .= <<<EOD
			<article class="entries">
				<p>Autor:<br>$firstname $lastname</p>
				<p>$content</p>
				<p>E-Mail:<br>$email</p>
				<p>Veröffentlicht am:<br>$created</p>
			</article>
EOD;
	}

	return $output;
}

/**
 * @return array
 */
function getErrorMessages ( $invalidInput )
{
	$errorMessages = array();

	foreach ($invalidInput as $key => $value)
	{
		// bei ungültiger Eingabe der Felder Fehler in array speichern
		switch ($value)
		{
			case "firstname":
				$errorMessages[$value] = "Bitte geben Sie einen validen Vornamen ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Leer- oder Sonderzeichen.";
				break;

			case "lastname":
				$errorMessages[$value] = "Bitte geben Sie einen validen Nachnamen ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Leer- oder Sonderzeichen.";
				break;

			case "email":
				$errorMessages[$value] = "Bitte geben Sie eine gültige E-Mail-Adresse ein. Sie sollte wie folgendes Beispiel aussehen: mustermann@example.com";
				break;

			case "textinput":
				$errorMessages[$value] = "Bitte geben Sie einen Text ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Sonderzeichen.";				
				break;
		}
	}

	return $errorMessages;
}

# zufälligen String generieren, bestehend aus 2 Vokalen und 2 Konsonanten
function randomString ()
{
  $kons = 'bcdfghjklmnpqrstvwxz';
  $voka = 'aeiou';

  return $kons[rand(0,19)].$voka[rand(0,4)].
         $kons[rand(0,19)].$voka[rand(0,4)];
}
