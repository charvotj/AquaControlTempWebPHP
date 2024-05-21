<?php
class PhotoGallery
{
    public function __construct($dirLarge,$dirSmall=null,$titleArray=null)
    {
        // large
        $this->dirLarge = $dirLarge;
        if(!file_exists($dirLarge))
            mkdir($dirLarge,0777,true); // vytvoří potřebné složky
        $this->photosLarge = scandir($dirLarge); 

        //small
        if($dirSmall!=null)
        {
            $this->dirSmall = $dirSmall;
            if(!file_exists($dirSmall))
                mkdir($dirSmall,0777,true); // vytvoří potřebné složky
            $this->photosSmall = scandir($dirSmall); 
            
            $this->generateMiniatures();
        }
        //photo titles
        if($titleArray!=null)
        {
            // $titleArray ve formátu ['userId' což je zároveň photoName bez přípony]=>title

            // podle titleArray seradime photosLarge
            $orderKeys = array_keys($titleArray);// viz pozn na zacatku if
            $tempLarge = array();
           foreach($this->photosLarge as $item)
           {
               $userId = explode("\.",$item)[0];
               $index = array_search($userId,$orderKeys);
               if($index!== false)
               {
                   $tempLarge[$index] = $item;
               }
           }
           ksort($tempLarge);
           $this->photosLarge = $tempLarge;
           // viz pozn na zacatku if
           $this->titleArray = array_values($titleArray);

        }
        else
            $this->titleArray = array();

    }

