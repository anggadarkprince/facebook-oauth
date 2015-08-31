<?php
session_start();
if(!empty($_SESSION)){
    header("Location: index.php");
}

echo "Welcome, ".$_SESSION['id']." ".$_SESSION['username']." You have logged in from ".$_SESSION['oauth_provider']." API provider";