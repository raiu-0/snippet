window.onload = () => {
    const searchBar = document.getElementById('search-input');
    const resultFrame = document.getElementById('result-frame');
    searchBar.onkeyup = async () => {
        if (searchBar.value.trim() !== '') {
            const request = new Request('api/searchResults.php', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    username: searchBar.value
                })
            });

            const response = await fetch(request);
            const text = await response.text();
            const data = JSON.parse(text);
            if (data.length > 0) {
                resultFrame.innerHTML = '';
                let inner = '';
                data.forEach(element => {
                    inner += `
                <div class="result-box flex-row">
                    <img src="images/icons/default-profile-icon.png" class="search-result-profile">
                        <div class="flex-col">
                            <div class="search-result-name">${element.name}</div>
                            <div class="search-result-username">@${element.username}</div>
                    </div>
                    <a href="account.php?user=${element.username}"><span class="button-link"></span></a>
                </div>
                `;
                });
                resultFrame.innerHTML = inner;
            } else {
                resultFrame.innerHTML = `
                <div class="result-box result-empty flex-row">
                    No results found...
                </div>
                `;
            }
        } else {
            resultFrame.innerHTML = '';
        }
    }
}