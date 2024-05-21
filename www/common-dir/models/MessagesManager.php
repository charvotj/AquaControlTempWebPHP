<?php 

class MessagesManager
{
    public $userId=0;
    
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function GetReceavedMessages()
    {
        $sql = "SELECT user_message.ID, users.prezdivka, CONCAT(users.jmeno,' ',users.prijmeni) as jmeno, `header`, `body`, `time`, `readed` 
        FROM `user_message` 
        INNER JOIN users 
        ON user_message.fromId=users.ID
        
        WHERE toId=? and toDeleted is null
        ORDER BY user_message.time DESC";
        $messages = Db::dotazVsechny($sql,array($this->userId));
        return $messages;
    }
    public function GetSentMessages()
    {
        $sql = "SELECT user_message.ID, users.prezdivka, CONCAT(users.jmeno,' ',users.prijmeni) as jmeno, `header`, `body`, `time`, `readed` 
        FROM `user_message` 
        INNER JOIN users 
        ON user_message.toId=users.ID
        
        WHERE fromId=? and fromDeleted is null
        ORDER BY user_message.time DESC";
        $messages = Db::dotazVsechny($sql,array($this->userId));
        return $messages;
    }

    public static function GetUnreadCount($userId)
    {
        $sql = "SELECT COUNT(*) as pocet FROM user_message
        WHERE toId=? AND readed=0 AND toDeleted is NULL 
        GROUP BY toId";
        return Db::dotazJeden($sql,array($userId))["pocet"];
    }

    public function GetCurrentMessage($messageId)
    {
        $sql="SELECT user_message.*,
        toUser.prezdivka AS toPrezdivka, CONCAT(toUser.jmeno, ' ', toUser.prijmeni) AS toJmeno,
        fromUser.prezdivka AS fromPrezdivka, CONCAT(fromUser.jmeno, ' ', fromUser.prijmeni) AS fromJmeno
    FROM user_message
    INNER JOIN users AS toUser
        ON user_message.toId = toUser.ID
    INNER JOIN users AS fromUser
        ON user_message.fromId = fromUser.ID
    WHERE user_message.ID = ? AND(fromId = ? OR toId = ?)";
         return Db::dotazJeden($sql,array($messageId,$this->userId, $this->userId));      
       //-1 = chyba
       //null = nenalezen vyhovujici zaznam
    }
    
    public function SendMessage($toId,$header,$body)
    {
        $sql="INSERT INTO `user_message`(`fromId`, `toId`, `header`, `body`) VALUES (?,?,?,?)";
        if(Db::dotazOld($sql,array($this->userId,$toId,$header,$body))>0)
            return true;
        else return false;
    }
    
    public function MarkAsRead($messageId)
    {
       $sql="UPDATE `user_message` SET `readed`=1 WHERE `ID`=?";
       return Db::dotazOld($sql,array($messageId));
    }
    
    function DeleteMessage($messageId,$isReceived = false)//pokud chci smazat prijatou, potom true
    {
        if($isReceived)
        {
            $sql="UPDATE `user_message` SET toDeleted=CURRENT_TIMESTAMP WHERE `ID`=? AND toId=?";
        }
        else 
        {
            $sql="UPDATE `user_message` SET fromDeleted=CURRENT_TIMESTAMP WHERE `ID`=? AND fromId=?";
        }        
       return (Db::dotazOld($sql,array($messageId,$this->userId))>0);
    }

    
}