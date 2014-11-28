<?php

use Symfony\Component\HttpFoundation\Response;

/**
* @return array
**/
function getCode ( $db, $code )
{
	// Code und ID von User der den Code angefordert hat aus DB auslesen
	$sql = 'SELECT
                code, id_user
            FROM
                auth_codes
            WHERE
            	code = "'. $code .'"';

    $dbRead = $db->query( $sql );

    $row = $dbRead->fetch_assoc();

    return $row;
}

/**
 * @return boolean
 */
function deleteCode ( $db, $code )
{
	$delete = 'DELETE FROM auth_codes WHERE code = "'. $code .'"';

    return $db->query( $delete );
}

$postdata = array(
        'code' => $code->get('code'),
        'password' => $password->get('password')
);

$result = getCode( $db, $postdata['code'] );

// Abfrage ob Code mit einem aus DB übereinstimmt
if( $result['code'] == NULL )
{
        return new Response ( 'Sie haben den falschen Code eingegegeben.
                <br><a href="../reset/code">Zurück zur Eingabe</a>', 404 );
}
else
{
        // Altes Passwort mit dem neuen überschreiben wenn Code stimmt
        include_once USER_DIR . '/dashboard/update.php';
        updatePassword( $db, $postdata['password'], $result['id_user'] );

        // Wenn Code eingegeben wurde lösche ihn aus DB
        deleteCode( $db, $result['code'] );

        return new Response ( 'Ihr Passwort wurde geändert! <a href="' 
                . $app['url_generator']->generate('login') . '">Zurück zum Login</a>', 201 );
}