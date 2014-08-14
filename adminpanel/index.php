<?php

error_reporting(-1);
ini_set('log_errors', 1);

// Funktionen einbinden
include('../lib/dbconnect.php');
include('../lib/ap-functions.php');
include('../lib/gb-functions.php');
include('../lib/debug-functions.php');

$login = array(
		"username" => "",
		"useremail" => "",
		"password" => ""
	);

include_once "../lib/dbconfig.php";

if ( isset($_POST["register"]) )
{
	if ( ! ( isset( $_POST['username'], $_POST['useremail'], $_POST['password'] ) ) )
	{
		return;
	}

	$login = array_intersect_key($_POST, $login);

	$login = sanitizeData( $login );

	$invalidInput = validateForm( $login );	

	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
	}
	else
	{
		if(	saveLogindata( $login, $db ) != 0 )
		{
			$message = "<p>Danke für Ihre Registrierung!</p>";
		}
		else
		{
			$sql = "SELECT id FROM user WHERE useremail = ? LIMIT 1";
		    $dbPrepare = $db->prepare($sql);

		   // check existing email
		    if ( $dbPrepare )
		    {
		        $dbPrepare->bind_param('s', $login['useremail']);
		        $dbPrepare->execute();
		        $dbPrepare->store_result();		        

		        if ( $dbPrepare->num_rows == 1 )
		        {
		            // A user with this email address already exists
		            $message = '<p>Es existiert bereits ein Benutzer mit dieser E-Mail Adresse. Loggen Sie sich ein!</p>';
		        }
		    }
		    else
		    {
		        $message = "<p>Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</p>";
		    }
		 
		    // check existing username
		    $sql = "SELECT id FROM user WHERE username = ? LIMIT 1";
		    $dbPrepare = $db->prepare($sql);
		 
		    if ( $dbPrepare )
		    {
		        $dbPrepare->bind_param('s', $login['username']);
		        $dbPrepare->execute();
		        $dbPrepare->store_result();
		 
		        if (  $dbPrepare->num_rows == 1)
		        {
		                // A user with this username already exists
		                $message = '<p>Es existiert bereits ein Benutzer mit diesem Benutzernamen. Loggen Sie sich ein!</p>';
		        }
		    }
		    else
		    {
				$message = "<p>Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</p>";
		    }                      
		}
	}
}

if ( isset($_POST["login"]) )
{
	$session = sessionStart();

	if ( isset($login['username'], $login['password']) )
	{
	    $username = $login['username'];
	    $password = $login['password'];
	 
	    if ( login( $db, $username, $password ) == true )
	    {
	        // Login success
	        $message = "<p>Sie haben sich erfolgreich eingeloggt.</p>";
	    }
	    else
	    {
	        // Login failed
	        $message = "<p>Der Login ist fehlgeschlagen.</p>";
	    }
	}
	else
	{
	    // The correct POST variables were not sent to this page. 
	    $message = '<p>Invalid Request</p>';
	}
}

// Header, Content (Posts) und Footer ausgeben
include('inc/header.php');
include('inc/loginpage.php');
include('inc/footer.php');


