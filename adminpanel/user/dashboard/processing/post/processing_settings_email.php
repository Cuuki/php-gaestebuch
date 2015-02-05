<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'oldemail' => $email->get( 'oldemail' ),
    'useremail' => $email->get( 'email' )
);

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );
$allUsers = getAllUsers( $app['db'] );

foreach ( $allUsers as $user )
{
    $useremails[] = $user['useremail'];
}

// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
if ( $postdata['oldemail'] != $users['useremail'] )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldemail',
                'oldinput_text' => 'Alte E-Mail Adresse',
                'oldinput_name' => 'oldemail',
                'newinput_for' => 'email',
                'newinput_text' => 'Neue E-Mail Adresse',
                'newinput_name' => 'email',
                'message' => 'Die alte E-Mail Adresse stimmt nicht mit Ihrer überein.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( $postdata['oldemail'] == $postdata['useremail'] )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldemail',
                'oldinput_text' => 'Alte E-Mail Adresse',
                'oldinput_name' => 'oldemail',
                'newinput_for' => 'email',
                'newinput_text' => 'Neue E-Mail Adresse',
                'newinput_name' => 'email',
                'message' => 'Die alte darf nicht mit der neuen Adresse übereinstimmen!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldemail',
                'oldinput_text' => 'Alte E-Mail Adresse',
                'oldinput_name' => 'oldemail',
                'newinput_for' => 'email',
                'newinput_text' => 'Neue E-Mail Adresse',
                'newinput_name' => 'email',
                'errormessages' => getErrorMessages( $invalidInput ),
                'message' => 'Sie haben keine valide E-Mail Adresse angegeben!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( in_array( $postdata['useremail'], $useremails, true ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername',
                'newinput_name' => 'username',
                'message' => 'Es existiert bereits ein Benutzer mit dieser E-Mail Adresse!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
else
{
    if ( updateEmail( $app['db'], $postdata['useremail'], $users['id'] ) )
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldemail',
                    'oldinput_text' => 'Alte E-Mail Adresse',
                    'oldinput_name' => 'oldemail',
                    'newinput_for' => 'email',
                    'newinput_text' => 'Neue E-Mail Adresse',
                    'newinput_name' => 'email',
                    'message' => 'Die E-Mail Adresse wurde geändert!',
                    'relog' => true,
                    'message_type' => 'alert alert-dismissable alert-success'
                ) ), 201 );
    }
    else
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldemail',
                    'oldinput_text' => 'Alte E-Mail Adresse',
                    'oldinput_name' => 'oldemail',
                    'newinput_for' => 'email',
                    'newinput_text' => 'Neue E-Mail Adresse',
                    'newinput_name' => 'email',
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger'
                ) ), 404 );
    }
}