const dragger = document.getElementById('dragger');
const dragger_text = document.getElementById('dragger_text');
const browseFileBtn = document.getElementById('browseFileBtn');
const fileSelectorInput = document.getElementById('fileSelectorInput');
const fileUploadInput = document.getElementById('fileUploadInput');
const filename = document.getElementById('filename')

const browseFileHandler = () => {
    fileSelectorInput.click()
}

fileSelectorInput.addEventListener('change', function (e){
    file = this.files[0];
    uploadFileHandler(file);
    fileUploadInput.files = this.files;
});

dragger.addEventListener('dragover', (e) => {
    e.preventDefault();
    dragger_text.textContent = "Release to Upload Image"
});

dragger.addEventListener('dragleave', () => {
    dragger_text.textContent = "Drag and Drop Image"
});

dragger.addEventListener('drop', (e) => {
    e.preventDefault();
    file = e.dataTransfer.files[0];
    uploadFileHandler(file);
    fileUploadInput.files = e.dataTransfer.files;
});

const deleteHandler = () => {
    const initial = `<div class="imageicon"><i class="fa-solid fa-image"></i></div>
    <h2 id="dragger_text">Drag and drop image</h2>
    <h3>OR</h3>
    <button type="button" class="btn btn-secondary browseFileBtn" onclick="browseFileHandler()">Browse File</button>
    <input type="file" hidden id="fileSelectorInput">`;
    dragger.innerHTML = initial;
    dragger.classList.remove('active');
    filename.innerHTML = "";
    fileUploadInput.files = "";
}

 const uploadFileHandler = (file) => {
     const validFileExtensions = ['image/jpeg','image/jpg','image/png'];

     if(validFileExtensions.includes(file.type)){
         const fileReader = new FileReader();

         fileReader.onload = () => {
             const fileURL = fileReader.result;
             const imageTag = `<img src=${fileURL} alt="" />`
             dragger.innerHTML = imageTag;
             const imageDetails = `<p>${file.name.split(".")[0]}<p> <i class="fa-solid fa-trash-can fa-lg" onclick="deleteHandler()"></i>`
             filename.innerHTML = imageDetails;

            //  fileSelectorInput.files = [file];
            const fileList = new DataTransfer();
            fileList.items.add(file);

            // Assign the FileList to the file input
            fileSelectorInput.files = fileList.files;
         }

         fileReader.readAsDataURL(file);
         dragger.classList.add('active');
     }
 }

