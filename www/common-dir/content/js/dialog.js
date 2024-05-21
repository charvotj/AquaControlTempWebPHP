/* function DialogVisibility(id,state)
    {
        var dialog = document.getElementById(id);
        if(state)
        {
            dialog.style.display = "flex";
        }
        else
            dialog.style.display = "none";
        
    } */

    
    function showDialog(dialogId,backId) {
        let dialog = document.getElementById(dialogId);
        let back = document.getElementById(backId);
        dialog.style.display = "flex";        
        back.style.opacity="1";
        back.style.visibility="visible"; 
        back.setAttribute('onclick',"hideDialog('mainDialog','back',true)");
        document.getElementsByName("text")[0].focus();
    }
    let really = true;
    function hideDialog(dialogId,backId,r) {
        let dialog = document.getElementById(dialogId);
        let back = document.getElementById(backId);
        if(r==really)
        {        
            dialog.style.display = "none";
            back.style.opacity="0";
            back.style.visibility="hidden";
        }
        else
        really=r;
    }
