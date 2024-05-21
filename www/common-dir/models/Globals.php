<?php 
class Globals{
    public static function prepisDatum($dateStr, $format=null)
    {   
        $date = strtotime($dateStr);
        if($format==null)
        {
            return (date("j.n.",$date)!=date("j.n.")?date("j.n.",$date):"").((date("Y")!=date("Y",$date)) ? date(" Y ",$date) : " ").date("H:i",$date);
        //j - Day of the month without leading zeros
        //n - Numeric representation of a month, without leading zeros
        }
        else
        {
            return(date($format,$date));
        }
        
    }
}