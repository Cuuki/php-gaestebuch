<?php

use Symfony\Component\HttpFoundation\Response;

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'user_update_form.twig', array(
    'label_for' => 'username',
    'label_text' => 'Neuer Benutzername:',
    'loggeduser' => $loggeduser,
    'id' => $id,
    'input_name' => 'username'
        ) );

return new Response( $render, 201 );
