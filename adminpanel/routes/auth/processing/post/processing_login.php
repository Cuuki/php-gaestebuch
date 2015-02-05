<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'username' => $username->get( 'username' ),
    'password' => $password->get( 'password' ),
    'staylogged' => $staylogged->get( 'staylogged' )
);

// Daten aus Datenbank holen
$logindata = getLogindata( $app['db'], $postdata['username'] );
$hash = $logindata['password'];

// mit Eingabe vergleichen, Authentifizierung
if ( password_verify( $postdata['password'], $hash ) )
{
    // Wenn 'angemeldet bleiben' ausgewählt lifetime auf 0 setzen und nicht automatisch ausloggen
    if ( $postdata['staylogged'] == 'staylogged' )
    {
        $app['session']->set( 'cookie_lifetime', 0 );
    }
    else
    {
        // Wenn nicht Cookie Lifetime auf einen anderen wert als 0 setzen
        $app['session']->set( 'cookie_lifetime', 900 );
    }
    // Sessionnamen auf Usernamen setzen
    $app['session']->set( 'user', $postdata['username'] );
    // Loginzeit setzen
    $app['session']->set( 'time', time() );

    // Weiterleiten auf Dashboard
    return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
}
else
{
    return new Response( $app['twig']->render( 'login_form.twig', array(
                'message' => 'Login fehlgeschlagen. Ihre Daten sind nicht korrekt.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
