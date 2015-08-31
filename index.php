<?php

if(!empty($_SESSION)){
    header("Location: home.php");
}

# We require the library
require("facebook.php");

# Creating the facebook object
$facebook = new Facebook(array(
    'appId'  => 'YOUR_APP_ID',
    'secret' => 'YOUR_APP_SECRET',
    'cookie' => true
));

# Let's see if we have an active session
$session = $facebook->getUser();

if(!empty($session)) {
    # Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
    try{
        $uid = $facebook->getUser();
        $user = $facebook->api('/me');
    } catch (Exception $e){}

    if(!empty($user)){
        # User info ok? Let's print it (Here we will be adding the login and registering routines)
        print_r($user);


        mysql_connect('localhost', 'root', '') or die("Invalid database connection");
        mysql_select_db('sandbox_crud') or die("invalid database selection");

        # We have an active session; let's check if we've already registered the user
        $query = mysql_query("SELECT * FROM users WHERE oauth_provider = 'facebook' AND oauth_uid = ". $user['id']);
        $result = mysql_fetch_array($query);

        # If not, let's add it to the database
        if(empty($result)){
            $query = mysql_query("INSERT INTO users (oauth_provider, oauth_uid, username) VALUES ('facebook', {$user['id']}, '{$user['name']}')");
            $query = msyql_query("SELECT * FROM users WHERE id = " . mysql_insert_id());
            $result = mysql_fetch_array($query);
        }

        # let's set session values
        session_start();
        $_SESSION['id'] = $result['id'];
        $_SESSION['oauth_uid'] = $result['oauth_uid'];
        $_SESSION['oauth_provider'] = $result['oauth_provider'];
        $_SESSION['username'] = $result['username'];

    } else {
        # For testing purposes, if there was an error, let's kill the script
        die("There was an error.");
    }
} else {
    # There's no active session, let's generate one
    $login_url = $facebook->getLoginUrl();
    header("Location: ".$login_url);
}