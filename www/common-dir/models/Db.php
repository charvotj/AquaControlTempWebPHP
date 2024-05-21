<?php 

 class Db 
{
    private static $conn;

    private static $nastaveni = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    public static function pripoj($host, $uzivatel, $heslo, $databaze)
    {
        try{
            if (!isset(self::$conn))
            {
                self::$conn = @new PDO(
                    "mysql:host=$host;dbname=$databaze",
                    $uzivatel,
                    $heslo,
                    self::$nastaveni
                );
            }
        }
        catch (PDOException $e) {            
            /*EmailSender::SendError($e);
            die("Omlouváme se, došlo k neočekávané chybě s databází, zkuste použít F5, přípdně se připojte později");*/
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