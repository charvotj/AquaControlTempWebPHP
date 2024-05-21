<?php

class AkvaStateController extends Controller
{
    public function zpracuj($parametry)
    {        
        $this->hlavicka['titulek'] = 'Stav akvária';       
        
        $nastaveni = new Settings;

        $this->pohled = "stav-akvaria";
               
        
    }
}