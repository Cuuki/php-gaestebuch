<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'content',
            'label_text' => 'Neuer Beitrag',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'content'
        ) ), 201 );
