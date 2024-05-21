<?php

class DiskuzeController extends Controller
{
    public static $menuTitle = "Diskuze";
    public static $footerOn = false;
    public function zpracuj($parametry)
    {        
        $this->hlavicka['titulek'] = 'Diskuze';       
        
        $spravce_diskuze = new SpravceDiskuze();
        $sett = new Settings();
        if(isset($_POST["prispevekID"]))
        {
            $spravce_diskuze->PridejLike($_POST["prispevekID"],$parametry[0], $_POST["state"], $_SESSION["userId"],ROOT);
        }
        else if(isset($_POST["text"]))
        {
            if($_SESSION["lastToken"] != $_POST["token"])
                    $this->presmeruj("diskuze/$parametry[0]");
            $spravce_diskuze->PridejPrispevek($_SESSION["userId"],$_POST["text"],$parametry[0],ROOT);
            if(isset($_POST["submitOnEnter"]))
                $sett->ChangeSetting("submitOnEnter",true);
            else
                $sett->ChangeSetting("submitOnEnter",false);
        }
        else if(isset($_POST["themeName"]))
        {
            $lastId = $spravce_diskuze->PridejTema($_POST["themeName"],ROOT,$_POST["roleId"]);
            $this->presmeruj("diskuze/$lastId");
            
        }

        /* TEMATA */
        if(empty($parametry[0]))
        {         
            $this->data["themeGroups"]=$this->pripravTemata($spravce_diskuze);
            $this->data["userRoles"]=UserManager::GetUserRoles($_SESSION["userId"],ROOT);

            $this->pohled = 'diskuzeTemata'; 
        }
        else
        {
            $themeID = $parametry[0];
            $prispevky = $spravce_diskuze->VratPrispevky($themeID,ROOT);
            $tema = $spravce_diskuze->VratJmenoTematu($themeID); 
            $spravce_diskuze->MarkThemeAsRead($themeID);  

            $this->data["themeName"] = $tema;
            $this->data["prispevky"]=$prispevky;
            $this->data["themeGroups"]=$this->pripravTemata($spravce_diskuze);
            $this->pohled = 'diskuzePrispevky'; 
            $token = bin2hex(openssl_random_pseudo_bytes(12));
            $this->data["token"]=$token;
            $_SESSION["lastToken"] = $token;
            $this->data["submitOnEnter"] = $sett->GetSettingValue("submitOnEnter");
        }
        
    }

    private function pripravTemata($spravce_diskuze)
    {
        $themeGroups = array();
        $result = $spravce_diskuze->VratTemata($_SESSION["userId"],ROOT);
        if(count($result)>0)
        {
            $result[0]["roleName"] = "Společné";
            array_push($themeGroups, $result); // spolecne
        }
            
        foreach($_SESSION["userRoles"] as $roleName) // podle roli
        {
            $result = $spravce_diskuze->VratTemata($_SESSION["userId"],ROOT,$roleName);
            if(count($result)>0)
                array_push($themeGroups, $result); 
        }
        return $themeGroups;
    }

    
}