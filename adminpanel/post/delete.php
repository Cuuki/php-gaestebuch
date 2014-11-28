<?php

/**
 * TODO: Template
 * @return string
 */
function displayDeleteEntries ( $data )
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
                <a href='delete/$id_entry'>Löschen</a>
            </article>
EOD;
    }

    return $output;
}

/**
 * TODO: Doctrine
 * @return boolean
 */
function deletePost ( mysqli $db, $id )
{
    $delete = 'DELETE FROM guestbook WHERE id_entry = "'. $id .'"';

    $dbRead = $db->query( $delete );

    return $dbRead;
}