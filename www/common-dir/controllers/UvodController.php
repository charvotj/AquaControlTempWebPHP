<?php

class UvodController extends Controller
{
    public static $loginRequire = false;
    
    public function zpracuj($parametry)
    {
        
        $this->hlavicka['titulek'] = 'Úvod';
        // nastaveni sablony
        $this->pohled = 'uvod';

    }
}