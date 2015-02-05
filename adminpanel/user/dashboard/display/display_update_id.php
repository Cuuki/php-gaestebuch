<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

// Wenn die Benutzerrolle 'Editor' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $users['role'] == 'Editor' )
{
    return new Response( $app['twig']->render( 'user_update_id.twig', array(
                'headline' => 'Alles bearbeiten',
                'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.',
                'is_active_usermanagement' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}

return new Response( $app['twig']->render( 'user_update_id.twig', array(
            'user' => getUser( $app['db'], $id ),
            'is_active_usermanagement' => true,
            'headline' => 'Alles bearbeiten',
            'submitvalue' => 'Ändern'
        ) ), 201 );
