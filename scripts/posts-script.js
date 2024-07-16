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

const getPosts = async (viewer, username = null) => {
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
        postData += constructPost(e, viewer);
    });
    postArea.innerHTML = postData;
}

const constructPost = (e, viewer, jsonencoded = true, enablehyperlink = true) => {
    if (!jsonencoded) {
        e = JSON.parse(e);
    }
    let postFiles = '';
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
    postData = `
        <div class="post-frame flex-col">
            <div class="post-box flex-row">
                <div class="post-pfp">
                    <img src="uploads/${e.profilePicture}">
                    <a href="account.php?user=${e.username}" class="post-prof-name"><span class="button-link"></span></a>
                </div>
                <div class="post-contents flex-col">
                    <div class="post-header flex-row">
                        <div class="post-details flex-col">
                            <div class="post-author flex-row">
                                <span class="post-prof-name">${e.name}</span>
                                <span class="post-prof-username">@${e.username}</span>
                                <a href="account.php?user=${e.username}" class="post-prof-name"><span class="button-link"></span></a>
                            </div>
                            <div class="post-time">${e.datetime}</div>
                        </div>
                        <div class="post-controls-frame flex-row">`;
    if (e.username === viewer)
        postData += `
            <button class="flex-row delete-post-btn" onclick="deletePost(this, ${e.id})">
                <img src="images/icons/x-icon.png">
                Delete
            </button>`;
    postData += enablehyperlink ? `<button onclick="toPostPage(${e.id})"><img src="images/icons/fullscreen-icon.png"></button>` : '';
    postData += `
                </div>
                    </div>
                    <div class="post-caption">${e.caption == null ? '' : e.caption}</div>
                    </div>
                </div>
                ${postFiles}
                <div class="post-interactions-box flex-col">
                    <div class="post-comments flex-col"></div>
                    <div class="flex-row">
                        <button class="like-post flex-row" onclick="likePost(this, ${e.id})"><img src="images/icons/heart-` + (e.liked ? '' : 'no-') + `fill-icon.png"><div>${e.like_count}</div></button>`;
    if (e.hasComments)
        postData += `
                    <button class="show-comments flex-row" onclick="showComments(this, ${e.id}, 4, '${viewer}')">Show comments<img src="images/icons/arrow-down-icon.png" class="icon"></button>
                    <button class="hide-comments flex-row hidden" onclick="hideComments(this, ${e.id}, '${viewer}')">Hide comments<img src="images/icons/arrow-up-icon.png" class="icon"></button>
                `;
    postData += `
                    </div>
                </div>
                <div class="comment-box flex-row">
                    <img src="uploads/${e.requesterPicture}" class="comment-pfp">
                    <input type="text" class="comment-input" placeholder="Comment">
                    <button type="button" onclick="submitComment(this, ${e.id}, '${viewer}')"><img src="images/icons/send-icon.png"></button>
                </div>`;
    postData += `</div>`;
    return postData;
}

const toPostPage = (id) => {
    window.location.href = "post.php?id=" + id;
}

const showComments = async (element, id, limit, viewer) => {
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
    if (data.length > 0) {
        let e;
        for (let i = 0; i < limit && i < data.length; i++) {
            e = data[i];
            delButton = '';
            if(viewer === e.username)
                delButton = `<button class="flex-row" onclick="deleteComment(this, '${e.comment_id}')">
                                <img src="images/icons/x-icon.png">
                                Delete
                            </button>`;
            comments += `
                <div class="comment-body flex-row">
                    <div class="comment-picture">
                        <img src="uploads/${e.picture}" class="comment-pfp">
                        <a href="account.php?user=${e.username}" class="post-prof-name"><span class="button-link"></span></a>
                    </div>
                    <div class="comment-content flex-col">
                        <div class="comment-bubble flex-col">
                            <div class="comment-author"><a href="account.php?user=${e.username}">${e.name}</a></div>
                            <div class="comment-text">${e.comment}</div>
                            
                        </div>
                        <div class="comment-time">${e.datetime}</div>
                    </div>
                    <div class="flex-row comment-del-button-frame">
                        ${delButton}
                    </div>
                </div>
            `;
        }
        element.parentNode.previousElementSibling.innerHTML = comments;
        element.innerHTML = 'View more<img src="images/icons/arrow-down-icon.png" class="icon">';
    } else {
        element.parentNode.previousElementSibling.innerHTML = `
            <div class="no-comments flex-row">No comments yet...<img src="images/icons/sad-icon.png"></div>
        `;
    }
    if (limit > data.length) {
        element.classList.add('hidden');
    } else
        element.setAttribute('onclick', `showComments(this, ${id}, ${limit + 5})`);
    element.nextElementSibling.classList.remove('hidden');
}

const hideComments = (element, id, viewer) => {
    element.parentNode.previousElementSibling.innerHTML = '';
    element.classList.add('hidden');
    element.previousElementSibling.innerHTML = `Show comments<img src="images/icons/arrow-down-icon.png" class="icon">`;
    element.previousElementSibling.classList.remove('hidden');
    element.previousElementSibling.setAttribute('onclick', `showComments(this, ${id}, 5, '${viewer}')`);
}

const submitComment = async (element, id, viewer) => {
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
        const interactionPanel = element.parentNode.previousElementSibling.children[1];
        if (interactionPanel.childElementCount < 3) {
            interactionPanel.innerHTML += `
                <button class="show-comments flex-row hidden" onclick="showComments(this, ${id}, 4, '${viewer}')">Show comments<img src="images/icons/arrow-down-icon.png" class="icon"></button>
                <button class="hide-comments flex-row" onclick="hideComments(this, ${id}, '${viewer}')">Hide comments<img src="images/icons/arrow-up-icon.png" class="icon"></button>
            `;
        }
        showComments(element.parentNode.previousElementSibling.children[1].children[1], id, 4, viewer);
        element.previousElementSibling.value = '';
    } else {
        alert('Cannot post comment.');
    }
}

const likePost = async (element, id) => {
    const request = new Request('api/likePost.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: id
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    console.log(text);
    const data = JSON.parse(text);
    let count = parseInt(element.children[1].innerText);
    console.log(element);
    data.status === 'liked' ? count++ : count--;
    element.innerHTML = `<img src="images/icons/heart-` + (data.status === 'liked' ? '' : 'no-') + `fill-icon.png"><div>${count}</div>`;
}

const deletePost = async (element, id) => {
    const request = new Request('api/deletePost.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: id
        })
    });

    const response = await fetch(request);
    const text = await response.text();

    if (text === 'Success') {
        let child = element.parentNode.parentNode.parentNode.parentNode.parentNode;
        child.parentNode.removeChild(child);
    } else
        alert('Post cannot be removed');
}

const deleteComment = async (e, id) => {
    const request = new Request('api/deleteComment.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            id: id
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    if (text === 'Success') {
        let child = e.parentNode.parentNode;
        let parent = child.parentNode;
        parent.removeChild(child);
        if(parent.childElementCount === 0){
            let interactioncontrols = parent.nextElementSibling;
            interactioncontrols.removeChild(interactioncontrols.children[1]);
            interactioncontrols.removeChild(interactioncontrols.children[1]);
        }
    } else
        alert('Post cannot be removed');
}