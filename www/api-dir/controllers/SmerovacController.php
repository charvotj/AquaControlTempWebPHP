<?php

class SmerovacController extends Controller
{

        protected $controller;

        public function zpracuj($parametry)//0-url,
        {
                $naparsovanaURL = $this->parsujURL($parametry[0]);
               

                if (empty($naparsovanaURL[0]))
                        $this->presmeruj('Error404');

                $tridaKontroleru = $this->pomlckyDoVelblouda(array_shift($naparsovanaURL)) . 'Controller'; 
                if(file_exists('common-dir/controllers/' . $tridaKontroleru . '.php') || file_exists(ROOT.'-dir/controllers/' . $tridaKontroleru . '.php'))
                        $this->controller = new $tridaKontroleru;
                else
                        //echo "FAIL";
                        $this->presmeruj('Error404');

                // TODO upravit n verifikaci tokenem
                // if($this->controller::$loginRequire && !$_SESSION["logged"])
                // {                             
                //         $this->forceUserLogin();
                // }

                if(count($this->controller::$allowedRoles)>0)
                {
                        // pokud neexistuje průnik povolených rolí, tak uživatel nemá přístup
                        if(count(array_intersect($_SESSION["userRoles"],$this->controller::$allowedRoles))==0)
                        {
                                $this->presmeruj("sem_nesmis_jasny?");
                        }
                }
                
                $this->controller->zpracuj($naparsovanaURL);

                           
                
                
                // Nastavení hlavní šablony
                $this->pohled = 'api-sablona';
        }

        
}