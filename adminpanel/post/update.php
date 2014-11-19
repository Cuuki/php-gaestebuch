<?php

/**
 * @return string
 */
function getUpdateForm ()
{
    return file_get_contents(__DIR__ . '/../inc/templates/post_form.html');
};

/**
 * @return string
 */
function displayUpdateEntries ( $data )
{
    $output = '';

    foreach($data as $row)
    {
        $id_entry = $row['id_entry'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $content = $row['content'];
        $created = $row['created'];


        $output .= <<<EOD
            <article style='margin-top: 50px; margin-bottom: 50px;'>
                <p>Vorname: $firstname</p>
                <p>Nachname: $lastname</p>
                <p>E-Mail: $email</p>
                <p>Beitrag: $content</p>
                <p>Erstellt: $created</p>
                <a href='update/$id_entry'>Bearbeiten</a>
            </article>
EOD;
    }

    return $output;
}

/**
 * @return boolean
 */
function updatePost ( mysqli $db, array $params, $id )
{
	$update =	'UPDATE guestbook
				SET
                    firstname = "'. $db->real_escape_string( $params["firstname"] ) .'",
                    lastname = "'. $db->real_escape_string( $params["lastname"] ). '",
                    email = "'. $db->real_escape_string( $params["email"] ). '",                    
                    content = "'. $db->real_escape_string( $params["textinput"] ) .'"
				WHERE
					id_entry = "'. $id .'"';

	$dbRead = $db->query( $update );

    return $dbRead;
}

