<?php

error_reporting(-1);
ini_set('log_errors', 1);

// Dateien einbinden
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/../lib/debug-functions.php';
include_once __DIR__ . '/../lib/dbconnect.php';
include_once __DIR__ . '/../lib/dbconfig.php';
$apFunctions = include_once __DIR__ . '/../lib/ap-functions.php';
$gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';
// Superuser anlegen
include_once __DIR__ . '/sudo-config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

define( 'ROUTES_DIR' , realpath( __DIR__ . '/routes' ));
define( 'USER_DIR' , realpath( __DIR__ . '/user' ));
define( 'POST_DIR' , realpath( __DIR__ . '/post' ));

// Mail Encoding auf UTF-8 setzen
mb_internal_encoding("UTF-8");

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = TRUE;

$app['session.storage.options'] = array(
	'lifetime' => 900
);

$app->before( function () use ( $app )
{
        $getPath = $app['request_context']->getPathInfo();
        
        // Wenn Session null dann true und führe aus, wenn Session nicht null dann false und führe nicht aus
        if( ! $app['session']->get('user') )
        {
            // Wenn Pfad = auth/login dann nicht redirecten
            if( $getPath == '/auth/login' || $getPath == '/auth/reset' || $getPath == '/auth/reset/code' )
            {
                return;
            }           
            return $app->redirect($app['url_generator']->generate('login'));
        }
});

$app->get('/', function () use ( $app )
{
	return $app->redirect( 'user/dashboard/' );
});

$app->get('/post/', function () use ( $app )
{
	return $app->redirect( '../user/dashboard/' );
});

$app->get('/user/', function () use ( $app )
{
	return $app->redirect('dashboard/');
});

$app->get('/auth/', function () use ( $app )
{
	return $app->redirect( 'login' );
});

$app->get('/auth/login', function () use ( $app, $db, $apFunctions )
{
        // Wenn bereits eingeloggt weiterleiten auf Dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}
        
	$route = include_once ROUTES_DIR . '/auth/login.php';

	return $route( $app );
})->bind('login');

$app->post('/auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $db, $apFunctions )
{
        $processing = include_once ROUTES_DIR . '/auth/processing_login.php';
        
        return $processing;
});

$app->get('/auth/reset', function () use ( $app, $db, $apFunctions )
{
        // Wenn bereits eingeloggt weiterleiten auf Dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}
        
	$route = include_once ROUTES_DIR . '/auth/reset.php';

	return $route( $app );
});

$app->post('/auth/reset', function ( Request $email ) use ( $db, $app )
{
        $processing = include_once ROUTES_DIR . '/auth/processing_reset.php';
        
        return $processing;  
});

$app->get('auth/reset/code', function () use ( $app )
{
        // Wenn bereits eingeloggt weiterleiten auf Dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}
        
	include_once ROUTES_DIR . '/auth/code.php';

	return getCodeForm();
});

$app->post('auth/reset/code', function ( Request $code, Request $password ) use ( $db, $app )
{
        $processing = include_once ROUTES_DIR . '/auth/processing_code.php';
        
        return $processing;   
});

// User Einstellungen
$app->get('/user/dashboard/settings', function () use ( $app )
{	$route = include_once USER_DIR . '/settings.php';

	return $route( $app );
})->bind('settings');

// Userzeile als Rückmeldung das er eingeloggt ist
$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="/php-gaestebuch/adminpanel' . 
        $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

$app->get('/user/dashboard/', function () use ( $app, $userHeader )
{
	$loggedInSince = $app['session']->get('time');

	$route = include_once USER_DIR . '/dashboard.php';

	return new Response ( $userHeader . 'Sie sind eingeloggt seid: ' . date('h:i:sa, d.m.Y', $loggedInSince) . '<br>' . $route( $app ), 201 );
})->bind('dashboard');

