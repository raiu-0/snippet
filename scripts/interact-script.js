const interactBtn = document.getElementById('interact');
const followerCount = document.getElementById('followers');
const followingCount = document.getElementById('following');
const popup =  
`   <div class="pop-up-body flex-col">
        <div class="pop-up-title flex-row">
            <span id="pop-up-title-text"></span>
            <button id="pop-up-exit" onclick="exitPopup(this)">
                <img src="images/icons/x-icon.png">
            </button>
        </div>
        <div id="pop-up-results" class="flex-col"></div>
    </div>`;

interactBtn.onclick = async () => {
    const request = new Request('api/interactionManager.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        }
    });

    const response = await fetch(request);
    const text = await response.text();
    const data = JSON.parse(text);
    interactBtn.innerText = data.buttonState;
    followerCount.innerText = 'Followers: ' + data.followerCount;

    if(data.buttonState === "Followed"){
        interactBtn.classList.remove('not-followed');
        interactBtn.classList.add('followed');
    } else {
        interactBtn.classList.remove('followed');
        interactBtn.classList.add('not-followed');
    }

}

const toggleFollow = async (e, target, change=false) => {
    const request = new Request('api/interactionManager.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            username: target
        })
    });

    const response = await fetch(request);
    const text = await response.text();
    const data = JSON.parse(text);
    e.innerText = data.buttonState;
    console.log(data);

    if(data.buttonState === "Followed"){
        e.classList.remove('not-followed');
        e.classList.add('followed'); 
    } else {
        e.classList.remove('followed');
        e.classList.add('not-followed');
    }

    if(change){
        followingCount.innerText = 'Following: ' + data.followingCount;
    }
}

const followHover = (e) => {
    if(e.classList.contains('followed'))
        e.innerText = 'Unfollow';
}

const followNoHover = (e) => {
    if(e.classList.contains('followed'))
        e.innerText = 'Followed';
}

followerCount.onclick = async () => {
    const popupElement = document.createElement('div');
    popupElement.className = "pop-up-bg flex-col";
    popupElement.innerHTML = popup;
    document.body.appendChild(popupElement);
    document.getElementById('pop-up-title-text').innerText = "Followers";
    const request = new Request('api/followInfo.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            mode: 'followers'
        })
    });
    const response = await fetch(request);
    const text = await response.text();
    const data = JSON.parse(text);
    console.log(data);
    let followerStr = '';
    data.forEach((e)=>{
        let btn = '';
        let checkIfFollowed = e.followed;
        console.log(checkIfFollowed);
        if(checkIfFollowed === 'self')
            btn = '';
        else if(checkIfFollowed === true)
            btn = `<button class="followed" onmouseover="followHover(this)" onmouseleave="followNoHover(this)" onclick="toggleFollow(this, '${e.username}')">Followed</button>`;
        else
            btn = `<button class="not-followed" onmouseover="followHover(this)" onmouseleave="followNoHover(this)" onclick="toggleFollow(this, '${e.username}')">Follow</button>`;
        followerStr += `
            <div class="pop-up-result-frame flex-row">
                <div class="pop-up-result-picture">
                    <img src="uploads/${e.picture}">
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                <div class="pop-up-result-name">
                    ${e.name}
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                <div class="pop-up-result-username">
                    @${e.username}
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                ${btn}
            </div>
        `;
    });
    document.getElementById('pop-up-results').innerHTML = followerStr;
}

const followingCountClick = async (update) => {
    const popupElement = document.createElement('div');
    popupElement.className = "pop-up-bg flex-col";
    popupElement.innerHTML = popup;
    document.body.appendChild(popupElement);
    document.getElementById('pop-up-title-text').innerText = "Following";
    const request = new Request('api/followInfo.php', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            mode: 'following'
        })
    });
    const response = await fetch(request);
    const text = await response.text();
    const data = JSON.parse(text);
    console.log(data);
    let followerStr = '';
    data.forEach((e)=>{
        let btn = '';
        let checkIfFollowed = e.followed;
        console.log(checkIfFollowed);
        if(checkIfFollowed === 'self')
            btn = '';
        else if(checkIfFollowed === true)
            btn = `<button class="followed" onmouseover="followHover(this)" onmouseleave="followNoHover(this)" onclick="toggleFollow(this, '${e.username}', ${update})">Followed</button>`;
        else
            btn = `<button class="not-followed" onmouseover="followHover(this)" onmouseleave="followNoHover(this)" onclick="toggleFollow(this, '${e.username}', ${update})">Follow</button>`;
        followerStr += `
            <div class="pop-up-result-frame flex-row">
                <div class="pop-up-result-picture">
                    <img src="uploads/${e.picture}">
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                <div class="pop-up-result-name">
                    ${e.name}
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                <div class="pop-up-result-username">
                    @${e.username}
                    <a href="account.php?user=${e.username}"><span class="button-link"></span></a>
                </div>
                ${btn}
            </div>
        `;
    });
    document.getElementById('pop-up-results').innerHTML = followerStr;
}

const exitPopup = (e) => {
    document.body.removeChild(e.parentNode.parentNode.parentNode);
}