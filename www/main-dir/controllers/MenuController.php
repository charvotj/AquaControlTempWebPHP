<?php

class MenuController extends Controller
{
   
    public function zpracuj($parametry)
    {
        $this->hlavicka['titulek'] = "Aktivity";
        $this->pohled = "menu";
    }
}