$app->get('/user/dashboard/settings/username', function () use ( $app )
{
	// Alter Benutzername, Neuer Benutzername einbinden
	$route = include_once USER_DIR . '/dashboard/settings_username.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changeUsername');

$app->post('/user/dashboard/settings/username', function ( Request $username ) use ( $db, $app, $apFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_settings_username.php';
        
        return $processing;
});

$app->get('/user/dashboard/settings/password', function () use ( $app )
{
	// Altes Passwort, Neues Passwort einbinden
	$route = include_once USER_DIR . '/dashboard/settings_password.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changePassword');

$app->post('/user/dashboard/settings/password', function ( Request $password ) use ( $db, $app, $apFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_settings_password.php';
        
        return $processing;
});

$app->get('/user/dashboard/settings/email', function () use ( $app )
{
	// Alte E-Mail, Neue E-Mail einbinden
	$route = include_once USER_DIR . '/dashboard/settings_email.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changeEmail');

$app->post('/user/dashboard/settings/email', function ( Request $email ) use ( $db, $app, $apFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_settings_email.php';
        
        return $processing;
});

// Benutzer hinzufügen
$app->get('/user/dashboard/add', function () use ( $app, $gbFunctions, $userHeader )
{
	$route = include_once USER_DIR . '/dashboard/add.php';

	return new Response ( $userHeader . $route( $app ), 201 );
})->bind('add');

$app->post('/user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_add.php';
        
        return $processing;
});

// Benutzerdaten bearbeiten
$app->get('/user/dashboard/update/', function () use ( $app, $db, $apFunctions, $userHeader )
{
        include_once USER_DIR . '/dashboard/display_update.php';
        
	return new Response( $userHeader . displayPagination ( $currentpage, $totalpages ) . $displayUpdateUsers . 
                '<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('update');

$app->get('/user/dashboard/update/{id}', function ( $id ) use ( $app, $db, $apFunctions, $gbFunctions, $userHeader )
{
	include_once USER_DIR . '/dashboard/display_update_id.php';

	return new Response( $userHeader . $displayUser . $userForm, 201 );
});

$app->post('/user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_update_id.php';
        
        return $processing;
});

$app->get('/user/dashboard/update/{id}/username', function ( $id ) use ( $app, $db, $userHeader )
{
	$route = include_once USER_DIR . '/dashboard/update_username.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $db, $apFunctions, $gbFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_update_username.php';
        
        return $processing;	
});

$app->get('/user/dashboard/update/{id}/email', function ( $id ) use ( $app, $db, $userHeader )
{
	$route = include_once USER_DIR . '/dashboard/update_email.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $db, $apFunctions, $gbFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_update_email.php';
        
        return $processing;
});

$app->get('/user/dashboard/update/{id}/password', function ( $id ) use ( $app, $db, $userHeader )
{
	$route = include_once USER_DIR . '/dashboard/update_password.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_update_password.php';
        
        return $processing;
});

// Benutzer löschen
$app->get('/user/dashboard/delete/', function () use ( $app, $db, $apFunctions, $userHeader )
{
	include_once USER_DIR . '/dashboard/display_delete.php';

	return new Response( $userHeader . displayPagination ( $currentpage, $totalpages ) . $displayDeleteUsers .
		'<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('delete');

$app->get('/user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
{
        $processing = include_once USER_DIR . '/dashboard/processing_delete.php';
        
        return $processing;	
});

// Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
$app->get('/post/add', function () use ( $app )
{
	return $app->redirect('../../#add');
});

$app->get('/post/update', function () use ( $db, $app, $userHeader )
{
	include_once POST_DIR . '/display_update.php';

	include_once POST_DIR . '/update.php';

	return new Response ( $userHeader . displayPagination( $currentpage, $totalpages ) . displayUpdateEntries( $posts ) . 
                '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
})->bind('postUpdate');

$app->get('/post/update/{id}', function ( $id ) use ( $db, $app, $gbFunctions, $userHeader )
{
	include_once POST_DIR . '/update.php';

	$entryData = getEntry( $db, $id );
	$postForm = getUpdateForm();

	return new Response ( $userHeader . displayPosts( $entryData ) . $postForm, 201 ) ;
});

$app->post('/post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $app, $gbFunctions, $userHeader )
{
        $processing = include_once POST_DIR . '/processing_update.php';
        
        return $processing;
});

$app->get('/post/delete', function () use ( $db, $app, $userHeader )
{
	include_once POST_DIR . '/display_delete.php';

	include_once POST_DIR . '/delete.php';

	return new Response ( $userHeader . displayPagination( $currentpage, $totalpages ) . displayDeleteEntries( $posts ) . 
                '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
})->bind('deletePost');

$app->get('/post/delete/{id}', function ( $id ) use ( $db, $app )
{
        $processing = include_once POST_DIR . '/processing_delete.php';
        
        return $processing;	
});

// Ausloggen
$app->get('/user/dashboard/logout', function () use ( $app )
{
	session_destroy();

	// nach ausloggen weiterleiten auf loginseite
	return $app->redirect( $app['url_generator']->generate('login') );
})->bind('logout');

// Loginsession starten
$app['session']->start();

// Letzte Aktivität in der Session
$sessionLastUsed = $app['session']->getMetadataBag()->getLastUsed();

var_dump($app['session']->get('cookie_lifetime'));

// Wenn nach 15 Minuten (900sek) keine Aktivität in der Session war und das Cookie Lifetime nicht 0 ist zerstöre diese
if ( ( $app['session']->get('cookie_lifetime') !== 0 ) && ( time() - $sessionLastUsed > 900 ) )
{
    session_destroy();
}

$app->run();