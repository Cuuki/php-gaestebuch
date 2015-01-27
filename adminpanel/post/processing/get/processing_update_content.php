<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_update_form.twig', array(
    'label_for' => 'content',
    'label_text' => 'Neuer Beitrag',
    'id' => $id,
    'is_active_postmanagement' => true,
    'post' => true,
    'input_name' => 'content'
        ) );

return new Response( $render, 201 );
