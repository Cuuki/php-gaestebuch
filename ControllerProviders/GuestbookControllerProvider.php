<?php

namespace Guestbook;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class GuestbookControllerProvider implements ControllerProviderInterface
{
    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';

        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get( '/', function ( Request $currentpage ) use ( $app )
        {
            return include_once __DIR__ . '/../guestbook/processing/get/processing_display.php';
        } )->bind( 'guestbook' );

        $controllers->post( '/', function ( Request $firstname, Request $lastname, Request $email, Request $textinput, Request $currentpage ) use ( $app, $gbFunctions )
        {
            return include_once __DIR__ . '/../guestbook/processing/post/processing_display.php';
        } );

        return $controllers;
    }

}
