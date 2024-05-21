<?php

class RegistrationMaster
{
    public function OverUdaje($login, $email)
    {
        $sql = "SELECT ID FROM users WHERE (login= ? OR email= ?)";
        if(Db::dotazOld($sql, array($login,$email)) == 0)
        {
            return true;
        }

        return false;
    }

    public function Zaregistruj($jmeno, $prijmeni, $email, $login, $heslo,$prezdivka)
    {
        $sql = "INSERT INTO `users`(`jmeno`, `prijmeni`, `login`, `heslo`, `email`,prezdivka) VALUES (?,?,?,?,?,?)";
        if(Db::dotazOld($sql, array($jmeno,$prijmeni,$login,password_hash($heslo, PASSWORD_DEFAULT),$email,$prezdivka))>0)
            return Db::lastId();
        else
            return false;
    }
    public function SendConfirmMessage($userId)
    {
        $sql = "SELECT * FROM users where ID=?";
        $response = Db::dotazJeden($sql,array($userId));
        $m = "ID: ";
        $m .= $response["ID"];
        $m .= "\nJméno: ";
        $m .= $response["jmeno"];
        $m .= "\nPříjmení: ";
        $m .= $response["prijmeni"];
        $m .= "\nPřezdívka: ";
        $m .= $response["prezdivka"];
        $m .= "\nLogin: ";
        $m .= $response["login"];
        $m .= "\nEmail: ";
        $m .= $response["email"];
        $m .= "\nČas: ";
        $m .= $response["regDate"];
        $m .= "\nWeb: ";
        $m .= ROOT;

        //return EmailSender::odesli("jakub.charvot@centrum.cz","Nová registrace na web",$m,"reg@charvot.cz");
        return Webhook::SendToDiscord($m,"Nová registrace");
    }
}