    private function generateMiniatures()
    {
        //odstrani prebytecne miniatury
        for ($i = 2; $i <= (count($this->photosSmall)-1); $i++)
        { 
            if(!in_array($this->photosSmall[$i],$this->photosLarge))
            {
                unlink($this->dirSmall.'/'.$this->photosSmall[$i]);
            }
        }
        
        // znovu nacte upravenou slozku
        $this->photosSmall = scandir($this->dirSmall);

        // vygeneruje chybejici miniatury
        for ($i = 2; $i <= (count($this->photosLarge)-1); $i++) {
            if(!in_array($this->photosLarge[$i],$this->photosSmall))
            {
                $sourcePath = $this->dirLarge.'/'.$this->photosLarge[$i];
                $size = getimagesize($sourcePath);
                if($size[0]<=5000 && $size[1]<=5000)
                {
                    $pripona = explode(".",$this->photosLarge[$i])[1];
                    if($pripona=="jpg" || $pripona=="jpeg")
                    {
                        $source_image = imagecreatefromjpeg($sourcePath);
                    }
                    else if($pripona=="png")
                    {
                        $source_image = imagecreatefrompng($sourcePath);
                    }
                    else if($pripona=="gif")
                    {
                        $source_image = imagecreatefromgif($sourcePath);
                    }
                }
                
                // pokud je obrázek moc velký nebo má divnou příponu
                if(!isset($source_image))
                {
                    $source_image = imagecreatefrompng("common-dir/content/img/cross.png");
                }
                

                // pokud je v exif datech fotka otočená, otočí podle nich i miniaturu
                if(function_exists("exif_read_data")){
                    $exif = exif_read_data($sourcePath);
                    if(!empty($exif['Orientation'])) {
                    switch($exif['Orientation']) {
                    case 8:
                        $source_image = imagerotate($source_image,90,0);
                        break;
                    case 3:
                        $source_image = imagerotate($source_image,180,0);
                        break;
                    case 6:
                        $source_image = imagerotate($source_image,-90,0);
                        break;
                    }
                    }
                }



                $source_imagex = imagesx($source_image);
                $source_imagey = imagesy($source_image);
                // mensi stranu zmensi na 150px, vetsi podle pomeru
                if($source_imagex>$source_imagey)
                {
                    $dest_imagey = 200;
                    $dest_imagex = ($dest_imagey / $source_imagey) * $source_imagex;
                }
                else
                {
                    $dest_imagex = 200;
                    $dest_imagey = ($dest_imagex / $source_imagex) * $source_imagey;
                }
                                    
                $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
                imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, 
                $dest_imagey, $source_imagex, $source_imagey);
                imagejpeg($dest_image, $this->dirSmall.'/'.$this->photosLarge[$i], 90);
                imagedestroy($source_image);
                imagedestroy($dest_image);
                // znovu nacte zmenenou slozku
                $this->photosSmall = scandir($this->dirSmall);
                
            }
        }
    }

    public function GenerateFotoViewer()
    {
        echo '<div id="photoslider" class="photoslider" style="visibility:hidden; opacity:0;">    
                    <div class="photoslider-control">
                        <span onclick="PhotoUp()" style="right:5px;"><img src="common-dir/content/img/gallery/right.png"/></span>
                        <span onclick="PhotoBack()" style="right:55px;"><img src="common-dir/content/img/gallery/left.png"/></span>
                        <span onclick="Zavri()" style="left:5px;"><img src="common-dir/content/img/gallery/cross.png"/></span>                
                    </div>
                
                    <div id="photo"></div>
                    <div id="photoName"></div>
                </div>';

        echo '<script>
                    var index = 0;
                    var directory = "'.$this->dirLarge.'/";
                    var photos = ['."'". implode("', '",$this->photosLarge)."'".'];
                    var photoTitles = ['."'". implode("', '",$this->titleArray)."'".'];
                    //photos.shift();
                    //photos.shift(); 
                    
                    if(photoTitles.length!=photos.length)
                    {
                        photoTitles=null;
                    }
                    var active = false;
                    function PhotoBack()
                    {
                        index--;
                        if(index<0)
                        {
                            index = photos.length - 1;
                        }
                        Zobraz();
                    }
                    function PhotoUp()
                    {
                        index++;
                        if(index>=(photos.length))
                        {
                            index = 0;
                        }
                        Zobraz();
                    }
                    function ZobrazIndex(ind)
                    {
                        active = true;
                        var a = document.getElementById("photoslider");
                        a.style.visibility = "visible";
                        a.style.opacity = "1";
                        index = ind;
                        Zobraz();
                    }
                    function Zavri()
                    {
                        active = false;
                        var a =document.getElementById("photoslider");
                        a.style.opacity = "0";
                        a.style.visibility = "hidden";
                    }
                    function Zobraz()
                    {
                        var a = document.getElementById("photo");
                        a.innerHTML = "<img src=\'"+directory+photos[index].replace("#","%23")+"\' />";

                        if(photoTitles!=null)
                        {
                            var b = document.getElementById("photoName");
                            b.innerHTML = "<span style=\'color:white;background-color: #0000005e;padding: 5px;max-width:100vw;\'>"+photoTitles[index]+"</span>";
                        }

                    }
                    function Klavesa(event)
                    {
                        if(active==true)
                        {
                        var x = event.key;
                        if(x=="ArrowLeft")
                            PhotoBack();
                        else if(x=="ArrowRight")
                            PhotoUp();
                        else if(x=="Escape")
                            Zavri();
                        }
                    }
                /*swipe detekce*/
                let touchstartX = 0;
                let touchendX = 0;
                
                const gestureZone = document.getElementById("photoslider");
                
                gestureZone.addEventListener("touchstart", function(event) {
                    touchstartX = event.changedTouches[0].screenX;    
                }, false);
                
                gestureZone.addEventListener("touchend", function(event) {
                    touchendX = event.changedTouches[0].screenX; 
                    handleGesture();
                }, false); 
                
                function handleGesture() {
                    if(active==true)
                        {
                    if ((touchstartX-touchendX)>30) {
                        console.log("Swiped left");
                        PhotoUp();                        
                    }
                    
                    if ((touchendX-touchstartX)>30) {
                        console.log("Swiped right");
                        PhotoBack();
                    }
                
                    if (touchendX == touchstartX) {
                        console.log("Tap");
                    }
                }
                }
                </script>';
        
        
    }
}