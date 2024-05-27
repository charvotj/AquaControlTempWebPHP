<?php

class ConfigurationController extends Controller
{
    public function zpracuj($parametry) // parametry obsahují kusy url, viz směrovač
    {
        // nastaveni sablony
        $this->pohled = null; // nebude se vypisovat, není třeba nastavovat
        $this->responseCode = 200; // HTTP OK
        
        // kontrola verifikace
        if(empty($_SERVER["HTTP_API_DIGEST"]) || empty($_SERVER["HTTP_UNIX"]) || !ApiManager::checkDigest($_SERVER["HTTP_API_DIGEST"], $_SERVER["HTTP_UNIX"]))
        {
            // comment to skip verification
            // die("Ты гребаный хакер");
            // $this->responseCode = 401; // HTTP Unauthorized
        }
       
        //  /api/configuration/...
        if(!empty($parametry[0]))   
        {
            switch($parametry[0])
            {
                //  /api/configuration/get
                case("get"):  
                    // verify
                    if(!isset($_GET['mainUnitSN']) || empty($_GET['mainUnitSN']))
                    {
                        $this->data = "ERROR - bad request format- ".file_get_contents('php://input');
                        $this->responseCode = 400; // HTTP bad request
                        break;
                    }

                    $mainUnitSN = $_GET['mainUnitSN'];
                    $this->data = AkvaManager::GetFullSystemConfig($mainUnitSN);
                    break;
                
                default:
                        $this->data = "ERROR - bad request format";
                        $this->responseCode = 400; // HTTP bad request
                break;
            }
        }
        else //  /api/configuration
        {
            $this->data = "ERROR - bad request format";
            $this->responseCode = 400; // HTTP bad request
        }

        


    }
}
