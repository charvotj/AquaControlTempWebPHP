<?php

class LoginController extends Controller
{
    public static $loginRequire = false;

    public function zpracuj($parametry)
    {
        $this->hlavicka['titulek'] = 'Přihlášení';

        
        if(isset($_GET["require"]))
        {
            $this->data["warnMessage"] = "Tato stránka vyžaduje přihlášení.";
        }
        if (isset($_POST["login"])) {
                $loginMaster = new LoginMaster;
                if ($loginMaster->OverHeslo($_POST["login"], $_POST["heslo"])) 
                {
                    $loginMaster->Prihlasit($_POST["login"]);
                        
                    $message = (($_SESSION["genderMale"]==1)? "přihlášen":"přihlášena");
                    Webhook::SendToDiscord($message,$_SESSION["jmeno"]);

                    if(!empty($_GET["url"]))
                    {
                        $this->presmeruj(urldecode($_GET["url"]),true); 
                    }
                    else
                        $this->presmeruj("menu");
                   
            
                } else
                    $this->data["warnMessage"] = "Špatné přihlašovací údaje";
            }
        $this->pohled = "login";
    }
}
