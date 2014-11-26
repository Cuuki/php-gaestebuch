<?php

namespace Guestbook;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestbookControllerProvider implements ControllerProviderInterface
{
    public $data;

    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug-functions.php';
        include_once __DIR__ . '/../lib/dbconnect.php';
        include_once __DIR__ . '/../lib/dbconfig.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';
        
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get( '/', function () use ( $db )
        {
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/inc/processing_pagination.php';

            // Header, Content (Posts) und Footer ausgeben
            return new Response( include_once __DIR__ . '/../guestbook/inc/main.php', 201 );
        } );

        $controllers->post( '/', function ( Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $gbFunctions )
        {
            $postdata = array(
                'firstname' => $firstname->get( 'firstname' ),
                'lastname' => $lastname->get( 'lastname' ),
                'email' => $email->get( 'email' ),
                'textinput' => $textinput->get( 'textinput' )
            );

            $data = $this->data = array(
                'firstname' => '',
                'lastname' => '',
                'email' => '',
                'textinput' => ''
            );

            include_once __DIR__ . '/../guestbook/inc/processing_add.php';
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/inc/processing_pagination.php';

            return new Response( include_once __DIR__ . '/../guestbook/inc/main.php', 201 );
        } );

        return $controllers;
    }

}
