<?php 

class SpravceDiskuze
{
    public function VratPrispevky($themeId,$groupName)
    {
        $sql = "SELECT chat.ID,users.prezdivka ,CONCAT(users.jmeno, ' ', users.prijmeni) as jmeno, chat.text, chat.date,
        (CASE WHEN t.prispevekID is NULL THEN 0 ELSE t.likes END) as likes, 
        (CASE WHEN t.prispevekID is NULL THEN 0 ELSE t.dislikes END) as dislikes
    FROM `chat`
    INNER JOIN users ON chat.userId = users.ID
    LEFT JOIN 
    (SELECT prispevekID, 
         SUM((CASE WHEN state=0 THEN 1 ELSE 0 END))as dislikes, 
         SUM((CASE WHEN state=1 THEN 1 ELSE 0 END))as likes from chat_likes 
         GROUP BY prispevekID) as t ON chat.ID = t.prispevekID
    INNER JOIN chat_themes as ct ON chat.themeID = ct.ID
    LEFT JOIN `groups` as g on ct.groupId = g.ID
    WHERE chat.active=1 AND chat.themeID = ? AND g.name=?
    ORDER BY chat.date ASC";          
        $prispevky = Db::dotazVsechny($sql,array($themeId, $groupName));        
        return $prispevky;
    }

    public function PridejLike($prispevekId,$themeId, $state,$userId,$groupName)
    {
        if(!$this->checkThemeWeb($themeId,$groupName))
            return false; //tema, kam chce pridat, neexistuje nebo nalezi jinemu webu
            
        $response = Db::dotazJeden("SELECT state FROM chat_likes WHERE (userId=? AND prispevekID=?)",array($userId,$prispevekId));
        if($response>0)
        {
            if($response["state"]==$state)
            {
                $sql = "DELETE FROM chat_likes WHERE (userId=? AND prispevekID=?)";
                Db::dotazOld($sql,array($userId,$prispevekId));
            }
            else{
                $sql = "UPDATE chat_likes SET `state`=? WHERE (userId=? AND prispevekID=?)";
                Db::dotazOld($sql,array($state,$userId,$prispevekId));
            }
        }
        else
        {
            $sql = "INSERT INTO chat_likes (userId, prispevekID,state) VALUES (?,?,?)";
            Db::dotazOld($sql,array($userId,$prispevekId,$state));
        }
        //echo($id . " " . $state);
    }

    public function PridejPrispevek($userId, $text, $themeId, $groupName)
    {
        if(!$this->checkThemeWeb($themeId,$groupName))
            return false; //tema, kam chce pridat, neexistuje nebo nalezi jinemu webu    
        $sql = "INSERT INTO chat(userId, text, themeID) VALUES (?,?,?)";
        Db::dotazOld($sql,array($userId,$text,$themeId));
    }

    public function VratTemata($userId,$groupName,$roleName="all")
    {
        $sql = "SELECT ct.ID,ct.themeName, (case when cPocet.pocet is null then 0 else cPocet.pocet end) as pocet,
        wr.name as roleName, (case when cur.unread is null THEN case when lastReadId is null then 
                                  case when cPocet.pocet is null then 0 else cPocet.pocet end ELSE 0 end else cur.unread end) as unread from chat_themes ct
            left join `groups` as g
                on g.ID = ct.groupId
            left join 
            (select * from chat_read where userId =?) cr
                on ct.ID = cr.themeId
            left join
            (SELECT cr.themeId, count(*) unread
                      FROM chat as c
                         INNER JOIN chat_read cr 
                           ON c.themeID = cr.themeId
                    WHERE cr.userId = 1 and c.ID > cr.lastReadId group by c.themeID) as cur
                    on ct.ID=cur.themeId    
             left join (SELECT themeID, count(*) as pocet from chat GROUP BY themeID) as cPocet
                 on ct.ID=cPocet.themeID
             left join web_role as wr
                 on ct.roleId=wr.ID
        where  g.name= ? and wr.name=?";
        $temata = Db::dotazVsechny($sql,array($userId,$groupName,$roleName));

        return $temata;
    }

    public function VratJmenoTematu($ID)
    {
        $sql = "SELECT themeName from chat_themes where ID=?";
        return Db::dotazJeden($sql,array($ID))["themeName"];
    }

    public function PridejTema($themeName,$groupName,$roleId)
    {
        $groupId = Db::dotazJeden("SELECT ID from web_sites where name=?", array($groupName))["ID"];
        $sql = "INSERT INTO chat_themes (themeName,groupId,roleId) VALUES (?,?,?)";
        Db::dotazOld($sql,array($themeName, $groupId,$roleId));
        return Db::lastId();
    }
    private function checkThemeWeb($themeId,$groupName)
    {
        $sql="SELECT ct.ID from chat_themes as ct
        left join `groups` as g
            on ct.groupId = g.ID
            where ct.ID=? and g.name=?";
        if(Db::dotazJeden($sql,array($themeId,$groupName))==null)
            return false; //pokud tema nalezi jinemu webu
        else
            return true;
    }

    public function MarkThemeAsRead($themeId,$userId=null)
    {
        $userId =($userId==null)?$_SESSION["userId"]:$userId;

        if(Db::dotazJeden("SELECT ID from chat where themeId=?",array($themeId))>0)
        {
            if(Db::dotazJeden("SELECT ID from chat_read where userId=? and themeId=?",array($userId,$themeId))>0)
            {
                $sql = "UPDATE chat_read set lastReadId=(select max(ID) from chat WHERE themeId=?) where userId=? and themeId=?";
                Db::dotazOld($sql,array($themeId,$userId,$themeId));
            }
            else
            {
                $sql = "INSERT INTO chat_read (lastReadId,userId,themeId) select (case when max(ID) is null then 0 else max(ID) end)as ID,?,? from chat WHERE themeId=?";
                Db::dotazOld($sql,array($userId,$themeId,$themeId));
            }
        }
    }

}