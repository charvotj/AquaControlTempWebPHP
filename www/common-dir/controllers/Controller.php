<?php
abstract class Controller
{
    public static $loginRequire = true;
    public static $allowedRoles = array();
    public static $menuTitle = "SampleText";
    public static $footerOn = true;
    protected $data = array();
    protected $pohled = "";
    protected $hlavicka = array('titulek' => '', 'klicova_slova' => '', 'popis' => '');

    abstract function zpracuj($parametry);
    
    public function vypisPohled()
    {
        if ($this->pohled)
        {
                extract($this->data);
                require(ROOT."-dir/views/" . $this->pohled . ".phtml");
        }
    }   
    public function presmeruj($url,$absolutePath=false)
    {
        if($absolutePath)
                header("Location: $url");
        else
                header("Location: /".ROOT."/$url");
        header("Connection: close");
        exit;
    }

    public function forceUserLogin()
    {
        $url="";
        $wantedUrlParsed = parse_url($_SERVER['REQUEST_URI']);
        if(!empty($wantedUrlParsed["path"]))
                $url.=$wantedUrlParsed["path"];
        if(!empty($wantedUrlParsed["query"]))
                $url.="?".$wantedUrlParsed["query"];

        if($url!="")
                $url = "&url=".urlencode($url);

        header("Location: /main/login?require$url");
        header("Connection: close");
        exit;
    }

    protected function parsujURL($url)
    {
            $naparsovanaURL = parse_url($url);
            $naparsovanaURL["path"] = ltrim($naparsovanaURL["path"], "/");
            $naparsovanaURL["path"] = trim($naparsovanaURL["path"]);
            $rozdelenaCesta = explode("/", $naparsovanaURL["path"]);
            return $rozdelenaCesta;
    }

    protected function pomlckyDoVelblouda($text)
    {
            $text = str_replace('-',' ',$text);
            $text = ucwords($text); // dela velka pismena
            $text = str_replace(' ','',$text);
            return $text;            
    }
}