<?php

class LogoutController extends Controller
{
    public static $loginRequire = false;

    public function zpracuj($parametry)
    {        
       $spravce_diskuze = new LoginMaster;
       $spravce_diskuze->Odhlasit();

       $this->presmeruj("login");
        
    }

    
}