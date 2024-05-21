<?php

class MessagesController extends Controller
{
    public function __construct()
    {
            
    }
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
                'titulek' => 'Soukrové zprávy',
                'klicova_slova' => 'norobot, hranicar, messages',
                'popis' => 'Soukromé zprávy našeho webu.'
        );
        $messManager = new MessagesManager($_SESSION["userId"]);
        if(empty($parametry[0]))
        {
            $this->data["pocet"]=MessagesManager::GetUnreadCount($_SESSION["userId"]);
            $this->pohled = 'messages';
        }
        else if($parametry[0]=="received")
        {
            if(isset($_GET["del"]))
            {
                $messManager->DeleteMessage($_GET["del"],true);//$isReceived = true
                $this->presmeruj("messages/received"); // kvuli odstraneni parametru z url
            }
            $this->data["messages"] = $messManager->GetReceavedMessages();
            $this->pohled = 'messages-received';
        }
        else if($parametry[0]=="sent") // odeslane
        {
            if(isset($_GET["del"]))
            {
                $messManager->DeleteMessage($_GET["del"]);//$isReceived = false
                $this->presmeruj("messages/received"); // kvuli odstraneni parametru z url
            }
            $this->data["messages"] = $messManager->GetSentMessages();
            $this->pohled = 'messages-sent';
        }
        else if($parametry[0]=="new")
        {
            if(isset($_GET['reply']))
            {
                $message = $messManager->GetCurrentMessage($_GET["reply"]);
                $message["header"] = "RE: ".$message["header"];
                $message["body"] = "\n\n__________________________\nPůvodní zpráva: \n".$message["body"];
                $this->data["message"]=$message;
                $this->data["back"]="received";// abych vedel, kam se vratit
            }
            $this->pohled="messages-new";
            $this->data["back"]="";
            $this->data["users"]=UserManager::GetAllUsers(ROOT);
        }
        else if($parametry[0]=="send") //odeslat
        {
            if(isset($_POST['toId']) && isset($_POST['body']))
            {
                if(empty($_POST["header"]))
                    $header = "<i>Bez předmětu</i>";
                else
                    $header = $_POST["header"];
                if($messManager->SendMessage($_POST["toId"],$header,$_POST["body"]))                
                {
                    $this->presmeruj("messages?");
                }
                else
                {
                    $this->presmeruj("messages/new");
                }
                    
            }
        }
        else if($parametry[0]=="read"){
            if(isset($_GET["id"]))
            {
                $message = $messManager->GetCurrentMessage($_GET["id"]);
                if($message == -1)
                $this->presmeruj("messages?message=nastala_chyba_sry");
                else if($message !=null)
                {
                    $this->pohled="messages-read";
                    $this->data["message"]=$message;

                    if($message["toId"]==$_SESSION["userId"])
                    {   // pokud jde o prichozi zpravu, tak oznacim jako prectenou
                        $messManager->MarkAsRead($_GET["id"]);
                        $this->data["back"]="received";// abych vedel, kam se vratit
                    }
                    else
                        $this->data["back"]="sent";
                }
               
            }
        }
        
            

    }
}

