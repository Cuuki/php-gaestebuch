<?php

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

// Prüfen ob Formulardaten vorhanden wenn nicht dann raus, sonst weiter
if ( !( isset( $postdata["firstname"] ) && isset( $postdata["lastname"] ) && isset( $postdata["email"] ) && isset( $postdata["textinput"] ) ) )
{
    return;
}

// Ermittelt die Schnittmenge von Arrays
$data = array_intersect_key( $postdata, $data );

$data = sanitizeData( $data );

// Formular Validierungsfunktion aufrufen
$invalidInput = validateForm( $data );

$errorMessages = NULL;
$message = NULL;

// Prüfen ob ungültige Eingaben nicht empty sind, wenn nicht empty dann iteriere invalidInput
if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
}
else
{
    if ( savePosts( $data, $app['db'] ) )
    {
        // hinweis, dass gespeichert wurde
        $message = "Ihr Beitrag wurde erfolgreich gespeichert.";
    }
    else
    {
        // fehlermeldung, keine weiterleitung
        $message = "Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.";
    }
}