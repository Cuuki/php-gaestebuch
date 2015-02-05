<?php

$insert = 'INSERT INTO
                user(username, useremail, password, role)
           VALUES
           (
                :username,
                :useremail,
                :password,
                :role
           )';

// lege superuser an, wenn nicht schon vorhanden
if ( getLogindata( $app['db'], 'sudo' ) == NULL )
{
    $app['db']->executeQuery( $insert, array(
        'username' => 'sudo',
        'useremail' => 'sudo@example.de',
        'password' => password_hash( "sudo", PASSWORD_BCRYPT ),
        'role' => 'Administrator'
    ) );
}

// lege user an, wenn nicht schon vorhanden
if ( getLogindata( $app['db'], 'adm' ) == NULL )
{
    $app['db']->executeQuery( $insert, array(
        'username' => 'adm',
        'useremail' => 'adm@example.de',
        'password' => password_hash( "adm", PASSWORD_BCRYPT ),
        'role' => 'Editor'
    ) );
}