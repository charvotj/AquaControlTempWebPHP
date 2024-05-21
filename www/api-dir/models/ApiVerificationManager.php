<?php 

 class ApiVerificationManager 
{
    public static function checkDigest($digest,$unix)
    {

        //$actualUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // traefik meni https na http a pak nesedi digest, proto radek zmenen
        $actualUrl = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $hashData = AKVA_API_KEY.$actualUrl.$unix;

        $digestCheck = hash("sha512",$hashData);

        //echo "dig: $digest\ndigCheck: $digestCheck";

        if($digestCheck==$digest)
        {
            // check timestamp if it's actual
            $difference = abs(floor($unix/1000) - time());
            // echo "\ndiff: $difference";
            if($difference>300) // > 300s clearly a russian hacker
                return false;
            else
                return true;
        }
    }
}



    public static function dotazJeden($dotaz, $parametry = array())
    {
        try{
            if(self::$conn==null)
                throw new Exception("Databáze není připojena");
            $navrat = self::$conn->prepare($dotaz);
            $navrat->execute($parametry);
            return $navrat->fetch();
        }
        catch (Exception $e) {
            EmailSender::SendError($e);
            return -1;
        } 
    }

    public static function dotazVsechny($dotaz, $parametry = array())
    {
        try{
            if(self::$conn==null)
                throw new Exception("Databáze není připojena");
        $navrat = self::$conn->prepare($dotaz);
        $navrat->execute($parametry);
        
        return $navrat->fetchAll();
        }
        catch(Exception $e)
        {
            EmailSender::SendError($e);
            return -1;
        }
    }

    // Spustí dotaz a vrátí počet ovlivněných řádků nebi PDOException
    public static function dotaz($dotaz, $parametry = array())
    {
        try{
            if(self::$conn==null)
                    throw new Exception("Databáze není připojena");
                $navrat = self::$conn->prepare($dotaz);        
                $navrat->execute($parametry);
                return $navrat->rowCount();
        } catch (PDOException $e) {
            EmailSender::SendError($e);
            // print_r($e->errorInfo);
            // $e->
            return $e;
        }      
    }
    public static function lastId()
    {
        return self::$conn->lastInsertId();
    }

    public static function dotazOld($dotaz, $parametry = array())
    {
        try{
            if(self::$conn==null)
                    throw new Exception("Databáze není připojena");
                $navrat = self::$conn->prepare($dotaz);        
                $navrat->execute($parametry);
                return $navrat->rowCount();
        } catch (PDOException $e) {
            EmailSender::SendError($e);
            return -1;
        }      
    }
}