<?php

/**
 * @return string
 */
function getUpdateForm ()
{
    return file_get_contents(__DIR__ . '/../../inc/templates/user_form.html');
}

/**
 * @return string
 */
function displayUser ( $data )
{
    $output = '';

    foreach($data as $row)
    {
        $username = $row['username'];
        $useremail = $row['useremail'];
        $password = $row['password'];
        $id = $row['id'];

        $output .= <<<EOD
            <article style='margin-bottom: 50px;'>
                <p>Benutzername: $username</p>
                <p><a href='$id/username'>Nur Benutzername bearbeiten</a></p>
                <br>
                <p>E-Mail: $useremail</p>
                <p><a href='$id/email'>Nur E-Mail bearbeiten</a></p>
                <br>
                <p>Passwort: $password</p>
                <p><a href='$id/password'>Nur Passwort bearbeiten</a></p>
            </article>
EOD;
    }

    return $output;
}

/**
 * @return string
 */
function displayUpdateUsers ( $data )
{
    $output = '';

    foreach($data as $row)
    {
        $username = $row['username'];
        $useremail = $row['useremail'];
        $password = $row['password'];
        $id = $row['id'];

        $output .= <<<EOD
            <article style='margin-top: 50px; margin-bottom: 50px;'>
                <p>Benutzername: $username</p>
                <p>E-Mail: $useremail</p>
                <p>Passwort: $password</p>
                <a href='$id'>Bearbeiten</a>
            </article>
EOD;
    }

    return $output;
}

/**
 * @return boolean
 */
function updateUser ( mysqli $db, array $params, $id )
{
	$update =	'UPDATE user
				SET
                    username = "'. $db->real_escape_string( $params["username"] ) .'",
                    useremail = "'. $db->real_escape_string( $params["useremail"] ). '",
                    password = "'. $db->real_escape_string( password_hash( $params["password"], PASSWORD_BCRYPT ) ) .'"
				WHERE
					id = "'. $id .'"';

	$dbRead = $db->query( $update );

    return $dbRead;
}

/**
 * @return boolean
 */
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

/**
 * @return boolean
 */
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

/**
 * @return boolean
 */
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

