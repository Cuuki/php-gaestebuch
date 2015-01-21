<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_update_form.twig', array(
    'label_for' => 'username',
    'label_text' => 'Neuer Benutzername',
    'id' => $id,
    'is_active_usermanagement' => true,
    'input_name' => 'username'
        ) );

return new Response( $render, 201 );
