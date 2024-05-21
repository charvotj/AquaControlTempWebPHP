function SkryjNeboZobrazMenu(menuId,backId)
{
    let menu = document.getElementById(menuId);
    let back = document.getElementById(backId);
    let icon = document.getElementById("hamburger");
    if(menu.style.right != "-80vw")
    {
        menu.style.right="-80vw";
        back.style.opacity="0";
        back.style.visibility="hidden";     
        icon.className="hamburger hamburger--elastic";
    }
    else
    {
        menu.style.right="0vw";
        back.style.opacity="1";
        back.style.visibility="visible"; 
        back.setAttribute('onclick',"SkryjNeboZobrazMenu('menu','back')");        
        icon.className="hamburger hamburger--elastic is-active";
    } 
}



