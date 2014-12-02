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

// Prüfen ob ungültige Eingaben nicht empty sind, wenn nicht empty dann iteriere invalidInput
if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
}
else
{
    if ( savePosts( $data, $db ) != 0 )
    {
        // hinweis, dass gespeichert wurde
        $message = "<p>Ihr Beitrag wurde erfolgreich gespeichert.</p>";
    }
    else
    {
        // fehlermeldung, keine weiterleitung
        $message = "<p>Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</p>";
    }
}