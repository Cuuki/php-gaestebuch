<?php

/**
 * @return array
 */
function getSelectedPost ( mysqli $db, $id )
{
    $sql = 'SELECT
                firstname, lastname, email, content, created
            FROM
                guestbook
            WHERE
            	id_entry = "'. $id .'"';

    $dbRead = $db->query( $sql );
    $userdata = array();

    $row = $dbRead->fetch_assoc();

    array_push($userdata, $row);

    return $userdata;
}

/**
 * @return string
 */
function displayPostsforUpdate ( $data )
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
 * @return string
 */
function displaySelectedPost ( $data )
{
    $output = '';

    foreach($data as $row)
    {
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
            </article>
EOD;
    }

    return $output;
}

function getForm ()
{
	return file_get_contents(__DIR__ . '/../inc/post/update-form.html');
};

function update ( mysqli $db, array $params, $id )
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

function updateUsername ( mysqli $db, $username, $id )
{
    $update =   'UPDATE user
                SET
                    username = "'. $db->real_escape_string( $username ) .'"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

function updateEmail ( mysqli $db, $email, $id )
{
    $update =   'UPDATE user
                SET
                    useremail = "'. $db->real_escape_string( $email ). '"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

function updatePassword ( mysqli $db, $password, $id )
{
    $update =   'UPDATE user
                SET
                    password = "'. $db->real_escape_string( password_hash( $password, PASSWORD_BCRYPT ) ) .'"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

