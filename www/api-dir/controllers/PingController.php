<?php

class PingController extends Controller
{
    public function zpracuj($parametry) // parametry obsahují kusy url, viz směrovač
    {
        // nekontroluje se verifikace, pouze vraci OK response
        // nastaveni sablony
        $this->pohled = null; // nebude se vypisovat, není třeba nastavovat
        $this->responseCode = 200; // HTTP OK
        $this->data = "OK";
    }
}
