<?php

class PingController extends Controller
{
    public function zpracuj($parametry) // parametry obsahují kusy url, viz směrovač
    {
        // nekontroluje se verifikace, pouze vraci OK response nebo verzi konfigurace
        // nastaveni sablony
        $this->pohled = null; // nebude se vypisovat, není třeba nastavovat

        if(isset($_GET['mainUnitSN']) && !empty($_GET['mainUnitSN']))
        {
            $mainUnitSN = $_GET['mainUnitSN'];
            $systemId = AkvaManager::GetSystemId($mainUnitSN);
            if($systemId < 0)
            {
                if(!AkvaManager::AddSystem($mainUnitSN))
                {
                    $this->responseCode = 555; // HTTP ERROR
                    $this->data = "ERROR adding module...";
                    return;
                }
                $systemId = AkvaManager::GetSystemId($mainUnitSN);
                if($systemId < 0)
                {
                    $this->responseCode = 555; // HTTP ERROR
                    $this->data = "ERROR weird stuff...";
                    return;
                }
            }
            $configVersion = AkvaManager::GetSystemConfigVersion($systemId);
            if($configVersion < 0)
            {
                $this->responseCode = 555; // HTTP ERROR
                $this->data = "ERROR weird stuff...";
            }
            else 
            {
                $this->data = array('lastConfigVersion' => $configVersion);
            }


        }
        else
        {
            // just OK on empty request
            $this->data = "OK";

        }

        $this->responseCode = 200; // HTTP OK
    }
}
