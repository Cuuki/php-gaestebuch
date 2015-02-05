<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'role' => $role->get( 'role' )
);

if ( updateRole( $app['db'], $postdata['role'], $id ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'message' => 'Die Rolle wurde geändert.',
                'message_type' => 'alert alert-dismissable alert-success',
                'label_for' => 'role',
                'label_text' => 'Neue Rolle',
                'id' => $id,
                'is_active_usermanagement' => true,
                'input_name' => 'role'
            ) ), 201 );
}
// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
else
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'message' => 'Die Rolle konnte nicht geändert werden!',
                'message_type' => 'alert alert-dismissable alert-danger',
                'label_for' => 'role',
                'label_text' => 'Neue Rolle',
                'id' => $id,
                'is_active_usermanagement' => true,
                'input_name' => 'role'
            ) ), 404 );
}
