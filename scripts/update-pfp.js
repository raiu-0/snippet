const pfp = document.getElementById('pfp');
const profilePic = document.getElementById('profile-picture');

profilePic.onchange = () => {
    const file = profilePic.files[0];
    if(file.type.split('/')[0] === 'image'){
        if (file.size > 100 * 1024 * 1024) {
            alert('Maximum upload size is 100MB');
            return;
        }
        pfp.src = URL.createObjectURL(file);
    }
}