<?php

namespace Guestbook;

class User extends AbstractDatabaseModel
{
//    TODO: methoden wie updatePassword in konkrete Klasse (Sonderfälle wie zb password),
//    wenn kein Sonderfall eintrifft parent:: aufrufen mit Standard ausführung

    /**
     * Automatically generated and increased
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $useremail;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $role;

    /**
     * @return Doctrine\DBAL\Driver\PDOStatement
     */
    public function updateUsername ( $tablename )
    {
        $update = 'UPDATE
                        ' . $tablename . '
                   SET
                        username = :username
                   WHERE
                        id = :id';

        return $this->db->executeQuery( $update, array(
                    'username' => $this->username,
                    'id' => $this->id
                ) );
    }

    /**
     * @return Doctrine\DBAL\Driver\PDOStatement
     */
    public function updateEmail ( $tablename )
    {
        $update = 'UPDATE
                        ' . $tablename . '
                   SET
                        useremail = :useremail
                   WHERE
                        id = :id';

        return $this->db->executeQuery( $update, array(
                    'useremail' => $this->email,
                    'id' => $this->id
                ) );
    }

    /**
     * @return Doctrine\DBAL\Driver\PDOStatement
     */
    public function updatePassword ( $tablename )
    {
        $update = 'UPDATE
                        ' . $tablename . '
                   SET
                        password = :password
                   WHERE
                        id = :id';

        return $this->db->executeQuery( $update, array(
                    'password' => password_hash( $this->password, PASSWORD_BCRYPT ),
                    'id' => $this->id
                ) );
    }

}
