<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'firstname' => $firstname->get( 'firstname' ),
    'lastname' => $lastname->get( 'lastname' ),
    'email' => $email->get( 'email' ),
    'textinput' => $textinput->get( 'textinput' )
);

include_once POST_DIR . '/update.php';

$entryData = getEntry( $db, $id );

foreach ( $entryData as $post )
{
    $oldcontent = $post['content'];
}

$postdata = sanitizeData( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
    return new Response( implode( '<br>', $errorMessages ), 201 );
}
elseif ( $postdata['textinput'] == $oldcontent )
{
    return new Response( 'Bitte geben Sie mindestenes einen neuen Beitrag ein!', 404 );
}
else
{
    if ( updatePost( $db, $postdata, $id ) )
    {
        return new Response( 'Die Daten wurden geändert!  ' .
                '<a href="' . $app['url_generator']->generate( 'postUpdate' ) . '">Zurück</a>', 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );
    }
}
