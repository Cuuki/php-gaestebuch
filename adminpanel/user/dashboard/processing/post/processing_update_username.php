<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
		'username' => $username->get('username')
);	

$userData = getUser( $db, $id );

foreach($userData as $user)
{
        $id = $user['id'];
}

$postdata = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $postdata );

if( ! empty( $invalidInput ) )
{
        $errorMessages = getErrorMessages( $invalidInput );
        return new Response( implode('<br>', $errorMessages), 201 );
}
else
{
        include_once USER_DIR . '/dashboard/update.php';
        if( updateUsername( $db, $postdata['username'], $id ) )
        {
                return new Response( 'Die Daten wurden geändert! ' . 
                        '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
        }
        // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
        else
        {
                return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
        }
}
