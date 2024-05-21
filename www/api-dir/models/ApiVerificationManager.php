<?php 

 class ApiVerificationManager 
{
    public static function checkDigest($digest,$unix)
    {

        //$actualUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // traefik meni https na http a pak nesedi digest, proto radek zmenen
        $actualUrl = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $hashData = AKVA_API_KEY.$actualUrl.$unix;

        $digestCheck = hash("sha512",$hashData);

        //echo "dig: $digest\ndigCheck: $digestCheck";

        if($digestCheck==$digest)
        {
            // check timestamp if it's actual
            $difference = abs(floor($unix/1000) - time());
            // echo "\ndiff: $difference";
            if($difference>300) // > 300s clearly a russian hacker
                return false;
            else
                return true;
        }
    }
}
