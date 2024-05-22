<?php 

class AkvaManager
{
    public static function AddSystem($mainUnitSN)
    {
        $sql = "INSERT INTO `akva_systems`(`mainUnitSN`) VALUES (?)";
        if(Db::dotazOld($sql,array($mainUnitSN))>0)
            return true;
        else return false;
    }

    public static function AddModule($mainUnitSN,$nodeSN,$nodeType)
    {
        // check if system exists
        $systemId = self::GetSystemId($mainUnitSN);
        if($systemId <= 0) // check if exist in database
        {
            if(!self::AddSystem($mainUnitSN)) // try to add new system if not exists
                return false;
            // check if exists after adding
            $systemId = self::GetSystemId($mainUnitSN);
            if($systemId <= 0)
                return false;
        }
        // add module
        $sql = "INSERT INTO `akva_modules`(`systemId`, `nodeType`, `nodeSN`) VALUES (?,?,?)";
        if(Db::dotazOld($sql,array($systemId,$nodeType,$nodeSN))>0)
            return true;
        else return false;
    }

    public static function AddModuleIfNotAdded($mainUnitSN,$nodeSN,$nodeType)
    {
        // check if module exists
        $moduleId = self::GetModuleId($nodeSN);
        if($moduleId <= 0)
            return self::AddModule($mainUnitSN,$nodeSN,$nodeType);
        return true; // just return false if already exists
    }

    public static function AddModuleData($nodeSN,$dataType,$dataValue)
    {
        $moduleId = self::GetModuleId($nodeSN);
        if($moduleId <= 0)
            return false;

        $sql = "INSERT INTO `akva_module_data`(`moduleID`, `dataType`, `dataValue`) VALUES (?,?,?)";
        if(Db::dotazOld($sql,array($moduleId,$dataType,$dataValue))>0)
            return true;
        else return false;
    }

    public static function GetModuleData($moduleId,$dataType,$limit =100)
    {
        $sql = "SELECT * FROM
                (
                    SELECT created as time,dataValue as value 
                    FROM `akva_module_data` 
                    WHERE `moduleID`=? and `dataType`=?
                    ORDER BY time DESC LIMIT ?
                ) AS sub
                ORDER BY sub.time ASC;
        ";
        return Db::dotazVsechny($sql,array($moduleId,$dataType,$limit));
    }

    public static function GetSystemId($mainUnitSN)
    {
        $sql = "SELECT akva_systems.ID
        FROM `akva_systems`         
        WHERE mainUnitSN=?";
        $system = Db::dotazJeden($sql,array($mainUnitSN));
        if(!empty($system['ID']))
            return $system['ID'];
        return -1;
    }

    public static function GetModuleId($nodeSN)
    {
        $sql = "SELECT akva_modules.ID
        FROM `akva_modules`         
        WHERE nodeSN=?";
        $module = Db::dotazJeden($sql,array($nodeSN));
        if(!empty($module['ID']))
            return $module['ID'];
        return -1;
    }

    public static function GetUserModules($userId)
    {
        $sql = "SELECT akva_modules.*, akva_systems.mainUnitSN, akva_systems.systemCustomName
        FROM `akva_modules`
        LEFT JOIN akva_user_system
        ON akva_modules.systemId=akva_user_system.systemId 
        LEFT JOIN akva_systems
        ON akva_modules.systemId=akva_systems.ID

        WHERE akva_user_system.userId=?";
        return Db::dotazVsechny($sql,array($userId));
    }


    // old
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