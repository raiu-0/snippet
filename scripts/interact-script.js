const interactBtn = document.getElementById('interact');
const followerCount = document.getElementById('followers');

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