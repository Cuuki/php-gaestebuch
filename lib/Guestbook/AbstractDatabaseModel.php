<?php

namespace Guestbook;

use Doctrine\DBAL\Driver\PDOStatement;

abstract class AbstractDatabaseModel implements DatabaseInterface
{
    /**
     * Database Connection
     * */
    private $db;

    /**
     * Set property db to passed database connection on initialisation of class
     * @return object
     */
    public function __construct ( $connection )
    {
        $this->db = $connection;

        return $this;
    }

    /**
     * Get Value of passed property
     */
    public function get ( $property )
    {
        return $this->$property;
    }

    /**
     * Set Value of passed property
     * @return object
     */
    public function set ( $property, $value )
    {
        $this->$property = $value;

        return $this;
    }

    /**
     * Iterate all Protected/Public properties
     * @return object
     */
    protected function iterateProperties ()
    {
        foreach ( $this as $key => $value )
        {
            $properties[$key] = $value;
        }

        return $properties;
    }

    /**
     * Select all from passed table
     * @return array
     */
    public function select ( $tablename )
    {
        $select = 'SELECT * FROM ' . $tablename . '';

        return $this->db->fetchAll( $select );
    }

    /**
     * Select all from passed table
     * Set value of WHERE to passed property
     * @return array
     */
    public function selectByAttribute ( $tablename, $property )
    {
        $select = 'SELECT * FROM ' . $tablename . ' WHERE ' . $property . '= ?';

        return $this->db->fetchAssoc( $select, array( $this->$property ) );
    }

    /**
     * Insert into passed table
     * Set VALUES to properties
     * @return PDOStatement
     */
    public function insert ( $tablename )
    {
        $properties = $this->iterateProperties();

        $insert = 'INSERT INTO
                        ' . $tablename . ' (' . implode( ', ', array_keys( $properties ) ) . ')
                   VALUES
                   (
                        :' . implode( ', :', array_keys( $properties ) ) . '
                   )';

        return $this->db->executeQuery( $insert, $properties );
    }

    /**
     * Update passed table
     * Set properties to values of properties
     * Set value of WHERE to passed property
     * @return PDOStatement
     */
    public function update ( $tablename, $property )
    {
        $properties = $this->iterateProperties();

        foreach ( array_keys( $properties ) as $key )
        {
            $arr[$key] = $key;

            $newarr[] = $arr[$key] . ' = :' . $arr[$key];
        }

        $update = 'UPDATE
                        ' . $tablename . '
                   SET
                        ' . implode( ', ', $newarr ) . '
                   WHERE
                        ' . $property . ' = :' . $property . '';

        return $this->db->executeQuery( $update, $properties );
    }

    /**
     * Delete from passed table
     * Set value of WHERE to passed property
     * @return PDOStatement
     */
    public function delete ( $tablename, $property )
    {
        // delete WHERE Property = Value of Property   
        return $this->db->delete( $tablename, array( $property => $this->$property ) );
    }

}
