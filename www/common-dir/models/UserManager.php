<?php 

class UserManager
{
    public static function GetAllUsers($groupName)
    {
        $sql="SELECT u.ID,u.prezdivka, CONCAT(u.jmeno,' ',u.prijmeni)as jmeno FROM `user_groups` as ug
        INNER JOIN users as u
        on u.ID = ug.userId
        left join `groups` as g
            on g.ID = ug.groupId
        WHERE g.name=?";
        return Db::dotazVsechny($sql, array($groupName));
    }
    public static function GetAllBankUsers($groupName)
    {
        $sql="SELECT u.ID,u.prezdivka, CONCAT(u.jmeno,' ',u.prijmeni)as jmeno, bank.accountNumber FROM `user_groups`
        INNER JOIN users as u
        	on u.ID = user_groups.userId
        INNER JOIN oddil_bank_account as bank
        	on u.ID = bank.userId        
        LEFT JOIN `groups` as g
            on g.ID = groupId
        WHERE g.name=?";
        return Db::dotazVsechny($sql, array($groupName));
    }
    /*  */
    public static function GetUsersAdmin($userId=0,$groupId=null)
    {
        if($groupId!=null)
        {
            $sql2 = "INNER JOIN user_groups as u_gr ON (u.ID=u_gr.userId and u_gr.groupId=$groupId)";
        }
        else{
            $sql2 = "";
        }
        $sql="SELECT u.ID, CONCAT(jmeno,' ',prijmeni) as jmeno,prezdivka,login,email,regDate,accountNumber,balance,
            GROUP_CONCAT(DISTINCT access.groupId) as `groups`, GROUP_CONCAT(DISTINCT usRole.roleId) as roles
        FROM `users` as u
        LEFT JOIN oddil_bank_account as bank
            on u.ID = bank.userId
        LEFT JOIN user_role as usRole
            on (u.ID = usRole.userId)
        LEFT JOIN user_groups as access
            on u.ID=access.userId
        $sql2
        WHERE (u.ID=? or 0=?) and u.active=true
        GROUP BY u.ID;";// ^ pokud chci jednoho, zadam ID, jinak plati 0=0 -> true
        return Db::dotazVsechny($sql, array($userId,$userId));


    }

    public static function GetUserRoles($userId)
    {
        $sql = "SELECT ur.*,wr.name as roleName from user_role as ur 
        left join web_role as wr 
            on ur.roleId=wr.ID 
        where userId=?";
        $result = Db::dotazVsechny($sql, array($userId));     
        return $result;   
    }

    public static function GetUserGroups($userId)
    {
        $sql = "SELECT g.ID, g.name as groupName FROM `user_groups` as ug
        left join `groups` as g
            on ug.groupId = g.ID
        WHERE userId=?";
        $result = Db::dotazVsechny($sql, array($userId));     
        return $result;   
    }

    public static function AddUserAcces(int $userId,string $groupName)
    {
        $response = Db::dotazJeden("SELECT ID from `groups` where name=?",array($groupName));
        if($response>0)
            $groupId=$response["ID"];
        else
            return "ERROR - Bad group name";


        $sql ="INSERT into user_groups (userId,groupId) VALUES (?,?)";
        $response = Db::dotaz($sql,array($userId,$groupId));
        if(!is_int($response))
        {
            $errorCode = $response->errorInfo[1];
            if($errorCode==1452)
                return "ERROR - user ID not exist";
            else if($errorCode==1062)
                return "ERROR - access is already added";
            else 
                return "ERROR - not očekávaná error";
        }
        return "OK - acces added for user ".$userId." to ".$groupName;
        
    }

    public static function RemoveUserAcces(int $userId,string $groupName)
    {
        $response = Db::dotazJeden("SELECT ID from `groups` where name=?",array($groupName));
        if($response>0)
            $groupId=$response["ID"];
        else
            return "ERROR - Bad group name";


        $sql ="DELETE FROM `user_groups` WHERE `userId`=? AND `groupId`=?";
        $response = Db::dotaz($sql,array($userId,$groupId));
        if(!is_int($response))
        {
            $errorCode = $response->errorInfo[1];
            return "ERROR - not očekávaná error";
        }
        else if($response==0)
            return "ERROR - user ID not exist or user already doesn't have access to ".$groupName;

        return "OK - acces removed for user ".$userId." to ".$groupName;
        
    }
}