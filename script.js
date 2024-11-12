var fillBars = document.querySelectorAll('.fill-bar');

fillBars.forEach(function(fillBar) {
    var usedStorage = fillBar.getAttribute('data-value');
    var alertedStorage = document.getElementById('total-Storage');
    var totalStorage = alertedStorage.getAttribute('data-value');

    var value = (usedStorage / totalStorage) * 100 ;
   
    if(value <= 100 ){
    fillBar.style.width = value + "%" ;
    }
    else{
        fillBar.style.width = 100 + "%";
    }
});


var textElements = document.getElementsByClassName('textElement');

Array.prototype.forEach.call(textElements, function(textElement) {
    var originalText = textElement.textContent;

    var capitalizedText = originalText.charAt(0).toUpperCase() + originalText.slice(1);

    if (capitalizedText.length > 10) {
        textElement.textContent = capitalizedText.substring(0, 10) + '...';
    } else {
        textElement.textContent = capitalizedText;
    }
});

    function profileIcon() {
        var Profile = document.querySelector('.profile'); // Assuming profile is a class
        var Container = document.querySelector('.container1');
        if (Profile.style.display === 'none' || Profile.style.display === '') {
            Profile.style.display = 'flex';
            Container.style.filter = 'blur(8px)';
            Container.style.opacity = '0.9';
        } else {
            Profile.style.display = 'none';
            Container.style.filter = 'none';
            Container.style.opacity = '1';
        }
    }
    function storage(){
        let totalstorage=document.querySelector('.total-storage');
        let h4storage=document.querySelector("#h4storage");
        if(h4storage.style.display==="none" || h4storage.style.display===""){
            h4storage.style.display='flex';
            totalstorage.style.filter='blur(8px)';
            totalstorage.style.opacity='0.9';
        }
        else {
            h4storage.style.display='none';
            totalstorage.style.filter='none';
            totalstorage.style.opacity='1';
        }
    }
    function createBtn() {
        var Upload = document.querySelector('.upload-container'); // Assuming profile is a class
        var Container = document.querySelector('.container1');
       
        if (Upload.style.display === 'none' || Upload.style.display === '') {
            Upload.style.display = 'block';
            Container.style.filter = 'blur(8px)';
            Container.style.opacity = '0.5';
          
        } else {
            Upload.style.display = 'none';
            Container.style.filter = 'none';
            Container.style.opacity = '1';
        
        }
    }


    // script.js
    document.getElementById('fileUpload').addEventListener('change', function(event) {
        const preview = document.getElementById('preview');
        const files = event.target.files; // Get all selected files
    
        preview.innerHTML = ''; // Clear previous previews
    
        if (files.length === 0) {
            preview.innerHTML = 'No files selected.';
            return;
        }
    
        Array.from(files).forEach(file => {
            const fileType = file.type;
    
            if (fileType.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '200px'; 
                img.style.margin = '10px'; 
                preview.appendChild(img);
            } else if (fileType === 'application/pdf') {
                const pdfCanvas = document.createElement('canvas');
                pdfCanvas.style.margin = '10px'; 
                preview.appendChild(pdfCanvas);
                const pdfContext = pdfCanvas.getContext('2d');
    
                const reader = new FileReader();
                reader.onload = function(event) {
                    const pdfData = new Uint8Array(event.target.result);
                    pdfjsLib.getDocument({ data: pdfData }).promise.then(pdf => {
                        pdf.getPage(1).then(page => {
                            const viewport = page.getViewport({ scale: 1 });
                            pdfCanvas.height = viewport.height;
                            pdfCanvas.width = viewport.width;
    
                            const renderContext = {
                                canvasContext: pdfContext,
                                viewport: viewport
                            };
                            page.render(renderContext);
                        });
                    });
                };
                reader.readAsArrayBuffer(file);
            } else if (fileType.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.style.maxWidth = '300px'; // Optional: set a max width for videos
                video.style.margin = '10px'; // Optional: add some margin for better spacing
                preview.appendChild(video);
            } else {
                const message = document.createElement('p');
                message.textContent = `No preview available for this file type: ${file.name}`;
                preview.appendChild(message);
            }
        });
    });
    

//access files
document.querySelector("#accessimage").addEventListener("click",()=>{
    window.location.href="accessphoto.php";
})
document.querySelector("#accessvideo").addEventListener("click",()=>{
    window.location.href="accessvideo.php";
})
document.querySelector("#accessmusic").addEventListener("click",()=>{
    window.location.href="accessaudio.php";
})
document.querySelector("#accessdocuments").addEventListener("click",()=>{
    window.location.href="accessdocuments.php";
})
document.querySelector('#sharefile').addEventListener("click",()=>{
    window.location.href="accessdocuments.php";
  });
  document.querySelector('#recent').addEventListener("click",()=>{
    window.location.href="accessrecent.php";
  });
  document.querySelector('#sharefile').addEventListener("click",()=>{
    window.location.href="accessdocuments.php";
  });
//   allow multiple file upload in the system

let button=document.querySelector("#chooseFilebtn");

button.addEventListener('click',function() {
    let fileinput=document.createElement('input');
    fileinput.type='file';
    fileinput.name='file[]';
    fileinput.multiple=true;
    fileinput.accept="image/*video/*";
    fileinput.style.display='none';
    
    // appen the file to form
    let form=document.querySelector('#uploadForm');
    form.appendChild(fileinput);
    //trigger the click events
    fileinput.click();
    // handle the file input change
    fileinput.addEventListener('change',function() {
        // submit the form automatically
        if(fileinput.files.length > 0){
            form.submit();
        }
    })
})
