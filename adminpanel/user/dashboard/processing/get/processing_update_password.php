<?php

use Symfony\Component\HttpFoundation\Response;

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'user_update_form.twig', array(
    'label_for' => 'password',
    'label_text' => 'Neues Passwort',
    'loggeduser' => $loggeduser,
    'id' => $id,
    'input_name' => 'password'
        ) );

return new Response( $render, 201 );
