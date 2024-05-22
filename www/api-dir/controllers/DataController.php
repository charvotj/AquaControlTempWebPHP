<?php

class DataController extends Controller
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
       
        //  /api/data/...
        if(!empty($parametry[0]))   
        {
            switch($parametry[0])
            {
                //  /api/data/add
                case("add"):  
                    $reqData = ApiManager::parseJSONRequest();
                    // verify
                    if(!isset($reqData['mainUnitSN']) || !isset($reqData['sensors']))
                    {
                        $this->data = "ERROR - bad request format- ".file_get_contents('php://input');
                        $this->responseCode = 400; // HTTP bad request
                        break;
                    }

                    if(count($reqData['sensors'])>0)
                    {
                        foreach ($reqData['sensors'] as $index => $sensor) 
                        {
                            // verify
                            if(!isset($sensor['SN']) || !isset($sensor['nodeType']) || !isset($sensor['dataType']) || !isset($sensor['value']))
                                continue;

                            // proccess
                            if(!AkvaManager::AddModuleIfNotAdded($reqData['mainUnitSN'],$sensor['SN'],$sensor['nodeType']) ||
                               !AkvaManager::AddModuleData($sensor['SN'],$sensor['dataType'],$sensor['value']))
                            {
                                $this->data = "ERROR - proccesing sensor data";
                                $this->responseCode = 555; // custom error code
                                break;
                            }
                        }
                    }
                    $this->data = "OK";
                    break;
                //  /api/data/view
                case("view"):  
                    if(isset($_GET["moduleId"]) && isset($_GET["dataType"]))  
                    {
                        $data = AkvaManager::GetModuleData($_GET["moduleId"],$_GET["dataType"]);
                        if(is_array($data) && count($data) > 0)
                        {
                            $filteredData = [];
                            foreach ($data as $entry) {
                                // Check if the entry contains the named keys 'time' and 'value'
                                if (isset($entry['time']) && isset($entry['value'])) {
                                    // Create a new associative array with only the named keys
                                    $filteredEntry = [
                                        'x' => $entry['time'],
                                        'y' => $entry['value']
                                    ];
                                    // Add the filtered entry to the new array
                                    $filteredData[] = $filteredEntry;
                                }
                            }
                            $this->data = $filteredData;
                        }
                        else 
                        {
                            $this->data = "Error getting module data";
                            $this->responseCode = 555;
                        }
                    }
                    else                        
                    {
                        $this->data = "ERROR - bad request format";
                        $this->responseCode = 400; // HTTP bad request
                    }                
                    break;

                
                default:
                        $this->data = "ERROR - bad request format";
                        $this->responseCode = 400; // HTTP bad request
                break;
            }
        }
        else //  /api/data
        {
            $this->data = "ERROR - bad request format";
            $this->responseCode = 400; // HTTP bad request
        }

        


    }
}
