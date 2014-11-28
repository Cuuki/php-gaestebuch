<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/delete.php';

if ( deletePost( $db, $id ) )
{
    return new Response( 'Beitrag erfolgreich gelöscht!
                <a href="' . $app['url_generator']->generate( 'deletePost' ) . '">Zurück</a>', 201 );
}
else
{
    return new Response( 'Der Beitrag konnte nicht gelöscht werden, versuchen sie es erneut!
                                <a href="' . $app['url_generator']->generate( 'deletePost' ) . '">Zurück</a>', 404 );
}