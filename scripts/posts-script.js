const postArea = document.getElementById('post-area');

const videoFormats = [
    'avi',
    'm2p',
    'm4v',
    'mov',
    'mp4',
    'mpg',
    'ts'
];

const getPosts = async () => {
    const request = new Request('api/postFeed.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        }
    });

    const response = await fetch(request);
    const text = await response.text();
    const data = JSON.parse(text);
    console.log(data);
    
    

    let postData = '';
    data.forEach(e => {
        postFiles = '';
        if(e.postFiles.length !== 0){
            postFiles += '<div class="post-window flex-row">';
            e.postFiles.forEach(file=>{
                if(videoFormats.includes(file.split('.').pop()))
                    postFiles += `<video src="uploads/${file}" controls></video>`;
                else
                    postFiles += `<img src="uploads/${file}">`
            });
            postFiles += '</div>';
        }
        postData += `
        <div class="post-box flex-row">
            <div class="post-pfp">
                <img src="images/icons/default-profile-icon.png" class="post-pfp">
            </div>
            <div class="post-contents flex-col">
                <div class="post-details flex-col">
                    <div class="post-author">${e.username}</div>
                    <div class="post-time">${e.datetime}</div>
                </div>
                <div class="post-caption">${e.caption}</div>
                
                    ${postFiles}
                
            </div>
        </div>
        `;
    });
    postArea.innerHTML = postData;
}

getPosts();