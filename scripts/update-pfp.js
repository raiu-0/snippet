const pfp = document.getElementById('pfp');
const profilePic = document.getElementById('profile-picture');

const acceptedImageFormats = [
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
    'webp'
];

profilePic.onchange = () => {
    const file = profilePic.files[0];
    const info = file.type.split('/');
    if(!(info[0] === 'image' && acceptedImageFormats.includes(info[1]))){
        alert('Invalid file type.');
        return;
    }
    if (file.size > 50 * 1024 * 1024) {
        alert('Maximum upload size is 50MB');
        return;
    }
    pfp.src = URL.createObjectURL(file);
}