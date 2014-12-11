<?php

namespace Guestbook;

class Post extends AbstractPost
{

    // set attributes to passed data
    public function __construct ( array $data )
    {
        if ( isset( $data['id_entry'] ) )
        {
            $this->id_entry = $data['id_entry'];
        }
        elseif ( isset( $data['firstname'] ) )
        {
            $this->firstname = $data['firstname'];
        }
        elseif ( isset( $data['lastname'] ) )
        {
            $this->lastname = $data['lastname'];
        }
        elseif ( isset( $data['email'] ) )
        {
            $this->email = $data['email'];
        }
        elseif ( isset( $data['textinput'] ) )
        {
            $this->content = $data['textinput'];
        }
    }

    /**
     * @return array
     */
    public function getPostById ( $db )
    {
        $select = 'SELECT * FROM guestbook WHERE id_entry = ?';

        return $db->fetchAssoc( $select, array( $this->id ) );
    }

    /**
     * @return array
     */
    public function getPosts ( $db, $rowsperpage, $currentpage )
    {
        $offset = ($currentpage - 1) * $rowsperpage;

        $select = 'SELECT * FROM guestbook ORDER BY created DESC LIMIT ' . (int) $offset . ', ' . (int) $rowsperpage . '';

        return $db->fetchAll( $select );
    }

    /**
     * @return stmt
     */
    public function updatePost ( $db )
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
                    'firstname' => $this->firstname,
                    'lastname' => $this->lastname,
                    'email' => $this->email,
                    'content' => $this->content,
                    'id_entry' => $this->id_entry
                ) );
    }

    /**
     * @return stmt
     */
    public function savePost ( $db )
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
                    'firstname' => $this->firstname,
                    'lastname' => $this->lastname,
                    'email' => $this->email,
                    'content' => $this->content,
                ) );
    }

    /**
     * @return stmt
     */
    public function deletePost ( $db )
    {
        return $db->delete( 'guestbook', array( 'id_entry' => $this->id ) );
    }

}
