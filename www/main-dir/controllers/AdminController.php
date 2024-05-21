<?php

class AdminController extends Controller
{
    public function zpracuj($parametry)
    {        
        $this->hlavicka['titulek'] = 'Administrace';   
        
        if(!in_array(1,$_SESSION['userRoles']))
            $this->presmeruj("semnelezpls");
        if(empty($_GET['userId']))//seznam vsech uzivatelu
        {
            $checkboxes = array('ID'=>true,'jmeno'=>true,'prezdivka'=>true,'login'=>false,'email'=>false,'regDate'=>false,'accountNumber'=>false,'balance'=>false,'webs'=>false,'admin'=>false,'allWebs'=>false);
            if($_SERVER['REQUEST_METHOD']=="POST")
            {
                $checkboxes['ID'] = (empty($_POST['ID']))?false:true;
                $checkboxes['jmeno'] = (empty($_POST['jmeno']))?false:true;
                $checkboxes['prezdivka'] = (empty($_POST['prezdivka']))?false:true;
                $checkboxes['login'] = (empty($_POST['login']))?false:true;
                $checkboxes['email'] = (empty($_POST['email']))?false:true;
                $checkboxes['regDate'] = (empty($_POST['regDate']))?false:true;
                $checkboxes['accountNumber'] = (empty($_POST['bank']))?false:true;
                $checkboxes['balance'] = (empty($_POST['bank']))?false:true;
                $checkboxes['webs'] = (empty($_POST['webs']))?false:true;
                $checkboxes['admin'] = (empty($_POST['admin']))?false:true;
                $checkboxes['allWebs'] = (empty($_POST['allWebs']))?false:true;
            }

            if(!$checkboxes['allWebs'])
            $this->data["users"]=UserManager::GetUsersAdmin(0,4); // TODO obecne
            else
            $this->data["users"]=UserManager::GetUsersAdmin();

            $this->pohled = 'admin';
            $this->data['checkboxes']=$checkboxes;
        }
        else // zobrazeni konkretniho uzivatele
        {
            if($_SERVER['REQUEST_METHOD']=="POST")
            {
                if(!empty($_POST["login"])){
                    $newLogin=$_POST['login'];
                    //echo $newLogin;
                    if(Db::dotazJeden("SELECT login from users where login=?",array($newLogin))>0)
                        $this->data["warnMessage"]="Zadaný login už někdo používá";
                    else{
                        if(Db::dotazOld("UPDATE users set login=? where ID=?",array($newLogin,$_GET['userId']))>0)
                            $this->data["infoMessage"]="Úspěšně uloženo";
                        else
                            $this->data["warnMessage"]="Oups, chybka...";
                    }

                }
                else if(!empty($_POST["email"])){
                    $newEmail=$_POST['email'];
                    //echo $newLogin;
                    if(Db::dotazJeden("SELECT email from users where email=?",array($newEmail))>0)
                        $this->data["warnMessage"]="Zadaný E-mail už někdo používá";
                    else{
                        if(Db::dotazOld("UPDATE users set email=? where ID=?",array($newEmail,$_GET['userId']))>0)
                            $this->data["infoMessage"]="Úspěšně uloženo";
                        else
                            $this->data["warnMessage"]="Oups, chybka...";
                    }

                }
                else if(!empty($_POST["web"])){
                   $postedArray =$_POST['web'];
                   $tempArray = Db::dotazVsechny("SELECT groupId from user_groups where userId=?",array($_GET['userId']));
                   $actualArray = array();
                   $addArray = array();
                   $removeArray = array();
                  
                   foreach($tempArray as $item)
                   {
                       array_push($actualArray,$item['groupId']);
                       if(!in_array($item['groupId'],$postedArray))
                            array_push($removeArray,$item['groupId']);
                   }
                   foreach($postedArray as $item)
                   {
                       if(!in_array($item,$actualArray))
                            array_push($addArray,$item);
                   }
                   $sql ="INSERT into user_groups (userId,groupId) VALUES (?,?)";
                   $okCount =0;
                   foreach($addArray as $item)
                   {
                       if(Db::dotazOld($sql,array($_GET['userId'],$item))>0)
                        $okCount++;
                   }
                   if($okCount!=count($addArray))
                   {
                    $this->data["warnMessage"]="Oups, chybka...";
                    goto endW;
                   }
                   $sql ="DELETE FROM user_groups where(userId=? and groupId=?)";
                   $okCount =0;
                   foreach($removeArray as $item)
                   {
                       if(Db::dotazOld($sql,array($_GET['userId'],$item))>0)
                        $okCount++;
                   }
                   if($okCount!=count($removeArray))
                   {
                    $this->data["warnMessage"]="Oups, chybka...";
                    goto endW;
                   }
                   $this->data["infoMessage"]="Úspěšně uloženo";

                   endW:
                }
                else if(!empty($_POST["roles"])){
                    $postedArray =$_POST['roles'];
                    $tempArray = Db::dotazVsechny("SELECT roleId from user_role where userId=? and groupId=?",array($_GET['userId'],$_SESSION["groupId"]));
                    $actualArray = array();
                    $addArray = array();
                    $removeArray = array();
                   
                    foreach($tempArray as $item)
                    {
                        array_push($actualArray,$item['roleId']);
                        if(!in_array($item['roleId'],$postedArray))
                             array_push($removeArray,$item['roleId']);
                    }
                    foreach($postedArray as $item)
                    {
                        if(!in_array($item,$actualArray))
                             array_push($addArray,$item);
                    }
                    $sql ="INSERT into user_role (userId,roleId,groupId) VALUES (?,?,?)";
                    $okCount =0;
                    foreach($addArray as $item)
                    {
                        if(Db::dotazOld($sql,array($_GET['userId'],$item,$_SESSION["groupId"]))>0)
                         $okCount++;
                    }
                    if($okCount!=count($addArray))
                    {
                     $this->data["warnMessage"]="Oups, chybka...";
                     goto endR;
                    }
                    $sql ="DELETE FROM user_role where(userId=? and roleId=? and groupId=?)";
                    $okCount =0;
                    foreach($removeArray as $item)
                    {
                        if(Db::dotazOld($sql,array($_GET['userId'],$item,$_SESSION["groupId"]))>0)
                         $okCount++;
                    }
                    if($okCount!=count($removeArray))
                    {
                     $this->data["warnMessage"]="Oups, chybka...";
                     goto endR;
                    }
                    $this->data["infoMessage"]="Úspěšně uloženo";
 
                    endR:
                 }
                else if(isset($_POST["admin"])){
                    $newState=$_POST['admin'];

                    if($newState)
                    {
                        //echo "01";
                        if(!Db::dotazJeden("SELECT ID from user_role where userId=? and roleId=1 and groupId=?",array($_GET['userId'],$_SESSION["groupId"]))>0)
                        $sql="INSERT into user_role (userId,roleId,groupId) values(?,1,?)";
                    }
                    else
                    {
                        //echo "02";
                        if(Db::dotazJeden("SELECT ID from user_role where userId=? and roleId=1",array($_GET['userId']))>0)
                        $sql="DELETE from user_role where userId=? and roleId=1 and groupId=?";
                    }
                            
                            
                    if(isset($sql) && Db::dotazOld($sql,array($_GET['userId'],$_SESSION["groupId"]))>0)
                        $this->data["infoMessage"]="Úspěšně uloženo";
                    else
                        $this->data["warnMessage"]="Oups, chybka...";
                }
            }
            $this->data["user"]=UserManager::GetUsersAdmin($_GET['userId'])[0];
            $this->pohled = 'admin-user';
            $this->data["webs"]=Db::dotazVsechny("SELECT * from web_sites where id>0",array());
            $this->data["roles"]=Db::dotazVsechny("SELECT * from web_role where id>1",array());
        }
        

        
        
        
        
        
    }

    
}