<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'username' => $username->get( 'username' ),
    'useremail' => $useremail->get( 'useremail' ),
    'password' => $password->get( 'password' )
);

$postdata = $this->sanitizeLogindata( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );

    $render = $app['twig']->render( 'user_add.twig', array(
        'errormessages' => $errorMessages,
        'postdata' => $postdata,
        'headline' => 'Benutzer hinzufügen:',
        'submitvalue' => 'Anlegen'
            ) );

    return new Response( $render, 404 );
}
else
{
//    $subject = 'Neu angelegter Benutzer';
//    $emailmessage = 'Hallo ' . $postdata['username'] . ',' . PHP_EOL . 'Sie wurden von ' . $app['session']->get( 'user' ) .
//            ' als neuer Benutzer für das Adminpanel hinzugefügt. Sie können sich nun mit folgendem Passwort anmelden: ' . $postdata['password'] .
//            ' (Sie können das Passwort jederzeit auf Ihrem Profil ändern).' . PHP_EOL . 'Mit freundlichen Grüßen' . PHP_EOL . 'Ihr Service Team';
    $userData = getAllUsers( $app['db'] );
    
    foreach ( $userData as $user )
    {
        $username = $user['username'];
        $useremail = $user['useremail'];
    }
    
    if ( $postdata['username'] == $username || $postdata['useremail'] == $useremail )
    {
        $render = $app['twig']->render( 'user_add.twig', array(
            'message' => 'Der Benutzer existiert bereits.',
            'headline' => 'Benutzer hinzufügen:',
            'submitvalue' => 'Anlegen'
                ) );

        return new Response( $render, 404 );
    }
    elseif ( saveLogindata( $postdata, $app['db'] ) )
    {
        // Mail an angegebene E-Mail Adresse mit Logindaten
//        mb_send_mail( $postdata['useremail'], $subject, $emailmessage );

        $render = $app['twig']->render( 'user_add.twig', array(
            'message' => 'Der Benutzer wurde hinzugefügt.',
            'headline' => 'Benutzer hinzufügen:',
            'submitvalue' => 'Anlegen'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'user_add.twig', array(
            'message' => 'Der Benutzer konnte nicht gepseichert werden!',
            'headline' => 'Benutzer hinzufügen:',
            'submitvalue' => 'Anlegen'
                ) );

        return new Response( $render, 404 );
    }
}
