<?php

namespace Guestbook;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class GuestbookControllerProvider implements ControllerProviderInterface
{
    private $data;

    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';

        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get( '/', function () use ( $app )
        {
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/processing/get/processing_pagination.php';
            return include_once __DIR__ . '/../guestbook/processing/get/processing_display.php';
        } )->bind( 'guestbook' );

        $controllers->post( '/', function ( Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $app, $gbFunctions )
        {
            include_once __DIR__ . '/../guestbook/processing/post/processing_add.php';
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/processing/get/processing_pagination.php';

            return include_once __DIR__ . '/../guestbook/processing/post/processing_display.php';
        } );

        return $controllers;
    }

}
