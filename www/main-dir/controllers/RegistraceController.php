<?php

class RegistraceController extends Controller
{
    public static $loginRequire = false;
    public function zpracuj($parametry)
    {
        $this->hlavicka['titulek'] = 'Registrace';

        $warnMessage = "";
        
        if (isset($_POST["regButton"]))
            {
                $regMaster = new RegistrationMaster;
               
                if(empty($_POST["jmeno"]) || empty($_POST["prijmeni"]) || empty($_POST["login"])|| empty($_POST["heslo"]))
                    $warnMessage = "Musíš zadat všechny povinné údaje...";
                else if(!$regMaster->OverUdaje($_POST["login"],$_POST["email"]))//jsou duplicitni udaje
                    $warnMessage= "Zadaný login nebo e-mail už někdo používá";
                else if($_POST["heslo"]!=$_POST["heslo2"])
                    $warnMessage = "Zadaná hesla se neshodují";
                else // vse OK
                {
                    //zde pridat volitelne udaje
                    $prezdivka = (empty($_POST["prezdivka"]))?null:$_POST["prezdivka"];
                    $email = (empty($_POST["email"]))?null:$_POST["email"];

                    $newUserId = $regMaster->Zaregistruj($_POST["jmeno"],$_POST["prijmeni"],$email,$_POST["login"],$_POST["heslo"],$prezdivka);
                    if($newUserId != false)
                    {
                        $regMaster->SendConfirmMessage($newUserId);
                        $this->presmeruj("login?infoMessage=regOk");
                    }
                    else{
                        $warnMessage = "Při registraci bohužel nastala chyba :(";
                    }
                    
                }
                
               
            }
            $this->data["warnMessage"] = $warnMessage;
        $this->pohled = "registrace";
    }
}
