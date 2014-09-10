<?php

/**
 * @return array
 */
function getSelectedUser ( mysqli $db, $id )
{
    $sql = 'SELECT
                id, username, useremail, password
            FROM
                user
            WHERE
            	id = "'. $id .'"';

    $dbRead = $db->query( $sql );
    $userdata = array();

    $row = $dbRead->fetch_assoc();

    array_push($userdata, $row);

    return $userdata;
}

/**
 * @return string
 */
function displayUsers ( $data )
{
    $output = '';

    foreach($data as $row)
    {
        $username = $row['username'];
        $useremail = $row['useremail'];
        $password = $row['password'];
        $id = $row["id"];

        $output .= <<<EOD
            <article style='margin-bottom: 50px;'>
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
 * @return string
 */
function displaySelectedUser ( $data )
{
    $output = '';

    foreach($data as $row)
    {
        $username = $row['username'];
        $useremail = $row['useremail'];
        $password = $row['password'];

        $output .= <<<EOD
            <article style='margin-bottom: 50px;'>
                <p>Benutzername: $username</p>
                <p>E-Mail: $useremail</p>
                <p>Passwort: $password</p>
            </article>
EOD;
    }

    return $output;
}

function getForm ()
{
	return file_get_contents(__DIR__ . '/../../inc/post/update-form.html');
};

function update ( mysqli $db, array $params, $id )
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

