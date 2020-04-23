(function (){
    const elMessages = document.getElementById('messages');
    const elMembers = document.getElementById('members');
    const elText = document.getElementById('messageText');
    const elSendButton = document.getElementById('sendButton');

    function setMessages(data) {
        elMessages.innerHTML = data;
        elMessages.scrollTop = elMessages.scrollHeight;
    }
    function get(url, callback) {
        const request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                callback(this.responseText);
            }
        };
        request.open("GET", url, true);
        request.send();
    }
    function update()
    {
        get('/messages', setMessages);
        get('/members', (htmlText) => { elMembers.innerHTML = htmlText });
    }
    if (elMessages) {
        setInterval(update, 3000);
        update();
        elText.focus();
    }
    const elSendForm = document.getElementById('sendForm');
    if (elSendForm) {
        elSendForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    setMessages(this.responseText);
                    elText.disabled = false;
                    elSendButton.disabled = false;
                    elText.value = '';
                    elText.focus();
                }
            };
            request.open("POST", elSendForm.action, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(elText.name + "=" + encodeURI(elText.value));
            elText.disabled = true;
            elSendButton.disabled = true;
        })
    }
})();
