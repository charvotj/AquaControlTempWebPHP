<?php

class Settings
{  
    public function ZmenHeslo($noveHeslo)
    {
        $noveHeslo = password_hash($noveHeslo, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET heslo=? WHERE ID=?";
        if(Db::dotazOld($sql,array($noveHeslo, $_SESSION["userId"]))>0)
            return true;
        else
            return false;
    }
    

    public function CheckLogin($newLogin)
    {
        if((Db::dotazJeden("SELECT ID FROM users WHERE login=?",array($newLogin)))>0)
        {
            return false;
        }
        else
            return true;
    }

    public function ZmenLogin($newLogin)
    {
        $sql = "UPDATE users SET login=? WHERE ID=?";
        if(Db::dotazOld($sql,array($newLogin, $_SESSION["userId"]))>0)
            return true;
        else
            return false;
    }

    public function ZmenPrezdivku($newPrezdivka)
    {
        $sql = "UPDATE users SET prezdivka=? WHERE ID=?";
        if(Db::dotazOld($sql,array($newPrezdivka, $_SESSION["userId"]))>0)
            return true;
        else
            return false;
    }

    public function ChangeSetting($key,$value,$userId = null,$groupId = 0)
    {
        // ve výchozím parametru musí být konstanta, ne $_SESSION
        $userId =($userId==null)?$_SESSION["userId"]:$userId;
       
        $sqlUpd = "UPDATE `user_setting` SET `settingValue`=?, updateBool=not updateBool WHERE userId=? and groupId=? and settingKey=?";
        $sqlIns = "INSERT INTO `user_setting`(`userId`, `groupId`, `settingKey`, `settingValue`) VALUES (?,?,?,?)";

        $response=Db::dotazOld($sqlUpd,array($value,$userId,$groupId,$key));
        if($response>0)
            return true;
        else if(Db::dotazOld($sqlIns,array($userId,$groupId,$key,$value))>0)
            return true;
        else
            return false;

    }

    public function GetSettingValue($key,$userId = null,$groupId = 0)
    {
        // ve výchozím parametru musí být konstanta, ne $_SESSION
        $userId =($userId==null)?$_SESSION["userId"]:$userId;
        
        $sql = "SELECT * from user_setting where userId=? and settingKey=? and groupId=?";
        return Db::dotazJeden($sql,array($userId,$key,$groupId))["settingValue"];
    }
}
