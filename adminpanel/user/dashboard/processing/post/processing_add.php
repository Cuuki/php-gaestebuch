<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'username' => $username->get( 'username' ),
    'useremail' => $useremail->get( 'useremail' ),
    'password' => $password->get( 'password' )
);

$postdata = sanitizeLogindata( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
    return new Response( implode( '<br>', $errorMessages ) .
            '<br>' . '<a href="' . $app['url_generator']->generate( 'add' ) . '">Zurück</a>', 404 );
}
else
{
    $subject = 'Neu angelegter Benutzer';
    $emailmessage = 'Hallo ' . $postdata['username'] . ',' . PHP_EOL . 'Sie wurden von ' . $app['session']->get( 'user' ) . ' als neuer Benutzer für das Adminpanel hinzugefügt. Sie können sich nun mit folgendem Passwort anmelden: ' . $postdata['password'] . ' (Sie können das Passwort jederzeit auf Ihrem Profil ändern).' . PHP_EOL . 'Mit freundlichen Grüßen' . PHP_EOL . 'Ihr Service Team';

    if ( saveLogindata( $postdata, $db ) != 0 )
    {
        // Mail an angegebene E-Mail Adresse mit Logindaten
        mb_send_mail( $postdata['useremail'], $subject, $emailmessage );

        return new Response( 'Der Benutzer wurde hinzugefügt. 
                        <a href="' . $app['url_generator']->generate( 'dashboard' ) . '">Zurück zur Übersicht</a>', 201 );
    }
    else
    {
        return new Response( 'Der Benutzer konnte nicht gepseichert werden! 
                        <a href="' . $app['url_generator']->generate( 'add' ) . '">Zurück</a>', 404 );
    }
}
