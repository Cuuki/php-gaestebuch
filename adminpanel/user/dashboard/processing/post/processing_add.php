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
    return new Response( $app['twig']->render( 'user_add.twig', array(
                'errormessages' => getErrorMessages( $invalidInput ),
                'postdata' => $postdata,
                'is_active_usermanagement' => true,
                'headline' => 'Benutzer hinzufügen',
                'submitvalue' => 'Anlegen'
            ) ), 404 );
}
else
{
    foreach ( getAllUsers( $app['db'] ) as $user )
    {
        $usernames[] = $user['username'];
        $useremails[] = $user['useremail'];
    }

    if ( in_array( $postdata['username'], $usernames, true ) || in_array( $postdata['useremail'], $useremails, true ) )
    {
        return new Response( $app['twig']->render( 'user_add.twig', array(
                    'message' => 'Der Benutzer existiert bereits.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'is_active_usermanagement' => true,
                    'headline' => 'Benutzer hinzufügen',
                    'submitvalue' => 'Anlegen'
                ) ), 404 );
    }
    elseif ( saveLogindata( $postdata, $app['db'] ) )
    {
        // $subject = 'Neu angelegter Benutzer';
        // $emailmessage = 'Hallo ' . $postdata['username'] . ',' . PHP_EOL . 'Sie wurden von ' . $app['session']->get( 'user' ) .
        // ' als neuer Benutzer für das Adminpanel hinzugefügt. Sie können sich nun mit folgendem Passwort anmelden: ' . $postdata['password'] .
        // ' (Sie können das Passwort jederzeit auf Ihrem Profil ändern).' . PHP_EOL . 'Mit freundlichen Grüßen' . PHP_EOL . 'Ihr Service Team';                        
        // Mail an angegebene E-Mail Adresse mit Logindaten
        // mb_send_mail( $postdata['useremail'], $subject, $emailmessage );

        return new Response( $app['twig']->render( 'user_add.twig', array(
                    'message' => 'Der Benutzer wurde hinzugefügt.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'is_active_usermanagement' => true,
                    'headline' => 'Benutzer hinzufügen',
                    'submitvalue' => 'Anlegen'
                ) ), 201 );
    }
    else
    {
        return new Response( $app['twig']->render( 'user_add.twig', array(
                    'message' => 'Der Benutzer konnte nicht gespeichert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'is_active_usermanagement' => true,
                    'headline' => 'Benutzer hinzufügen',
                    'submitvalue' => 'Anlegen'
                ) ), 404 );
    }
}
