<?php

class LoginMaster
{
    public function OverHeslo($login, $heslo)
    {
        $sql = "SELECT heslo FROM users WHERE login= ?";
        $result = Db::dotazJeden($sql, array($login));

        // if ($heslo == $result['heslo']) {
        //     return true;
        // }
        if (password_verify($heslo, $result["heslo"])) {
            return true;
        }


        return false;
    }

    /* public function OverPristup($login)
    {
        $sql = "SELECT ID FROM user_groups WHERE `groupId`IN(SELECT ID FROM web_sites where name=?) AND `userId` IN (SELECT ID FROM users where login=?)";
        $result = Db::dotazJeden($sql, array(ROOT,$login));

        if (empty($result))
            return false;
        else return true;
    } */

    public function Prihlasit($login)
    {
        $sql = "SELECT users.ID,login,CONCAT(jmeno, ' ', prijmeni) AS CeleJmeno,prezdivka,male
                FROM users
                WHERE login = ?";
        $result = Db::dotazJeden($sql, array($login));

        $_SESSION["logged"] = true;
        $_SESSION["login"] = $result["login"];
        $_SESSION["jmeno"] = $result["CeleJmeno"];
        $_SESSION["userId"] = $result["ID"];
        $_SESSION["genderMale"] = $result["male"];
        $_SESSION["prezdivka"] = $result["prezdivka"];
        
        $_SESSION["userRoles"] = array();
    
        $uRoles = UserManager::GetUserRoles($result["ID"]);
        foreach($uRoles as $role)
        {
            array_push($_SESSION["userRoles"],$role["roleName"]);
        }

        $_SESSION["userGroups"] = array();

        $uGroups = UserManager::GetUserGroups($result["ID"]);
        foreach($uGroups as $group)
        {
            array_push($_SESSION["userGroups"],$group["groupName"]);
        }
       
    }

    public function Odhlasit()
    {
        session_destroy();
    }
}
