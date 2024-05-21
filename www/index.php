<?php
//---------------------------------//
//---DEFAULT FILE - Always first---//
//---------------------------------//

try{
        // global settings
        mb_internal_encoding("UTF-8");
        date_default_timezone_set('Europe/Prague');

        // Nacteni konfigurace a loginu do DB
        require_once("live-data/config.php");
        // načtení zakladních souborů před autoloaderem, dále už nebude potřeba tímto způsobem
        require_once("common-dir/models/Db.php");
        require_once("common-dir/models/EmailSender.php");
        Db::pripoj(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

        $parsedUrl = explode("/", trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/'));

        // trideni pozadavku
        switch ($parsedUrl[0]) {
                case ("oddil"):
                        define("ROOT","oddil");

                        array_shift($parsedUrl);
                        $url = join("/", $parsedUrl);
                        include("oddil-dir/index.php");
                        break;

                case ("api"):
                        define("ROOT","api");

                        array_shift($parsedUrl);
                        $url = join("/", $parsedUrl);
                        include("api-dir/index.php");
                        break;
                                
                case(""):
                case ("main"):
                        define("ROOT","main");

                        array_shift($parsedUrl);
                        $url = join("/", $parsedUrl);
                        include("main-dir/index.php");
                        break;

              
                        
                        
                     
                default:
                        if(!tryDbForwarder($parsedUrl[0]))
                                echo "Wrong side of the Force...";
        }

}
catch(Exception $e)
{
        echo '<span class="i-w-message warn-message">Nastala neočekávaná chyba</span>';
        EmailSender::SendError($e);
}


function tryDbForwarder($fromUrl)
{
        $row = Db::dotazJeden("SELECT * from web_forward where fromUrl=?",array($fromUrl));
        if($row>0)
        {
                header("Location: ".$row["toUrl"]); 
                exit();
        }
}



