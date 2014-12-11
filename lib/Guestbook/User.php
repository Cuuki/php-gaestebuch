<?php

namespace Guestbook;

//use Exception;

class User extends AbstractUser
{

    // set attributes to passed data
    public function __construct ( array $data )
    {
        if ( isset( $data['id'] ) )
        {
            $this->id = $data['id'];
        }
        elseif ( isset( $data['username'] ) )
        {
            $this->username = $data['username'];
        }
        elseif ( isset( $data['useremail'] ) )
        {
            $this->useremail = $data['useremail'];
        }
        elseif ( isset( $data['password'] ) )
        {
            $this->password = $data['password'];
        }
        elseif ( isset( $data['role'] ) )
        {
            $this->role = $data['role'];
        }
//        else
//        {
//            throw new Exception( 'No attributes set!', 404 );
//        }
    }

    public function getUserByName ( $db )
    {
        $select = 'SELECT * FROM user WHERE username = ?';

        return $db->fetchAssoc( $select, array( $this->username ) );
    }

    public function getUserById ( $db )
    {
        $select = 'SELECT * FROM user WHERE id = ?';

        return $db->fetchAssoc( $select, array( $this->id ) );
    }

    public function getUsers ( $db )
    {
        $select = 'SELECT * FROM user';

        return $db->fetchAll( $select );
    }

    public function updateUser ( $db )
    {
        $update = 'UPDATE
                        user
                   SET
                        username = :username,
                        useremail = :useremail,
                        password = :password
                   WHERE
                        id = :id';

        return $db->executeQuery( $update, array(
                    'username' => $this->username,
                    'useremail' => $this->useremail,
                    'password' => password_hash( $this->password, PASSWORD_BCRYPT ),
                    'id' => $this->id
                ) );
    }

    public function updateUsername ( $db )
    {
        $update = 'UPDATE
                        user
                   SET
                        username = :username
                   WHERE
                        id = :id';

        return $db->executeQuery( $update, array(
                    'username' => $this->username,
                    'id' => $this->id
                ) );
    }

    public function updateEmail ( $db )
    {
        $update = 'UPDATE
                        user
                   SET
                        useremail = :useremail
                   WHERE
                        id = :id';

        return $db->executeQuery( $update, array(
                    'useremail' => $this->email,
                    'id' => $this->id
                ) );
    }

    public function updatePassword ( $db )
    {
        $update = 'UPDATE
                        user
                   SET
                        password = :password
                   WHERE
                        id = :id';

        return $db->executeQuery( $update, array(
                    'password' => password_hash( $this->password, PASSWORD_BCRYPT ),
                    'id' => $this->id
                ) );
    }

    public function saveUser ( $db )
    {
        $insert = 'INSERT INTO
                        user(username, useremail, password)
                   VALUES
                   (
                        :username,
                        :useremail,
                        :password
                   )';

        return $db->executeQuery( $insert, array(
                    'username' => $this->username,
                    'useremail' => $this->useremail,
                    'password' => password_hash( $this->password, PASSWORD_BCRYPT )
                ) );
    }

    public function deleteUser ( $db )
    {
        return $db->delete( 'user', array( 'id' => $this->id ) );
    }

}
