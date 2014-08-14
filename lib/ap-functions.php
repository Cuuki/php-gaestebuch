<?php

error_reporting(-1);
ini_set('log_errors', 1);

/**
 * @return int
 */
function saveLogindata ( array $params, mysqli $db )
{
    $insert = 'INSERT INTO
                    user(username, useremail, password)
                VALUES
                (
                    "'. $db->real_escape_string( $params["username"] ) .'",
                    "'. $db->real_escape_string( $params["useremail"] ). '",
                    "'. $db->real_escape_string( $params["password"] ) .'"
                )';
    
    $db->query( $insert );

    return $db->insert_id;
}

/**
 * @return array
 */
function getLogindata ( mysqli $db )
{
    $sql = "SELECT
                username, useremail, password
            FROM
                user";

    $dbRead = $db->query( $sql );

    $logindata = array();

    while( $row = $dbRead->fetch_assoc() )
    {
        array_push($logindata, $row);
    }

    return $logindata;
}

/**
 * @return boolean
 */
function sessionStart ()
{
    $session_name = 'login_session';
    $secure = false;

    // This stops JavaScript being able to access the session id.
    $httponly = true;

    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === false)
    {
        header("Location: inc/error.php");
    }

    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();

    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly
    );
    
    session_name($session_name);
    session_start();

    return session_regenerate_id();
}

/**
 * @return boolean
 */
function login ( mysqli $db, $username, $password )
{
    if ( $sql = $db->prepare("SELECT id, username, password, salt FROM users WHERE username = ? LIMIT 1") )
    {
        $sql->bind_param('s', $username());
        $sql->execute();
        $sql->store_result();

        // get variables from result.
        $sql->bind_result($user_id, $username, $db_password, $salt);
        $sql->fetch();

        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);

        if ( $sql->num_rows == 1 )
        {
            // Check if the password in the database matches
            // the password the user submitted.
            if ( $db_password == $password )
            {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                
                // Login successful.
                return true;
            }
            else
            {
                // Password is not correct
                // We record this attempt in the database
                $now = time();
                $db->query(
                    "INSERT INTO
                        login_attempts(user_id, time)
                    VALUES 
                    (
                        '$user_id', '$now'
                    )"
                );

                return false;
            }
        }
        else
        {
            // No user exists.
            return false;
        }
    }
}

/**
 * @return boolean
 */
function login_check (mysqli $db)
{
    // Check if all session variables are set 
    if ( isset( $_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'] ) )
    {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ( $sql = $db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1") )
        {
            // Bind "$user_id" to parameter. 
            $sql->bind_param('i', $user_id);
            $sql->execute();
            $sql->store_result();
 
            if ( $sql->num_rows == 1 )
            {
                // If the user exists get variables from result.
                $sql->bind_result($password);
                $sql->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ( $login_check == $login_string )
                {
                    // Logged in
                    return true;
                }
                else
                {
                    // Not logged in
                    return false;
                }
            }
            else
            {
                // Not logged in 
                return false;
            }
        }
        else
        {
            // Not logged in 
            return false;
        }
    }
    else
    {
        // Not logged in 
        return false;
    }
}

function logout ()
{
    // Unset all session values 
    $_SESSION = array();
     
    // get session parameters 
    $params = session_get_cookie_params();
     
    // Delete the actual cookie. 
    setcookie(session_name(),
            '', time() - 42000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]);
     
    // Destroy session 
    session_destroy();
    header('Location: index.php');
}

