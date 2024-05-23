<?php

class Error404Controller extends Controller
{
    public static $loginRequire = false;

    public function zpracuj($parametry)
    {
        //hlavicka pozadavku
        //header("HTTP/1.0 404 Not Found");  //--domluvit se s Honzou
        //hlavicka stranky
        $this->hlavicka['titulek'] = 'Chyba 404';
        // nastaveni sablony
        $this->pohled = 'error404';

        $this->responseCode = 404; // HTTP Page Not Found

    }
}