<?php

class TestController extends Controller
{
    public static $loginRequire = false;

    public function zpracuj($parametry)
    {
       if($_SERVER['REQUEST_METHOD']=="POST")
       {
            $content = file_get_contents("php://input");
            $data = json_decode($content);
            Webhook::SendToDiscord($data[0]->jmeno.$_SERVER["HTTP_APP_TOKEN"]."nic","BOTa","error");
            print_r(apache_request_headers());
            die("OKi");
       }
       die("Nope");
    }
}