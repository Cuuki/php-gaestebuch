<?php

namespace Guestbook;

class Post
{
    private $id_entry;
    private $firstname;
    private $latname;
    private $email;
    private $content;
    private $created;

    /**
     * @return array
     */
    public function getPostById ( $db, $id )
    {
        $select = 'SELECT * FROM guestbook WHERE id_entry = ?';

        return $db->fetchAssoc( $select, array( $id ) );
    }

    /**
     * @return array
     */
    public function getAllPosts ( $db, $rowsperpage, $currentpage )
    {
        $offset = ($currentpage - 1) * $rowsperpage;

        $select = 'SELECT * FROM guestbook ORDER BY created DESC LIMIT ' . (int) $offset . ', ' . (int) $rowsperpage . '';

        return $db->fetchAll( $select );
    }

    /**
     * @return stmt
     */
    public function updatePost ( $db, array $params, $id )
    {
        $update = 'UPDATE
                        guestbook
                   SET
                        firstname = :firstname,
                        lastname = :lastname,
                        email = :email,
                        content = :content
                   WHERE
                        id_entry = :id_entry';

        return $db->executeQuery( $update, array(
                    'firstname' => $params["firstname"],
                    'lastname' => $params["lastname"],
                    'email' => $params["email"],
                    'content' => $params["textinput"],
                    'id_entry' => $id
                ) );
    }

    /**
     * @return stmt
     */
    public function savePost ( array $params, $db )
    {
        $insert = 'INSERT INTO
                        guestbook(firstname, lastname, email, content)
                   VALUES
                   ( 
                        :firstname,
                        :lastname,
                        :email,
                        :content
                   )';

        return $db->executeQuery( $insert, array(
                    'firstname' => $params["firstname"],
                    'lastname' => $params["lastname"],
                    'email' => $params["email"],
                    'content' => $params["textinput"]
                ) );
    }

    /**
     * @return stmt
     */
    public function deletePost ( $db, $id )
    {
        return $db->delete( 'guestbook', array( 'id_entry' => $id ) );
    }

}
