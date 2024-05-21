<?php
// Start the session
//session_set_cookie_params(3600,"/");
session_start();


if(!isset($_SESSION["firstStart"]))//prvni spusteni session
{
    $_SESSION["firstStart"]=false;
    $_SESSION["currentRequest"]=$_SERVER['REQUEST_URI'];

    $_SESSION["logged"]=false;
   
    
    //echo("session started");
}
/*else{
    
    if($_SESSION["webName"]!=ROOT){
        session_destroy();
        header("Location: /".ROOT);
        header("Connection: close");
        exit;
    }

}*/
if(!in_array("back",explode('/',$_SERVER['REQUEST_URI'])))
{
    $_SESSION["lastRequest"]    = $_SESSION["currentRequest"];
    $_SESSION["currentRequest"] = $_SERVER['REQUEST_URI'];
}

