<?php

use Symfony\Component\HttpFoundation\Response;

include_once ROUTES_DIR . '/auth/password_forget.php';

$postdata = array(
    'email' => $email->get( 'email' )
);

$result = getMail( $app['db'], $postdata['email'] );

// Abfrage ob E-Mail mit einer aus DB übereinstimmt
if ( $result['useremail'] == NULL )
{
    return new Response( $app['twig']->render( 'reset_form.twig', array(
                'message' => 'Es existiert kein Benutzer mit der angegebenen E-Mail Adresse.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}

$code = mt_rand( 1000000000, 9999999999 );

//$subject = 'Authentifizierungscode Passwort-Neuvergabe';
//$message = 'Bitte geben Sie folgenden Code: ' . $code . ' im Eingabefeld der Website ein.' . PHP_EOL . PHP_EOL . 'Mit freundlichen Grüßen' . PHP_EOL . 'Ihr Service-Team';

if ( saveCode( $app['db'], $code, $result['id'] ) )
{
    // UTF-8 codierte mail versenden
    //mb_send_mail( $postdata['email'], $subject, $message );

    return new Response( $app['twig']->render( 'reset_form.twig', array(
                'message' => 'Sie erhalten in Kürze eine E-Mail mit dem Authentifizierungscode.',
                'message_type' => 'alert alert-dismissable alert-success'
            ) ), 201 );
}

return new Response( $app['twig']->render( 'reset_form.twig', array(
            'message' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut!',
            'message_type' => 'alert alert-dismissable alert-danger'
        ) ), 404 );
