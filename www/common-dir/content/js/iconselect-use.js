            
var iconSelect;
var selectedIconInput;

window.onload = function(){
    
    selectedIconInput = document.getElementById('itemIcon');
    
    document.getElementById('my-icon-select').addEventListener('changed', function(e){
        selectedIconInput.value = iconSelect.getSelectedValue();
    });
    
    iconSelect = new IconSelect("my-icon-select", 
    {'selectedIconWidth':48,
    'selectedIconHeight':48,
    'selectedBoxPadding':5,
    'iconsWidth':48,
    'iconsHeight':48,
    'boxIconSpace':1,
    'vectoralIconNumber':2,
    'horizontalIconNumber':6});


    var icons = [];
    icons.push({'iconFilePath':'common-dir/content/img/inv/1.png', 'iconValue':'1'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/2.png', 'iconValue':'2'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/3.png', 'iconValue':'3'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/4.png', 'iconValue':'4'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/5.png', 'iconValue':'5'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/6.png', 'iconValue':'6'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/7.png', 'iconValue':'7'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/8.png', 'iconValue':'8'});
    icons.push({'iconFilePath':'common-dir/content/img/inv/9.png', 'iconValue':'9'});
    
 
    iconSelect.refresh(icons);
    iconSelect.setSelectedIndex(itemIconId-1);
  



};
    
