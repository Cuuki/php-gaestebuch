<?php

namespace Adminpanel;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

define( 'ROUTES_DIR', realpath( __DIR__ . '/../adminpanel/routes' ) );
define( 'USER_DIR', realpath( __DIR__ . '/../adminpanel/user' ) );
define( 'POST_DIR', realpath( __DIR__ . '/../adminpanel/post' ) );

class AdminpanelControllerProvider implements ControllerProviderInterface
{

    public function connect ( Application $app )
    {
        // Dateien einbinden
        include_once __DIR__ . '/../lib/debug.php';
        $gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';
        $apFunctions = include_once __DIR__ . '/../lib/ap-functions.php';
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

        $this->bindAuth( $app, $controllers, $apFunctions );
        $this->bindReset( $app, $controllers );
        $this->bindSettings( $app, $controllers, $apFunctions );
        $this->bindUser( $app, $controllers, $apFunctions, $gbFunctions );
        $this->bindPosts( $app, $controllers, $gbFunctions );

        return $controllers;
    }

    private function bindAuth ( Application $app, $controllers, $apFunctions )
    {
        //Login        
        $controllers->get( 'auth/login', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_login.php';
        } )->bind( 'login' );

        $controllers->post( 'auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $apFunctions )
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

    private function bindReset ( Application $app, $controllers )
    {
        //Passwort zurücksetzen        
        $controllers->get( 'auth/reset', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_reset.php';
        } )->bind('reset');

        $controllers->post( 'auth/reset', function ( Request $email ) use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/post/processing_reset.php';
        } );

