<?php

use Symfony\Component\HttpFoundation\Response;

/**
 * @return array
 * */
function getMail ( $db, $postdata )
{
    // E-Mail von Eingabe aus DB auslesen
    $sql = 'SELECT
                id, useremail
            FROM
                user
            WHERE
            	useremail = "' . $postdata . '"';

    $dbRead = $db->query( $sql );

    $row = $dbRead->fetch_assoc();

    return $row;
}

/**
 * @return boolean
 */
function saveCode ( $db, $code, $id )
{
    // Code und ID von User der den Code angefordert hat in DB speichern
    $insert = 'INSERT INTO
					auth_codes(code, id_user)
				VALUES 
				(
					"' . $code . '",
					"' . $id . '"
				)';

    return $db->query( $insert );
}

$postdata = array(
    'email' => $email->get( 'email' )
);

$result = getMail( $db, $postdata['email'] );

// Abfrage ob E-Mail mit einer aus DB übereinstimmt
if ( $result['useremail'] == NULL )
{
    $render = $app['twig']->render( 'reset_form.twig', array(
        'message' => 'Es existiert kein Benutzer mit der angegebenen E-Mail Adresse.'
            ) );
    return new Response( $render, 404 );
}

$code = mt_rand( 1000, 9999 );

$subject = 'Authentifizierungscode Passwort-Neuvergabe';

$message = 'Bitte geben Sie folgenden Code: ' . $code . ' im Eingabefeld der Website ein.' . PHP_EOL . 'ACHTUNG der Code wird aus Sicherheitsgründen entfernt, nachdem er eingegeben wurde (Laden Sie die Seite danach nicht neu, ansonsten müssen Sie erst wieder einen neuen Code anfordern)!';

// UTF-8 codierte mail versenden
mb_send_mail( $postdata['email'], $subject, $message );

// Code und ID von User der diesen angefordert hat in Datenbank speichern
saveCode( $db, $code, $result['id'] );

$render = $app['twig']->render( 'reset_form.twig', array(
    'message' => 'Sie erhalten in Kürze eine E-Mail mit dem Authentifizierungscode.'
        ) );

return new Response( $render, 201 );
