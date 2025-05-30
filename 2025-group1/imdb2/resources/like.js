document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('like-button');
    const dislikeButton = document.getElementById('dislike-button');
    const loginButton = document.getElementById('rate-login-prompt');
    const likeCountElement = document.getElementById('like-count');


    function getIdAndTypeFromUrl() {
        const path = window.location.pathname;
        // Example: /INFO263-Group-Assignment/2025-group1/imdb2/title/tt1234567.php
        // or:      /INFO263-Group-Assignment/2025-group1/imdb2/person/nm1234567.php
        const parts = path.split('/');
        const phpFile = parts[parts.length - 1];
        const id = phpFile.split('.php')[0];
        const type = parts[parts.length - 2];
        return { id, type };
    }

    function sendLikeDislike(ld) {
        const { id, type } = getIdAndTypeFromUrl();
        fetch(`../resources/likes.php?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}&ld=${encodeURIComponent(ld)}&q=23`, {
            method: 'GET',
            credentials: 'same-origin'
        });
    }

    function checkLikeDislikeStatus() {
        const { id, type } = getIdAndTypeFromUrl();
        fetch(`../resources/likes.php?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}&q=4`, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.like) {
                likeButton.textContent = 'Remove Like';
                likeButton.disabled = false;
                dislikeButton.textContent = '👎 Dislike';
                dislikeButton.disabled = true;
            } else {
                likeButton.textContent = '👍 Like';
                likeButton.disabled = false;
                dislikeButton.disabled = false;
            }
            if (data.dislike) {
                dislikeButton.textContent = 'Remove Dislike';
                dislikeButton.disabled = false;
                likeButton.textContent = '👍 Like';
                likeButton.disabled = true;
            } else {
                dislikeButton.textContent = '👎 Dislike';
                dislikeButton.disabled = false;
                likeButton.disabled = false;
            }
        })
        .catch(error => console.error('Error fetching like/dislike status:', error));
    }

    if (likeButton) {
        likeButton.addEventListener('click', function() {
            if (likeButton.textContent === 'Remove Like') {
                sendLikeDislike('unlike');
                console.log('Removed like');
                if (likeCountElement) {
                    const currentCount = parseInt(likeCountElement.textContent, 10);
                    likeCountElement.textContent = currentCount - 1;
                }
                likeButton.textContent = '👍 Like';
                dislikeButton.disabled = false;
            } else {
                sendLikeDislike('like');
                console.log('Added like');
                if (likeCountElement) {
                    const currentCount = parseInt(likeCountElement.textContent, 10);
                    likeCountElement.textContent = currentCount + 1;
                }
                likeButton.textContent = 'Remove Like';
                dislikeButton.disabled = true;
            }
        });
        checkLikeDislikeStatus();
    }

    if (dislikeButton) {
        dislikeButton.addEventListener('click', function() {
            if (dislikeButton.textContent === 'Remove Dislike') {
                sendLikeDislike('undislike');
                console.log('Removed dislike');
                if (likeCountElement) {
                    const currentCount = parseInt(likeCountElement.textContent, 10);
                    likeCountElement.textContent = currentCount + 1;
                }
                dislikeButton.textContent = '👎 Dislike';
                likeButton.disabled = false;
            } else {
                dislikeButton.textContent = 'Remove Dislike';
                sendLikeDislike('dislike');
                console.log('Added dislike');
                if (likeCountElement) {
                    const currentCount = parseInt(likeCountElement.textContent, 10);
                    likeCountElement.textContent = currentCount - 1;
                }
                likeButton.disabled = true;
            }
        });
        checkLikeDislikeStatus();
    }
    if (loginButton) {
        loginButton.addEventListener('click', function() {
            window.location.href = '../signin.php';
        });
    }
});