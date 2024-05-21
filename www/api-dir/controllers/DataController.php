<?php

class UsersController extends Controller
{
    public function zpracuj($parametry) // parametry obsahují kusy url, viz směrovač
    {
        // nastaveni sablony
        $this->pohled = null; // nebude se vypisovat, není třeba nastavovat
        
        // kontrola verifikace
        if(empty($_SERVER["HTTP_DISCORD_DIGEST"]) || empty($_SERVER["HTTP_UNIX"]) || !$this->checkDigest($_SERVER["HTTP_DISCORD_DIGEST"], $_SERVER["HTTP_UNIX"]))
        {
            die("Ты гребаный хакер");
        }
       
        //  /api/users/...
        if(!empty($parametry[0]))   
        {
            switch($parametry[0])
            {
                
                case("add-access"):  
                    //  /api/users/add-access
                    if(isset($_POST["userId"]) && isset($_POST["groupName"]))  
                    {
                        $this->data = UserManager::AddUserAcces($_POST["userId"],$_POST["groupName"]);
                    }
                    else                        
                    {
                        $this->data = "ERROR - bad request format";
                    }                
                    break;

                case("remove-access"):  
                    //  /api/users/add-access
                    if(isset($_POST["userId"]) && isset($_POST["groupName"]))  
                    {
                        $this->data = UserManager::RemoveUserAcces($_POST["userId"],$_POST["groupName"]);
                    }
                    else                        
                    {
                        $this->data = "ERROR - bad request format";
                    }                
                    break;

                
                default:
                $this->data = "Daná akce není definována";
                break;
            }
        }
        else //  /api/users
        {
            if(isset($_POST["groupName"]))  
            {
                if($_POST["groupName"]=="all")
                    $this->data = UserManager::GetUsersAdmin();
                else
                {
                    $response = Db::dotazJeden("SELECT ID from `groups` where name=?",array($_POST["groupName"]));
                    if($response>0)
                        $groupId=$response["ID"];
                    else
                        return $this->data = "ERROR - Bad group nameee";
                    $this->data = UserManager::GetUsersAdmin(0,$groupId);
                }
            }
            else                        
            {
                $this->data = "ERROR - bad request format";
            }   
        }

        


    }

    private function checkDigest($digest,$unix)
    {
        $discordApiKey = "AkU12hVfBOSsHZMFNkIAcS2UwW0KQTOZYzGX8gjORGrRYTJ8quiYKCG7eklWlxLq";

        //$actualUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // traefik meni https na http a pak nesedi digest, proto radek zmenen
        $actualUrl = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $hashData = $discordApiKey.$actualUrl.$unix;

        $digestCheck = hash("sha512",$hashData);

        //echo "dig: $digest\ndigCheck: $digestCheck";

        if($digestCheck==$digest)
        {
            // check timestamp if it's actual
            $difference = abs(floor($unix/1000) - time());
            // echo "\ndiff: $difference";
            if($difference>300) // > 300s clearly a russian hacker
                return false;
            else
                return true;
        }
    }
}