        $controllers->get( 'auth/reset/code', function () use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/get/processing_code.php';
        } )->bind('resetCode');

        $controllers->post( 'auth/reset/code', function ( Request $code, Request $password ) use ( $app )
        {
            return include_once ROUTES_DIR . '/auth/processing/post/processing_code.php';
        } );

        return $controllers;
    }

    private function bindSettings ( Application $app, $controllers, $apFunctions )
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

        $controllers->post( 'user/dashboard/settings/username', function ( Request $username ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_username.php';
        } );

        $controllers->get( 'user/dashboard/settings/password', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings_password.php';
        } )->bind( 'changePassword' );

        $controllers->post( 'user/dashboard/settings/password', function ( Request $password ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_password.php';
        } );

        $controllers->get( 'user/dashboard/settings/email', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_settings_email.php';
        } )->bind( 'changeEmail' );

        $controllers->post( 'user/dashboard/settings/email', function ( Request $email ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_settings_email.php';
        } );

        return $controllers;
    }

    private function bindUser ( Application $app, $controllers, $apFunctions, $gbFunctions )
    {
        // Benutzer hinzufügen
        $controllers->get( 'user/dashboard/add', function () use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_add.php';
        } )->bind( 'add' );

        $controllers->post( 'user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_add.php';
        } );

        // Benutzerdaten bearbeiten
        $controllers->get( 'user/dashboard/update/', function ( Request $currentpage ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_update.php';
        } )->bind( 'update' );

        $controllers->get( 'user/dashboard/update/{id}', function ( $id ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_update_id.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_id.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/username', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_username.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_username.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/email', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_email.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_email.php';
        } );

        $controllers->get( 'user/dashboard/update/{id}/password', function ( $id ) use ( $app )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_update_password.php';
        } );

        $controllers->post( 'user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $apFunctions, $gbFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/post/processing_update_password.php';
        } );

        // Benutzer löschen
        $controllers->get( 'user/dashboard/delete/', function ( Request $currentpage ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/display/display_delete.php';
        } )->bind( 'delete' );

        $controllers->get( 'user/dashboard/delete/{id}', function( $id ) use ( $app, $apFunctions )
        {
            return include_once USER_DIR . '/dashboard/processing/get/processing_delete.php';
        } );

        return $controllers;
    }

    private function bindPosts ( Application $app, $controllers, $gbFunctions )
    {
        // Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
        $controllers->get( 'post/add', function () use ( $app )
        {
            return $app->redirect( '../../gb/#add' );
        } )->bind( 'postAdd' );

        //Beitrag bearbeiten
        $controllers->get( 'post/update', function ( Request $currentpage ) use ( $app )
        {
            return include_once POST_DIR . '/display/display_update.php';
        } )->bind( 'postUpdate' );

        $controllers->get( 'post/update/{id}', function ( $id ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/get/processing_update_id.php';
        } );

        $controllers->post( 'post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_id.php';
        } );

        $controllers->get( 'post/update/{id}/firstname', function ( $id ) use ( $app )
        {
            return include_once POST_DIR . '/processing/get/processing_update_firstname.php';
        } );

        $controllers->post( 'post/update/{id}/firstname', function ( $id, Request $firstname ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_firstname.php';
        } );

        $controllers->get( 'post/update/{id}/lastname', function ( $id ) use ( $app )
        {
            return include_once POST_DIR . '/processing/get/processing_update_lastname.php';
        } );

        $controllers->post( 'post/update/{id}/lastname', function ( $id, Request $lastname ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_lastname.php';
        } );

        $controllers->get( 'post/update/{id}/email', function ( $id ) use ( $app )
        {
            return include_once POST_DIR . '/processing/get/processing_update_email.php';
        } );

        $controllers->post( 'post/update/{id}/email', function ( $id, Request $email ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_email.php';
        } );
        $controllers->get( 'post/update/{id}/content', function ( $id ) use ( $app )
        {
            return include_once POST_DIR . '/processing/get/processing_update_content.php';
        } );

        $controllers->post( 'post/update/{id}/content', function ( $id, Request $content ) use ( $app, $gbFunctions )
        {
            return include_once POST_DIR . '/processing/post/processing_update_content.php';
        } );            
        
        //Beitrag löschen        
        $controllers->get( 'post/delete', function ( Request $currentpage ) use ( $app )
        {
            return include_once POST_DIR . '/display/display_delete.php';
        } )->bind( 'postDelete' );

        $controllers->get( 'post/delete/{id}', function ( $id ) use ( $app )
        {
            return include_once POST_DIR . '/processing/get/processing_delete_id.php';
        } );

        return $controllers;
    }

    /**
     * @return array
     */
    private function sanitizeLogindata ( array $params )
    {
        $data = array(
            "username" => filter_var( trim( $params["username"] ), FILTER_SANITIZE_STRING ),
            "useremail" => filter_var( trim( $params["useremail"] ), FILTER_VALIDATE_EMAIL ),
            "password" => filter_var( trim( $params["password"] ), FILTER_SANITIZE_STRING )
        );

        return $data;
    }

    /**
     * @return array
     */
    private function sanitizeIndividualFields ( array $params )
    {
        $data = array();

        switch ( $params )
        {
            case isset( $params['username'] ):
                $data['username'] = filter_var( trim( $params['username'] ), FILTER_SANITIZE_STRING );
                break;

            case isset( $params['useremail'] ):
                $data['useremail'] = filter_var( trim( $params['useremail'] ), FILTER_VALIDATE_EMAIL );
                break;

            case isset( $params['password'] ):
                $data['password'] = filter_var( trim( $params['password'] ), FILTER_SANITIZE_STRING );
                break;

            case isset( $params['firstname'] ):
                $data['firstname'] = filter_var( trim( $params['firstname'] ), FILTER_SANITIZE_STRING );
                break;

            case isset( $params['lastname'] ):
                $data['lastname'] = filter_var( trim( $params['lastname'] ), FILTER_SANITIZE_STRING );
                break;            
                        
            case isset( $params['email'] ):
                $data['email'] = filter_var( trim( $params['email'] ), FILTER_VALIDATE_EMAIL );
                break;
                        
            case isset( $params['textinput'] ):
                $data['textinput'] = filter_var( trim( $params['textinput'] ), FILTER_SANITIZE_STRING );
                break;
        }

        return $data;
    }

}
