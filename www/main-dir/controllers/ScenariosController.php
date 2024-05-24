<?php

class ScenariosController extends Controller
{
    public function zpracuj($parametry)
    {        
        $this->hlavicka['titulek'] = 'Scénáře';       

       
               
        

         //  /main/scenarios/...
         if(!empty($parametry[0]))   
         {
             switch($parametry[0])
             {
                 //  /main/scenarios/relays
                 case("relays"):
                    $this->pohled = "akva-scenare-relays";
                     
                     break;
                 //  /main/scenarios/view
                 case("view"):  
                    $this->pohled = "akva-scenare-prehled";      
                     break;
                //  /main/scenarios/settings
                 case("settings"):  
                    $this->pohled = "akva-scenare-nastaveni";
                    if(isset($_POST['moduleId']) && isset($_POST['nodeType']))
                    AkvaManager::SaveModuleConfigFromPost();
                    break;
 
                 
                 default:
                         $this->data = "ERROR - bad request format";
                         $this->responseCode = 400; // HTTP bad request
                 break;
             }
            $this->data['userModules'] = AkvaManager::GetUserModules($_SESSION["userId"]);
         }
         else //  /main/scenarios
         {
            $this->pohled = "akva-scenare-menu";
         }
         
        
    }
}