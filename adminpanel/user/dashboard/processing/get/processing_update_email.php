<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_update_form.twig', array(
    'label_for' => 'useremail',
    'label_text' => 'Neue E-Mail Adresse',
    'id' => $id,
    'is_active_usermanagement' => true,
    'input_name' => 'useremail'
        ) );

return new Response( $render, 201 );
