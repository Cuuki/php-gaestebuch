<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

return new Response( $app['twig']->render( 'post_update_id.twig', array(
            'headline' => 'Daten ändern',
            'submit_text' => 'Ändern',
            'is_active_postmanagement' => true,
            'post' => getEntry( $app['db'], $id )
        ) ), 201 );
