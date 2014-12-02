<?php

namespace Guestbook;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestbookControllerProvider implements ControllerProviderInterface
{
    private $data;

    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug-functions.php';
        include_once __DIR__ . '/../lib/dbconnect.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';

        $dboptions = array(
            "Hostname" => "localhost",
            "Username" => "root",
            "Password" => "XDrAgonStOrM129",
            "Databasename" => "gaestebuch"
        );

        $db = dbConnect( $dboptions );

        $db->query( "SET NAMES utf8" );

        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get( '/', function () use ( $app, $db )
        {
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/inc/processing_pagination.php';

            $posts = getPosts( $db, $rowsperpage, $currentpage );

            $render = $app['twig']->render( 'guestbook.twig', array(
                'posts' => $posts,
                'firstpage' => $firstPage,
                'currentpage' => $currentpage,
                'pagenumber' => $pageNumber,
                'nextpage' => $nextPage,
                'lastpage' => $lastPage
                    ) );

            // Header, Content (Posts) und Footer ausgeben
            return new Response( $render, 201 );
        } )->bind( 'guestbook' );

        $controllers->post( '/', function ( Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $gbFunctions )
        {
            include_once __DIR__ . '/../guestbook/inc/processing_add.php';
            include_once __DIR__ . '/../lib/pagination.php';
            include_once __DIR__ . '/../guestbook/inc/processing_pagination.php';

            return new Response( include_once __DIR__ . '/../guestbook/inc/main.php', 201 );
        } );

        return $controllers;
    }

}
