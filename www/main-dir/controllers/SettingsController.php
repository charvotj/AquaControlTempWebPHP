<?php

class SettingsController extends Controller
{
    public function zpracuj($parametry)
    {        
        $this->hlavicka['titulek'] = 'Nastavení';       
        
        $nastaveni = new Settings;

        $this->pohled = "nastaveni";
               
        if(isset($_POST["hesloBtn"]))
        {
            
            $heslo_old = $_POST["heslo_old"];
            $heslo = $_POST["heslo"];
            $heslo02 = $_POST["heslo02"];
            if($heslo == $heslo02)
            {
                $lm = new LoginMaster();
                if($lm->OverHeslo($_SESSION["login"],$heslo_old))
                {
                    if($nastaveni->ZmenHeslo($heslo))
                        $this->data["infoMessage"]="Úspěšně změněno";
                    else
                        $this->data["warnMessage"]="Nastala chyba";
                }
                else
                {
                    $this->data["warnMessage"] = "Špatně zadané staré heslo";
                }
                unset($lm);

            }
            else
            {
                $this->data["warnMessage"] = "Zadaná hesla se neshodují";
            }
        }

      
        else if(isset($_POST["loginBtn"]))
        {           
          if(!empty($_POST["login"]))
          {
              $newLogin = $_POST["login"];
              if($nastaveni->CheckLogin($newLogin))
              {
                  if($nastaveni->ZmenLogin($newLogin))
                  {
                    $this->data["infoMessage"]="Úspěšně změněno";
                    $_SESSION['login']=$newLogin;
                  }
                  else
                    $this->data["warnMessage"]="Nastala chyba";
              }
              else
              $this->data["warnMessage"]="Zadaný login už někdo používá";               
          }
          else
            $this->data["warnMessage"]="Login nesmí být prázdný";
        }

        else if(isset($_POST["prezdivkaBtn"]))
        {           
            if(!empty($_POST["prezdivka"]))
            {
                $newPrezdivka = $_POST["prezdivka"];
                    if($nastaveni->ZmenPrezdivku($newPrezdivka))
                    {
                    $this->data["infoMessage"]="Úspěšně změněno";
                    $_SESSION['prezdivka']=$newPrezdivka;
                    }
                    else
                    $this->data["warnMessage"]="Nastala chyba";
            
            }
            else
                $this->data["warnMessage"]="Přezdívka nesmí být prázdná";
        }


        
        
       
        
    }
}