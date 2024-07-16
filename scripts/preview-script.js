const mediaInput = document.getElementById('post-media');
const mediaPreview = document.getElementById('media-preview');
const buttonTemplate = document.getElementById('exit-template');

const acceptedMediaFormats = [
    'apng',
    'avif',
    'gif',
    'jpg',
    'pjp',
    'jfif',
    'jpeg',
    'pjpeg',
    'png',
    'svg',
    'webp',
    'avi',
    'm2p',
    'm4v',
    'mov',
    'mp4',
    'mpg',
    'ts'
];


mediaInput.files = (new DataTransfer()).files;
let filesArray = [];

function removeMedia(button) {
    const preview = button.parentNode;
    const fileName = preview.dataset.fileName;

    mediaPreview.removeChild(preview);
    
    filesArray = filesArray.filter(file => file.name !== fileName);

    const dt = new DataTransfer();
    filesArray.forEach((file) => { dt.items.add(file); });

    mediaInput.files = dt.files;

    if (mediaPreview.childElementCount === 0) {
        mediaPreview.classList.remove('has-content');
    }
    console.log(mediaInput.files);
}

mediaInput.onchange = () => {
    const newFiles = Array.from(mediaInput.files);

    for(let i = 0; i < newFiles.length; i++){
        let fileType = newFiles[i].type.split('/');
        if (!((fileType[0] === 'video' || fileType[0] === 'image') && acceptedMediaFormats.includes(fileType[1]))) {
            alert('Invalid file type.');
            return;
        }
        if (newFiles[i].size > 50 * 1024 * 1024) {
            alert('Maximum upload size is 100MB');
            return;
        }
        if (newFiles[i].name.length > 255){
            alert('Maximum file name length is 255.');
            return;
        }
    }
    newFiles.forEach((file) => {
        if (!filesArray.some(existingFile => existingFile.name === file.name)) {
            let fileType = file.type.split('/')[0];
            filesArray.push(file);
            console.log("ADDD POSTTTTTT");
            let preview = document.createElement('div');
            preview.className = 'media';
            preview.dataset.fileName = file.name;

            let button = buttonTemplate.content.cloneNode(true);
            let child;

            if (fileType === 'video') {
                child = document.createElement('video');
                child.controls = true;

                const reader = new FileReader();
                reader.onload = (e) => {
                    child.src = e.target.result;
                    child.load();
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'image') {
                child = document.createElement('img');
                child.src = URL.createObjectURL(file);
            }

            preview.appendChild(child);
            preview.appendChild(button);
            mediaPreview.appendChild(preview);

            if (!mediaPreview.classList.contains('has-content')) {
                mediaPreview.classList.add('has-content');
            }
        }
    });

    const dt = new DataTransfer();
    filesArray.forEach((file) => { dt.items.add(file); });
    mediaInput.files = dt.files;

    console.log(mediaInput.files);
};