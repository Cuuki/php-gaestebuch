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

        //Dashboard 
        $controllers->get( 'user/dashboard/', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_dashboard.php';
        } )->bind( 'dashboard' );

        $this->bindAuth( $app, $controllers, $db, $apFunctions );
        $this->bindReset( $app, $controllers, $db );
        $this->bindSettings( $app, $controllers, $db, $apFunctions );
        $this->bindUser( $app, $controllers, $db, $apFunctions, $gbFunctions );
        $this->bindPosts( $app, $controllers, $db, $gbFunctions );

        return $controllers;
    }

    private function bindAuth ( Application $app, $controllers, $db, $apFunctions )
    {
        //Login        
        $controllers->get( 'auth/login', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_login.php';
        } )->bind( 'login' );

        $controllers->post( 'auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $db, $apFunctions )
        {
            return include_once ROUTES_DIR . '/auth/processing/post/processing_login.php';
        } );

        // Logout
        $controllers->get( 'user/dashboard/logout', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_logout.php';
        } )->bind( 'logout' );

        return $controllers;
    }

    private function bindReset ( Application $app, $controllers, $db )
    {
        //Passwort zurücksetzen        
        $controllers->get( 'auth/reset', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_reset.php';
        } );

        $controllers->post( 'auth/reset', function ( Request $email ) use ( $db )
        {
            return include_once ROUTES_DIR . '/auth/processing/post/processing_reset.php';
        } );

        $controllers->get( 'auth/reset/code', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_code.php';
        } );

        $controllers->post( 'auth/reset/code', function ( Request $code, Request $password ) use ( $db, $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/post/processing_code.php';
        } );

        return $controllers;
    }

    private function bindSettings ( Application $app, $controllers, $db, $apFunctions )
    {
        // User Einstellungen
        $controllers->get( 'user/dashboard/settings', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings.php';
        } )->bind( 'settings' );

        $controllers->get( 'user/dashboard/settings/username', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings_username.php';
        } )->bind( 'changeUsername' );

        $controllers->post( 'user/dashboard/settings/username', function ( Request $username ) use ( $db, $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_username.php';
        } );

        $controllers->get( 'user/dashboard/settings/password', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings_password.php';
        } )->bind( 'changePassword' );

        $controllers->post( 'user/dashboard/settings/password', function ( Request $password ) use ( $db, $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_password.php';
        } );

        $controllers->get( 'user/dashboard/settings/email', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings_email.php';
        } )->bind( 'changeEmail' );

        $controllers->post( 'user/dashboard/settings/email', function ( Request $email ) use ( $db, $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_email.php';
        } );

        return $controllers;
    }

    private function bindUser ( Application $app, $controllers, $db, $apFunctions, $gbFunctions )
    {
        // Benutzer hinzufügen
        $controllers->get( 'user/dashboard/add', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_add.php';
        } )->bind( 'add' );

        $controllers->post( 'user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_add.php';
        } );

        // Benutzerdaten bearbeiten
        $controllers->get( 'user/dashboard/update/', function () use ( $app, $db, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_update.php';
        } )->bind( 'update' );

        $controllers->get( 'user/dashboard/update/{id}', function ( $id ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_update_id.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_id.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/username', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_username.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_username.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/email', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_email.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_email.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/password', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_password.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_password.php';
        } );

        // Benutzer löschen
        $controllers->get( 'user/dashboard/delete/', function () use ( $app, $db, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_delete.php';
        } )->bind( 'delete' );

        $controllers->get( 'user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_delete.php';
        } );

        return $controllers;
    }

    private function bindPosts ( Application $app, $controllers, $db, $gbFunctions )
    {
        // Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
        $controllers->get( 'post/add', function () use ( $app )
        {
            return $app->redirect( '../../gb/#add' );
        } );

        //Beitrag bearbeiten
        $controllers->get( 'post/update', function () use ( $db, $app )
        {
            return include_once POST_DIR . '/display/display_update.php';
            
        } )->bind( 'postUpdate' );

        $controllers->get( 'post/update/{id}', function ( $id ) use ( $app, $db, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/get/processing_update_id.php';            
        } );

        $controllers->post( 'post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_id.php';
        } );

        //Beitrag löschen        
        $controllers->get( 'post/delete', function () use ( $db )
        {
            return include_once POST_DIR . '/display/display_delete.php';
        } )->bind( 'deletePost' );

        $controllers->get( 'post/delete/{id}', function ( $id ) use ( $db, $app )
        {
            return include_once POST_DIR . '/processing/get/processing_delete_id.php';
        } );

        return $controllers;
    }

}
