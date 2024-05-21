<?php

class PhpInfoController extends Controller
{
   
    public function zpracuj($parametry)
    {
        $this->hlavicka['titulek'] = "PHP info";
        $this->pohled = "phpinfo";
    }
}