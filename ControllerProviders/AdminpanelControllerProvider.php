<?php

namespace Adminpanel;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

define( 'ROUTES_DIR', realpath( __DIR__ . '/../adminpanel/routes' ) );
define( 'USER_DIR', realpath( __DIR__ . '/../adminpanel/user' ) );
define( 'POST_DIR', realpath( __DIR__ . '/../adminpanel/post' ) );

class AdminpanelControllerProvider implements ControllerProviderInterface
{

    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug-functions.php';
        include_once __DIR__ . '/../lib/dbconnect.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';
        $apFunctions = include_once __DIR__ . '/../lib/ap-functions.php';

        $dboptions = array(
            "Hostname" => "localhost",
            "Username" => "root",
            "Password" => "XDrAgonStOrM129",
            "Databasename" => "gaestebuch"
        );

        $db = dbConnect( $dboptions );

        $db->query( "SET NAMES utf8" );

        include_once __DIR__ . '/../adminpanel/sudo-config.php';

        $controllers = $app['controllers_factory'];

        $controllers->before( function () use ( $app )
        {
            $getPath = $app['request_context']->getPathInfo();

            //Wenn Session null dann true und führe aus, wenn Session nicht null dann false und führe nicht aus
            if ( !$app['session']->get( 'user' ) )
            {
                // Wenn Pfad = auth/login dann nicht redirecten
                if ( $getPath == '/ap/auth/login' || $getPath == '/ap/auth/reset' || $getPath == '/ap/auth/reset/code' )
                {
                    return;
                }
                return $app->redirect( $app['url_generator']->generate( 'login' ) );
            }
        } );

        $controllers->get( '/', function () use ( $app )
        {
            return $app->redirect( 'user/dashboard/' );
        } );

        $controllers->get( 'post/', function () use ( $app )
        {
            return $app->redirect( '../user/dashboard/' );
        } );

        $controllers->get( 'user/', function () use ( $app )
        {
            return $app->redirect( 'dashboard/' );
        } );

        $controllers->get( 'auth/', function () use ( $app )
        {
            return $app->redirect( 'login' );
        } );

        $controllers->get( 'auth/login', function () use ( $app )
        {
            // Wenn bereits eingeloggt weiterleiten auf Dashboard
            if ( ($app['session']->get( 'user' )) != NULL )
            {
                return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
            }

            $render = $app['twig']->render( 'login_form.html' );

            return $render;
        } )->bind( 'login' );

        $controllers->post( 'auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $db, $apFunctions )
        {
            $processing = include_once ROUTES_DIR . '/auth/processing/processing_login.php';

            return $processing;
        } );

        $controllers->get( 'auth/reset', function () use ( $app )
        {
            // Wenn bereits eingeloggt weiterleiten auf Dashboard
            if ( ($app['session']->get( 'user' )) != NULL )
            {
                return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
            }

            $render = $app['twig']->render( 'reset_form.html' );

            return $render;
        } );

        $controllers->post( 'auth/reset', function ( Request $email ) use ( $db )
        {
            $processing = include_once ROUTES_DIR . '/auth/processing/processing_reset.php';

            return $processing;
        } );

        $controllers->get( 'auth/reset/code', function () use ( $app )
        {
            // Wenn bereits eingeloggt weiterleiten auf Dashboard
            if ( ($app['session']->get( 'user' )) != NULL )
            {
                return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
            }

            $render = $app['twig']->render( 'code_form.html' );

            return $render;
        } );

        $controllers->post( 'auth/reset/code', function ( Request $code, Request $password ) use ( $db, $app )
        {
            $processing = include_once ROUTES_DIR . '/auth/processing/processing_code.php';

            return $processing;
        } );

        // User Einstellungen
        $controllers->get( 'user/dashboard/settings', function () use ( $app )
        {
            $render = $app['twig']->render( 'settings_form.html' );

            return $render;
        } )->bind( 'settings' );

        // Userzeile als Rückmeldung das er eingeloggt ist
        $userHeader = '<header><h3>Sie sind als  eingeloggt.</h3></header>';
        //<a href="/php-gaestebuch/adminpanel' . $app['url_generator']->generate( 'settings' ) . '">' . $app['session']->get( 'user' ) . '</a>

        $controllers->get( 'user/dashboard/', function () use ( $app, $userHeader )
        {
            $loggedInSince = $app['session']->get( 'time' );

            $render = $app['twig']->render( 'dashboard_form.html' );

            return new Response( $userHeader . '<p>Sie sind eingeloggt seid: ' . date( 'h:i:sa, d.m.Y', $loggedInSince ) . '</p>' . $render, 201 );
        } )->bind( 'dashboard' );

        $controllers->get( 'user/dashboard/settings/username', function () use ( $app )
        {
            $render = $app['twig']->render( 'settings_update_form.html', array(
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername:',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername:',
                'newinput_name' => 'username'
                    ) );

            return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
        } )->bind( 'changeUsername' );

        $controllers->post( 'user/dashboard/settings/username', function ( Request $username ) use ( $db, $app, $apFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_settings_username.php';

            return $processing;
        } );

        $controllers->get( 'user/dashboard/settings/password', function () use ( $app )
        {
            $render = $app['twig']->render( 'settings_update_form.html', array(
                'oldinput_for' => 'oldpassword',
                'oldinput_text' => 'Altes Passwort:',
                'oldinput_name' => 'oldpassword',
                'newinput_for' => 'password',
                'newinput_text' => 'Neues Passwort:',
                'newinput_name' => 'password'
                    ) );

            return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
        } )->bind( 'changePassword' );

        $controllers->post( 'user/dashboard/settings/password', function ( Request $password ) use ( $db, $app, $apFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_settings_password.php';

            return $processing;
        } );

