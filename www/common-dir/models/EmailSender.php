<?php

class EmailSender
{
        // Odešle email jako HTML, lze tedy používat základní HTML tagy a nové
        // řádky je třeba psát jako <br /> nebo používat odstavce. Kódování je
        // odladěno pro UTF-8.
        public static function odesli($komu, $predmet, $zprava, $od)
        {
                $hlavicka = "From: " . $od;
                $hlavicka .= "\nMIME-Version: 1.0\n";
                $hlavicka .= "Content-Type: text/html; charset=\"utf-8\"\n";
                return mb_send_mail($komu, $predmet, $zprava, $hlavicka);
        }

        public static function SendError($e)
        {
                //return self::odesli("jakub.charvot@centrum.cz", "ERROR ".$e->getCode(), $e, "watchdog@charvot.cz");
                Webhook::SendToDiscord($e,"ERROR ".$e->getCode(),"error");
        }

}