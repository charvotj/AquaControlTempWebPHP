<?php 

class AkvaManager
{
    public static function AddSystem($mainUnitSN)
    {
        // load default config
        $relaysConfig = file_get_contents("default-configuration/relays-default.json");
        $sql = "INSERT INTO `akva_systems`(`mainUnitSN`, relaysConfiguration) VALUES (?,?)";
        if(Db::dotazOld($sql,array($mainUnitSN,$relaysConfig))>0)
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
        // load default config
        $moduleConfig = file_get_contents("default-configuration/module-$nodeType-default.json");
        $sql = "INSERT INTO `akva_modules`(`systemId`, `nodeType`, `nodeSN`, `configuration`) VALUES (?,?,?,?)";
        if(Db::dotazOld($sql,array($systemId,$nodeType,$nodeSN,$moduleConfig))>0)
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

    public static function GetRelaysConfig($systemId)
    {
        $sql = "SELECT akva_systems.relaysConfiguration
                FROM akva_systems

                WHERE akva_systems.ID=?";
        return Db::dotazJeden($sql,array($systemId));
    }

    public static function GetModuleConfig($moduleId)
    {
        $sql = "SELECT akva_modules.configuration
                FROM akva_modules

                WHERE akva_modules.ID=?";
        return Db::dotazJeden($sql,array($moduleId));
    }

    public static function SaveModuleConfig($moduleId,$newConfig)
    {
        $sql = "UPDATE akva_modules set `configuration`=?
                WHERE akva_modules.ID=?";
        if(Db::dotazOld($sql,array($newConfig,$moduleId)) < 0)
            return false;
        
        $sql = "SELECT akva_modules.systemId
                FROM akva_modules
                WHERE akva_modules.ID=?";
        $systemId = Db::dotazJeden($sql,array($moduleId))['systemId'];
        print_r("SystemID:".$systemId);

        $sql = "UPDATE akva_systems 
                SET `configurationVersion`=configurationVersion + 1
                WHERE akva_systems.ID=?";
        if(Db::dotazOld($sql,array($systemId)) < 0)
            return false;
        return true;
    }

    public static function NodeTypeToString($nodeType)
    {
        switch ($nodeType) {              
            case 2: 
                return "Ovládání LED";
            case 3: 
                return "Sensor teploty";
            case 4: 
                return "Sensor hladiny";
            case 5: 
                return "Sensor pH";
                
            default:
                return "Neznámá periferie";
        }
    }

    public static function SaveModuleConfigFromPost()
    {
        $moduleId = $_POST['moduleId'];
        $nodeType = $_POST['nodeType'];
        $config = self::GetModuleConfig($moduleId)['configuration'];
        $config = json_decode($config,true);

        if($config['nodeType'] != $nodeType)
            return false;

        switch ($nodeType) {              
            case 2: 
                for($i=0;$i<2;$i++)
                {
                    $config['ledStrips'][$i]["intensity"] = intval($_POST["ledStrip_$i"."_intensity"]);
                    $config['ledStrips'][$i]["startTime"] = $_POST["ledStrip_$i"."_startTime"];
                    $config['ledStrips'][$i]["endTime"]   = $_POST["ledStrip_$i"."_endTime"];
                    $config['ledStrips'][$i]["riseTime"]  = intval($_POST["ledStrip_$i"."_riseTime"]);
                    $config['ledStrips'][$i]["fallTime"]  = intval($_POST["ledStrip_$i"."_fallTime"]);
                }
                break;
            case 3: 
                $config['alarm']['active']   = isset($_POST['alarm_active'])?1:0;
                $config['alarm']['minValue'] = floatval($_POST['alarm_minValue']);
                $config['alarm']['maxValue'] = floatval($_POST['alarm_maxValue']);
                break;
            case 4: 
                $config['alarm']['active']   = isset($_POST['alarm_active'])?1:0;
                $config['alarm']['minValue'] = floatval($_POST['alarm_minValue']);
                $config['alarm']['maxValue'] = floatval($_POST['alarm_maxValue']);
                break;
            case 5: 
                $config['alarm']['active']   = isset($_POST['alarm_active'])?1:0;
                $config['alarm']['minValue'] = floatval($_POST['alarm_minValue']);
                $config['alarm']['maxValue'] = floatval($_POST['alarm_maxValue']);
                break;
                
            default:
                return false;

        }
        self::SaveModuleConfig($moduleId,json_encode($config));

    }
    
}