document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('like-button');
    const dislikeButton = document.getElementById('dislike-button');

    if (likeButton) {
        likeButton.addEventListener('click', function() {
            if (likeButton.textContent === 'Remove Like') {
                likeButton.textContent = 'I like this!';
                dislikeButton.disabled = false;
            } else {
                likeButton.textContent = 'Remove Like';
                dislikeButton.disabled = true;
            }
        });
    }

    if (dislikeButton) {
        dislikeButton.addEventListener('click', function() {
            if (dislikeButton.textContent === 'Remove Dislike') {
                dislikeButton.textContent = 'I Dislike This';
                likeButton.disabled = false;
            } else {
                dislikeButton.textContent = 'Remove Dislike';
                likeButton.disabled = true;
            }
        });
    }
});
