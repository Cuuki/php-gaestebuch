<?php

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
                <p>Username: $username</p>
                <p>E-Mail: $useremail</p>
                <p>Passwort: $password</p>
                <a href='$id'>Löschen</a>
            </article>
EOD;
    }

    return $output;
}

function deleteUser ( mysqli $db, $id )
{
    $delete = 'DELETE FROM user WHERE id = "'. $id .'"';

    $dbRead = $db->query( $delete );
}