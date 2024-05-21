<?php

class HashController extends Controller
{
    public function zpracuj($parametry)
    {
        

        // Odpoznámkovat pro funkci
        $this->presmeruj("asdasssdfdsfds");

        
        $regMaster = new RegistrationMaster();

/*         $newUsers = array(
                        array("Sebastian", "Žák", "", "seba", "seba2", "Seba"),
                        array("Jakub", "Bagier", "smoula", "", "smoula3", "Šmoula"),
                        array("Lukáš", "Kurovský", "", "koblih", "koblih4", "Koblih"),
                        array("Dominik", "Hlíva", "", "dominik", "dominik5", "Dominik"),
                        array("Ester", "Jančičková", "", "ester", "ester6", "Ester"),
                        array("Veronika", "Huňařová", "", "verca", "verca7", "Verča"),
                        array("Liliana", "Ostružková", "", "lili", "lili8", "Lili"),
                        array("Richard", "Hrubčín", "", "risa", "risa9", "Ríša"),
                        array("Tobiáš", "Doskočil", "", "tobi", "tobi10", "Tobi"),
                        array("Radek", "Jančička", "", "radek", "radek11", "Řádek"),
                        array("Jiří", "Vaněk", "", "sirka", "sirka12", "Sirka"),
                        array("Jindra", "Vaněk", "", "jindra", "jindra13", "Jindra"),
                        array("Adam", "Kloubek", "", "adam", "adam14", "Adam"),
                        array("Matěj", "Klus", "", "matej", "matej15", "Matěj"),
                        array("Sára", "Zorychtová", "", "sara", "sara16", "Sára m."),
                        array("Sabina", "Wojciková", "", "sabca", "sabca17", "Sabča"),
                        array("Rosťa", "Rosťa", "", "rosta", "rosta18", "Rosťa"),
                        array("Samuel", "Kondziolka", "", "sam", "sam19", "Sam")

        ); */

        $newUsers = array(
           
            array("Hana", "Švancarová", "", "hanka", "hanka007", "Hanina"),
           

);

        
           foreach($newUsers as $user)
           {
                echo $user[3];
               if($regMaster->Zaregistruj($user[0],$user[1],$user[2],$user[3],$user[4],$user[5])!=false)
               {
                   echo " ok";
               }
               else
                    echo " fail";
           }
        
        echo"done";
        
    }
}