<?php

class BackController extends Controller
{
    public static $loginRequire = false;

    public function zpracuj($parametry)
    {
       $this->presmeruj($_SESSION["lastRequest"],true);
    }
}