        $controllers->get( 'user/dashboard/settings/email', function () use ( $app )
        {
            $render = $app['twig']->render( 'settings_update_form.html', array(
                'oldinput_for' => 'oldemail',
                'oldinput_text' => 'Alte E-Mail Adresse:',
                'oldinput_name' => 'oldemail',
                'newinput_for' => 'email',
                'newinput_text' => 'Neue E-Mail Adresse:',
                'newinput_name' => 'email'
                    ) );

            return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
        } )->bind( 'changeEmail' );

        $controllers->post( 'user/dashboard/settings/email', function ( Request $email ) use ( $db, $app, $apFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_settings_email.php';

            return $processing;
        } );

        // Benutzer hinzufügen
        $controllers->get( 'user/dashboard/add', function () use ( $app, $userHeader )
        {
            $render = $app['twig']->render( 'user_form.html', array(
                'headline' => 'Benutzer hinzufügen:',
                'submitvalue' => 'Anlegen',
                'link_back' => '../'
                    ) );

            return new Response( $userHeader . $render, 201 );
        } )->bind( 'add' );

        $controllers->post( 'user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_add.php';

            return $processing;
        } );

        // Benutzerdaten bearbeiten
        $controllers->get( 'user/dashboard/update/', function () use ( $app, $db, $apFunctions, $userHeader )
        {
            $processing = include_once USER_DIR . '/dashboard/display/display_update.php';

            return $processing;
        } )->bind( 'update' );

        $controllers->get( 'user/dashboard/update/{id}', function ( $id ) use ( $app, $db, $apFunctions, $gbFunctions, $userHeader )
        {
            include_once USER_DIR . '/dashboard/display/display_update_id.php';

            $render = $app['twig']->render( 'user_form.html', array(
                'headline' => 'Alle Benutzerdaten ändern:',
                'submitvalue' => 'Ändern',
                'link_back' => '../update'
                    ) );

            return new Response( $userHeader . $displayUser . $render, 201 );
        } );

        $controllers->post( 'user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_update_id.php';

            return $processing;
        } );

        $controllers->get( 'user/dashboard/update/{id}/username', function ( $id ) use ( $app, $userHeader )
        {
            $render = $app['twig']->render( 'user_update_form.html', array(
                'label_for' => 'username',
                'label_text' => 'Neuer Benutzername:',
                'input_name' => 'username'
                    ) );

            return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
        } );

        $controllers->post( 'user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_update_username.php';

            return $processing;
        } );

        $controllers->get( 'user/dashboard/update/{id}/email', function ( $id ) use ( $app, $userHeader )
        {
            $render = $app['twig']->render( 'user_update_form.html', array(
                'label_for' => 'useremail',
                'label_text' => 'Neue E-Mail Adresse:',
                'input_name' => 'useremail'
                    ) );

            return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
        } );

        $controllers->post( 'user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_update_email.php';

            return $processing;
        } );

        $controllers->get( 'user/dashboard/update/{id}/password', function ( $id ) use ( $app, $userHeader )
        {
            $render = $app['twig']->render( 'user_update_form.html', array(
                'label_for' => 'password',
                'label_text' => 'Neues Passwort:',
                'input_name' => 'password'
                    ) );

            return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
        } );

        $controllers->post( 'user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_update_password.php';

            return $processing;
        } );

        // Benutzer löschen
        $controllers->get( 'user/dashboard/delete/', function () use ( $app, $db, $apFunctions, $userHeader )
        {
            $processing = include_once USER_DIR . '/dashboard/display/display_delete.php';

            return $processing;
        } )->bind( 'delete' );

        $controllers->get( 'user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
        {
            $processing = include_once USER_DIR . '/dashboard/processing/processing_delete.php';

            return $processing;
        } );

        // Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
        $controllers->get( 'post/add', function () use ( $app )
        {
            return $app->redirect( '../../gb/#add' );
        } );

        $controllers->get( 'post/update', function () use ( $db, $app, $userHeader )
        {
            include_once POST_DIR . '/display/display_update.php';

            include_once POST_DIR . '/update.php';

            return new Response( $userHeader . displayPagination( $currentpage, $totalpages ) . displayUpdateEntries( $posts ) .
                    '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
        } )->bind( 'postUpdate' );

        $controllers->get( 'post/update/{id}', function ( $id ) use ( $app, $db, $gbFunctions, $userHeader )
        {
            include_once POST_DIR . '/update.php';

            $entryData = getEntry( $db, $id );

            $render = $app['twig']->render( 'post_form.html' );

            return new Response( $userHeader . displayPosts( $entryData ) . $render, 201 );
        } );

        $controllers->post( 'post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $app, $gbFunctions, $userHeader )
        {
            $processing = include_once POST_DIR . '/processing/processing_update.php';

            return $processing;
        } );

        $controllers->get( 'post/delete', function () use ( $db, $userHeader )
        {
            include_once POST_DIR . '/display/display_delete.php';

            include_once POST_DIR . '/delete.php';

            return new Response( $userHeader . displayPagination( $currentpage, $totalpages ) . displayDeleteEntries( $posts ) .
                    '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
        } )->bind( 'deletePost' );

        $controllers->get( 'post/delete/{id}', function ( $id ) use ( $db, $app )
        {
            $processing = include_once POST_DIR . '/processing/processing_delete.php';

            return $processing;
        } );

        // Ausloggen
        $controllers->get( 'user/dashboard/logout', function () use ( $app )
        {
            session_destroy();

            // nach ausloggen weiterleiten auf loginseite
            return $app->redirect( $app['url_generator']->generate( 'login' ) );
        } )->bind( 'logout' );

        return $controllers;
    }

}
