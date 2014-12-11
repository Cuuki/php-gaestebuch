<?php

namespace Guestbook;

class User implements DatabaseQueriesInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $useremail;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $role;

//    TODO User raus aus funktionennamen
//    MAGIC METHODS
//    Setter für Attribute


    public function __construct ()
    {
        
    }

    // Getter

    /**
     * @return int
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail ()
    {
        return $this->useremail;
    }

    /**
     * @return string
     */
    public function getPassword ()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRole ()
    {
        return $this->role;
    }

    // Setter
    // Queries

    /**
     * @return array
     */
    public function select ( $db )
    {
        $select = 'SELECT * FROM user';

        return $db->fetchAll( $select );
    }

    /**
     * @return stmt
     */
    public function update ( $db )
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

    /**
     * @return stmt
     */
    public function insert ( $db )
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

    /**
     * @return stmt
     */
    public function delete ( $db )
    {
        return $db->delete( 'user', array( 'id' => $this->id ) );
    }

    /**
     * @return array
     */
    public function selectByName ( $db )
    {
        $select = 'SELECT * FROM user WHERE username = ?';

        return $db->fetchAssoc( $select, array( $this->username ) );
    }

//TODO selectByName und selectById zusammenfassen
    /**
     * @return array
     */
    public function selectById ( $db )
    {
        $select = 'SELECT * FROM user WHERE id = ?';

        return $db->fetchAssoc( $select, array( $this->id ) );
    }

    /**
     * @return stmt
     */
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

    /**
     * @return stmt
     */
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

    /**
     * @return stmt
     */
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

}
