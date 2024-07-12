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

const getPosts = async (username=null) => {
    const request = new Request('api/postFeed.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
           username: username
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    console.log(text);
    const data = JSON.parse(text);
    console.log(data);

    let postData = '';
    data.forEach(e => {
        postFiles = '';
        if (e.postFiles.length !== 0) {
            postFiles += '<div class="post-window flex-row">';
            e.postFiles.forEach(file => {
                if (videoFormats.includes(file.split('.').pop()))
                    postFiles += `<video src="uploads/${file}" controls></video>`;
                else
                    postFiles += `<img src="uploads/${file}">`
            });
            postFiles += '</div>';
        }
        postData += `
        <div class="post-frame flex-col">
            <div class="post-box flex-row">
                <div class="post-pfp">
                    <img src="uploads/${e.profilePicture}">
                </div>
                <div class="post-contents flex-col">
                    <div class="post-details flex-col">
                        <div class="post-author"><a href="account.php?user=${e.username}">${e.name}</a></div>
                        <div class="post-time">${e.datetime}</div>
                    </div>
                    <div class="post-caption">${e.caption == null ? '' : e.caption}</div>
                    </div>
                </div>
                ${postFiles}
            <div class="post-comments-box flex-col">
                <div class="post-comments flex-col"></div>
                <div class="comment-controls flex-row">
                    <button class="show-comments flex-row" onclick="showComments(this, ${e.id}, 5)">Show comments<img src="images/icons/arrow-down-icon.png" class="icon"></button>
                    <button class="hide-comments flex-row hidden" onclick="hideComments(this, ${e.id})">Hide comments<img src="images/icons/arrow-up-icon.png" class="icon"></button>
                </div> 
            </div>
            <div class="comment-box flex-row">
                <img src="uploads/${e.requesterPicture}" class="comment-pfp">
                <input type="text" class="comment-input" placeholder="Comment">
                <button type="button" onclick="submitComment(this, ${e.id})"><img src="images/icons/send-icon.png"></button>
            </div>
        </div>
        `;
    });
    postArea.innerHTML = postData;
}

const showComments = async(element, id, limit) => {
    const request = new Request('api/getComments.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: id,
            limit: limit + 1
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    console.log(text);
    const data = JSON.parse(text);
    
    let comments = '';
    if(data.length > 0){
        let e;
        for(let i = 0; i < limit && i < data.length; i++){
            e = data[i];
            comments += `
                <div class="comment-body flex-row">
                    <div class="comment-picture">
                        <img src="uploads/${e.picture}" class="comment-pfp">
                    </div>
                    <div class="comment-content flex-col">
                        <div class="comment-bubble flex-col">
                            <div class="comment-author"><a href="account.php?user=${e.username}">${e.name}</a></div>
                            <div class="comment-text">${e.comment}</div>
                        </div>
                        <div class="comment-time">${e.datetime}</div>
                    </div>
                </div>
            `;
        }
        element.parentNode.previousElementSibling.innerHTML = comments;
        element.innerHTML = 'Show comments<img src="images/icons/arrow-down-icon.png" class="icon">';
    } else {
        element.parentNode.previousElementSibling.innerHTML = `
            <div class="no-comments flex-row">No comments yet...<img src="images/icons/sad-icon.png"></div>
        `;
    }
    if(limit > data.length){
        element.classList.add('hidden');
    } else
        element.setAttribute('onclick', `showComments(this, ${id}, ${limit+5})`);
    element.nextElementSibling.classList.remove('hidden');
}

const hideComments = (element, id) => {
    element.parentNode.previousElementSibling.innerHTML = '';
    element.classList.add('hidden');
    element.previousElementSibling.innerHTML = `Show comments<img src="images/icons/arrow-down-icon.png" class="icon">`;
    element.previousElementSibling.classList.remove('hidden');
    element.previousElementSibling.setAttribute('onclick', `showComments(this, ${id}, 5)`);
}

const submitComment = async (element, id) => {
    const request = new Request('api/submitComment.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: id,
            content: element.previousElementSibling.value
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    console.log(text);

    if (text === 'Success') {
        showComments(element.parentNode.previousElementSibling.children[1].children[0], id, 5);
    }
    element.previousElementSibling.value = '';
}