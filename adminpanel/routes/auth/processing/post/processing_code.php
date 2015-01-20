<?php

use Symfony\Component\HttpFoundation\Response;

/**
 * @return array
 * */
function getCode ( $db, $code )
{
    // Code und ID von User der den Code angefordert hat aus DB auslesen
    $select = 'SELECT * FROM auth_codes WHERE code = ?';

    return $db->fetchAssoc( $select, array( $code ) );
}

/**
 * @return boolean
 */
function deleteCode ( $db, $code )
{
    return $db->delete( 'auth_codes', array( 'code' => $code ) );
}

$postdata = array(
    'code' => $code->get( 'code' ),
    'password' => $password->get( 'password' )
);

$result = getCode( $app['db'], $postdata['code'] );

// Abfrage ob Code mit einem aus DB übereinstimmt
if ( $result['code'] == NULL )
{
    $render = $app['twig']->render( 'code_form.twig', array(
        'message' => 'Sie haben den falschen Code eingegegeben.',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
else
{
    // Altes Passwort mit dem neuen überschreiben wenn Code stimmt
    include_once USER_DIR . '/dashboard/update.php';
    updatePassword( $app['db'], $postdata['password'], $result['id_user'] );

    // Wenn Code eingegeben wurde lösche ihn aus DB
    deleteCode( $app['db'], $result['code'] );

    $render = $app['twig']->render( 'code_form.twig', array(
        'message' => 'Ihr Passwort wurde geändert!',
        'message_type' => 'successmessage'
            ) );

    return new Response( $render, 201 );
}