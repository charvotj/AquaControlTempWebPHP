let editor = document.querySelector(".editor-content");
let filePicker = document.getElementsByName("vyzvaImages[]")[0];

function textAlign(align)
{
    editor.style.textAlign = align;
}

function color(color)
{
    editor.style.color = color;
}

function fontStyle(style)
{
    if (window.getSelection) {
        let sel = window.getSelection();

        let range = sel.getRangeAt(0);

        console.log(range.commonAncestorContainer);
        let newContent = range.cloneContents();

        if(range.commonAncestorContainer.className !='editor-content')
        {
            let newElement = setStyle(style,newContent);
            range.deleteContents();
            range.insertNode(newElement);
            
        }
        else
        {
            console.log(range);

           
            let startParentDiv = range.startContainer.parentElement.closest("div");
            let endParentDiv = range.endContainer.parentElement.closest("div");

            let editorDivs = editor.querySelectorAll('div');
            let processThis = false;

            editorDivs.forEach(div=>{
                let tempRange = null;
                let tempContent,newElement;

                // start a end jsou relativní, záleží na směru výběru
                if(div==startParentDiv)
                {
                    tempRange = new Range();
                    tempRange.setStart(range.startContainer,range.startOffset);
                    tempRange.setEndAfter(startParentDiv.lastChild);

                    processThis = !processThis;
                }
                else if(div==endParentDiv)
                {
                    tempRange = new Range();
                    tempRange.setStartBefore(endParentDiv.firstChild);
                    tempRange.setEnd(range.endContainer,range.endOffset);

                    processThis = !processThis;
                }
                else if(processThis)
                {
                   tempRange = new Range();
                   tempRange.selectNodeContents(div);
                }

                if(tempRange != null)
                {
                    tempContent = tempRange.cloneContents();
                    newElement = setStyle(style,tempContent);
                    tempRange.deleteContents();
                    tempRange.insertNode(newElement);
                }
            });


            

            
        }
        

    }
}

function setStyle(style,newContent)
{
    let newElement = document.createElement("span");
    let spans = newContent.querySelectorAll('span');

    let notStyled;
    switch(style)
    {
        case('bold'):
            
            notStyled = true;
            spans.forEach(element => {
                if(element.style.fontWeight=='bold') // pokud je element tučný, zrušíme mu tento styl
                {
                    element.style.fontWeight='unset';
                    notStyled = false;
                }
            });
            if(notStyled) // pokud zde nebyl žádný tučný element, nastavíme celku tyčný styl
            {
                newElement.style.fontWeight='bold';
            }
            break;

        case('italic'):
            notStyled = true;
            spans.forEach(element => {
                if(element.style.fontStyle=='italic') // pokud je element kurzívou, zrušíme mu tento styl
                {
                    element.style.fontStyle='unset';
                    notStyled = false;
                }
            });
            if(notStyled) // pokud zde nebyl žádný element kurzívou, nastavíme celku tyčný styl
            {
                newElement.style.fontStyle='italic';
            }
            break;

        case('underline'):
            notStyled = true;
            spans.forEach(element => {
                if(element.style.textDecoration=='underline') // pokud je element podtržený, zrušíme mu tento styl
                {
                    element.style.textDecoration='none'
                    notStyled = false;
                }
            });
            if(notStyled) // pokud zde nebyl žádný podtržený element, nastavíme celku podtržený styl
            {
                newElement.style.textDecoration='underline';
            }
            break;
        default:
    }
    
    newElement.append(newContent);
    return newElement;
}


function addImage()
{
    let inp = document.createElement ("input");   
    inp.type = "file";
    //inp.onchange=showImage(this); 
    

    inp.addEventListener('change', (event) => {
        showImage(event.target);
      });
      inp.click();
    
}

function checkPhoto(img)
{
//   let img = new Image()
//   img.src = window.URL.createObjectURL(event.target.files[0])
//   img.onload = () => {
        if(img.width>5000)
        {
            alert("Tvůj obrázek je moc velký, maximální šířka je 5000px, zkus ho zmenšit.");
            return false;
        }
        else if(img.height>5000)
        {
            alert("Tvůj obrázek je moc velký, maximální výška je 5000px, zkus ho zmenšit.");
            return false;
        }
        else{
            return true;
        }
       
//   }
}

function showImage(fileInput)
{
    let img = new Image()
    img.src = window.URL.createObjectURL(fileInput.files[0])
    if(checkPhoto(img))
    {
        
        img.style.maxWidth="100%";
        img.style.display="block";
        img.style.width="auto";
        img.style.margin ="10px auto";
        img.id = fileInput.files[0].name;
        editor.append(img);

        // Objekt pro převod dat - do FileListu nejde přidávat, je třeba ho takto převést a vytvořit nový FileListm který pak přiřadíme inputu
        let list= new DataTransfer();
        Array.from(filePicker.files).forEach(f=>
            {
                list.items.add(f);
            });
        list.items.add(fileInput.files[0]);

        filePicker.files = list.files;
        updateFileNames();
    }
  
}

function updateFileNames()
{
    let span = document.getElementById("attached-photos");
    let files = filePicker.files;
    let text = "";
    Array.from(filePicker.files).forEach(f=>
        {
            text+=f.name;
        });
    span.innerHTML = Array.from(filePicker.files).map(e=>e.name).join(", ");
    console.log(files);
}

function doBeforeSubmit()
{
if(document.getElementById("vyzvaName").value=="")
{
    return false;
}
    let list= new DataTransfer();
    Array.from(filePicker.files).forEach(f=>
        {
            let extension = f.name.split(".")[1];
            let newName = Math.random().toString(36).substr(2)+Math.random().toString(36).substr(2)+"."+extension; // přejmenujeme soubor kvůli kolizi jmen na serveru
            let newFile = new File([f],newName);
            list.items.add(newFile);

            let img = document.getElementById(f.name);
            img.id=newName;
            img.src = "live-data/oddil/vyzvy/zadani/"+newName;
        });
        // pro input nastavíme list se změněnými jmény
    filePicker.files = list.files;

    // preneseni dat z editoru do hidden inputu
    document.getElementsByName('vyzvaContent')[0].value = editor.innerHTML;
}

function setCursor()
{
    // if(editor.childNodes.count)
    // {
    //     var range = new Range();
    //     var sel = window.getSelection()
        
    //     range.selectNodeContents(editor.firstChild);
    //     range.collapse(true) // not nessesary
        
    //     sel.removeAllRanges()
    //     sel.addRange(range)
    // }

   
}