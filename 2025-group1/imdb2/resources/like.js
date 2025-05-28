document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('like-button');
    const dislikeButton = document.getElementById('dislike-button');

    function getIdAndTypeFromUrl() {
        const path = window.location.pathname;
        // Example: /INFO263/INFO263-Group-Assignment/2025-group1/imdb2/title/tt1234567.php
        // or:      /INFO263/INFO263-Group-Assignment/2025-group1/imdb2/person/nm1234567.php
        const parts = path.split('/');
        const phpFile = parts[parts.length - 1];
        const id = phpFile.split('.php')[0];
        const type = parts[parts.length - 2];
        return { id, type };
    }

    function sendLikeDislike(ld) {
        const { id, type } = getIdAndTypeFromUrl();
        fetch(`../resources/likes.php?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}&ld=${encodeURIComponent(ld)}`, {
            method: 'GET',
            credentials: 'same-origin'
        });
    }

    if (likeButton) {
        likeButton.addEventListener('click', function() {
            if (likeButton.textContent === 'Remove Like') {
                likeButton.textContent = '👍 Like';
                dislikeButton.disabled = false;
                sendLikeDislike('unlike');
            } else {
                likeButton.textContent = 'Remove Like';
                dislikeButton.disabled = true;
                sendLikeDislike('like');
            }
        });
    }

    if (dislikeButton) {
        dislikeButton.addEventListener('click', function() {
            if (dislikeButton.textContent === 'Remove Dislike') {
                dislikeButton.textContent = '👎 Dislike';
                likeButton.disabled = false;
                sendLikeDislike('undislike');
            } else {
                dislikeButton.textContent = 'Remove Dislike';
                likeButton.disabled = true;
                sendLikeDislike('dislike');
            }
        });
    }
});