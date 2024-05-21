<?php
require("common-dir/sessions.php");

function autoloadFunkce($trida)
{
        // Končí název třídy řetězcem "Kontroler" ?
        if (preg_match('/Controller$/', $trida)) {
                if (file_exists(ROOT . "-dir/controllers/" . $trida . ".php"))
                        require(ROOT . "-dir/controllers/" . $trida . ".php");
                else
                        require("common-dir/controllers/" . $trida . ".php");
        } 
        else
        {
                if (file_exists(ROOT . "-dir/models/" . $trida . ".php"))
                        require(ROOT . "-dir/models/" . $trida . ".php");
                else if(file_exists("common-dir/models/" . $trida . ".php"))
                        require("common-dir/models/" . $trida . ".php"); 
        }
                
}

spl_autoload_register("autoloadFunkce");
// // autoload z composeru
// require 'vendor/autoload.php';


$smerovac = new SmerovacController();
$smerovac->zpracuj(array($url));
$smerovac->vypisPohled();
