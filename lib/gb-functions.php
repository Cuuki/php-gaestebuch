<?php

error_reporting(-1);
ini_set('log_errors', 1);

/**
 * @return array
 */
function sanitizeData ( array $params )
{
	if( isset($params["username"]) && isset($params["useremail"]) && isset($params["password"]) )
	{
		$data = array(
			"username" => filter_var( trim($params["username"]), FILTER_SANITIZE_STRING ),
			"useremail" => filter_var( trim($params["useremail"]), FILTER_VALIDATE_EMAIL ),
			// Password hashen
			"password" => filter_var( trim($params["password"]), FILTER_SANITIZE_STRING )
		);
	}
	else
	{
		$data = array(
			// eingaben nach ungültigen Zeichen filtern
			"firstname" => trim( filter_var($params["firstname"], FILTER_SANITIZE_STRING) ),
			"lastname" => trim( filter_var($params["lastname"], FILTER_SANITIZE_STRING) ),
			"email" => filter_var( trim($params["email"]), FILTER_VALIDATE_EMAIL ),
			"textinput" => trim( filter_var($params["textinput"], FILTER_SANITIZE_STRING) ),
			"date" => date('m/d/Y, H:i:s')
		);
	}

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

	return $db->insert_id;
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
 * @return int
 */
function totalEntries( mysqli $db )
{
   // wie viele Zeilen hat Tabelle
   $sql = "SELECT COUNT(*) as anzahl FROM guestbook";
   $result = $db->query($sql);
   $row = mysqli_fetch_row($result);

	// Soll alle Zeilen als int zurückgeben
   return (int) $row[0];
}

/**
 * @return float
 */
function totalPages( $count, $rowsperpage )
{
	// Maximale Seitenzahl berechnen
   return ceil($count / $rowsperpage);
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
				$errorMessages[$value] = "Bitte geben Sie eine gültige E-Mail-Adresse ein. Sie sollte wie folgendes Beispiel aussehen: test@example.com";
				break;

			case "textinput":
				$errorMessages[$value] = "Bitte geben Sie einen Text ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Sonderzeichen.";				
				break;

			case "username":
				$errorMessages[$value] = "Bitte geben Sie einen Usernamen ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Sonderzeichen.";				
				break;

			case "useremail":
				$errorMessages[$value] = "Bitte geben Sie eine gültige E-Mail-Adresse ein. Sie sollte wie folgendes Beispiel aussehen: test@example.com";				
				break;

			case "password":
				$errorMessages[$value] = "Bitte geben Sie ein Password ein. Lassen Sie das Feld nicht frei.";				
				break;
		}
	}

	return $errorMessages;
}

