<?php

class Webhook
{
        public static function SendToDiscord($message, $username = "Bot",$type="login")
        {
            switch($type)
            {
                case "login":
                    $url = 'https://discord.com/api/webhooks/836308006881722440/SHmshhgymLgoPid21fLyhpvtwG2Wtjk8t5zMznd_-0hGZuIYNleBPAkosBWpZ8UXjrd2';
                    break;
                case "error":
                    $url = 'https://discord.com/api/webhooks/884226409340489809/iND5AEPdjymNRyPsxlzH2Hp4VGXTybeuhDs5R9fZ_5e9JMAJKM04bA5JZZMTS-0YNSjv';
            }

            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $message.=' \n ['.$currentUrl.']';

            $postFields = array('content' => $message,'username' =>$username);
            self::sendWebook($url,$postFields,'multipart/form-data');
        }

        public static function SendToTeams($messsage,$message2,$callbackUrl)
        {
            $url = 'https://cedivoda.webhook.office.com/webhookb2/5015a229-4684-46ae-a5e0-8db3f87fbd69@65d6eb94-4a09-42a9-9d63-6b4d900d6fcc/IncomingWebhook/0ac5b2c995204df59afa48afb8f421c0/4356c876-0dba-487b-9ecc-eef9740bac62';
            $postFields='{
                "@type": "MessageCard",
                "@context": "http://schema.org/extensions",
                "themeColor": "0076D7",
                "summary": "'.$messsage.'",
                "sections": [{
                    "activityTitle": "'.$messsage.'",
                    "activitySubtitle": "'.$message2.'",
            
                    "potentialAction": [{
                        "@type": "OpenUri",
                        "name": "Zobrazit",
                        "targets": [{
                            "os": "default",
                            "uri": "'.$callbackUrl.'"
                            
                        }]
                       
                        
                    }]
                }]
            }';
            self::sendWebook($url,$postFields,'application/json');

        }
        
        private static function sendWebook(string $url, $postFields, string $contentType)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: '.$contentType
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //die( $response);
        }
}