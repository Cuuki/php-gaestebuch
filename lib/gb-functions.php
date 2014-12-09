<?php

/**
 * 
 * @return array
 */
function sanitizeData ( array $params )
{
    $data = array(
        // eingaben nach ungültigen Zeichen filtern
        "firstname" => trim( filter_var( $params["firstname"], FILTER_SANITIZE_STRING ) ),
        "lastname" => trim( filter_var( $params["lastname"], FILTER_SANITIZE_STRING ) ),
        "email" => filter_var( trim( $params["email"] ), FILTER_VALIDATE_EMAIL ),
        "textinput" => trim( filter_var( $params["textinput"], FILTER_SANITIZE_STRING ) ),
        "date" => date( 'm/d/Y, H:i:s' )
    );

    if ( mb_strlen( $data['textinput'] ) >= 1000 )
    {
        $data['textinput'] = false;
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

    foreach ( $params as $key => $value )
    {
        if ( empty( $value ) )
        {
            $invalidKeys[] = $key;
        }
    }

    return $invalidKeys;
}

/**
 * TODO: Doctrine
 * @return int
 */
function savePosts ( array $params, mysqli $db )
{
    $insert = 'INSERT INTO
					guestbook(firstname, lastname, email, content)
				VALUES 
				(
					"' . $db->real_escape_string( $params["firstname"] ) . '",
					"' . $db->real_escape_string( $params["lastname"] ) . '",
					"' . $db->real_escape_string( $params["email"] ) . '",
					"' . $db->real_escape_string( $params["textinput"] ) . '"
				)';

    $db->query( $insert );

    return $db->insert_id;
}

/**
 * TODO: Doctrine
 * @return array
 */
function getEntry ( mysqli $db, $id )
{
    $sql = 'SELECT
                firstname, lastname, email, content, created
            FROM
                guestbook
            WHERE
            	id_entry = "' . $id . '"';

    $dbRead = $db->query( $sql );
    $userdata = array();

    $row = $dbRead->fetch_assoc();

    array_push( $userdata, $row );

    return $userdata;
}

/**
 * TODO: Doctrine
 * @return array
 */
function getPosts ( mysqli $db, $rowsperpage, $currentpage )
{
    $offset = ($currentpage - 1) * $rowsperpage;

    // Ausgabe in Variable speichern
    $sql = "SELECT
				id_entry, firstname, lastname, email, content, created
			FROM
				guestbook

			ORDER BY created DESC
			LIMIT " . (int) $offset . ", " . (int) $rowsperpage . "";

    $dbRead = $db->query( $sql );

    $postings = array();

    while ( $row = $dbRead->fetch_assoc() )
    {
        array_push( $postings, $row );
    }

    return $postings;
}

/**
 * TODO: Template
 * @return array
 */
function getErrorMessages ( $invalidInput )
{
    $errorMessages = array();

    foreach ( $invalidInput as $key => $value )
    {
        // bei ungültiger Eingabe der Felder Fehler in array speichern
        switch ( $value )
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
                $errorMessages[$value] = "Bitte geben Sie einen Text ein, welcher 1000 Zeichen nicht überschreitet. Lassen Sie das Feld nicht frei und verwenden Sie keine Sonderzeichen.";
                break;

            case "username":
                $errorMessages[$value] = "Bitte geben Sie einen Usernamen ein. Lassen Sie das Feld nicht frei und verwenden Sie keine Sonderzeichen.";
                break;

            case "useremail":
                $errorMessages[$value] = "Bitte geben Sie eine gültige E-Mail-Adresse ein. Sie sollte wie folgendes Beispiel aussehen: test@example.com";
                break;

            case "password":
                $errorMessages[$value] = "Bitte geben Sie ein sicheres Passwort ein. Lassen Sie das Feld nicht frei.";
                break;
        }
    }

    return $errorMessages;
}
