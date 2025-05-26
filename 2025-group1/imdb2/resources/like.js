
document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('like-button');

    if (likeButton) {
        likeButton.addEventListener('click', function() {
            if (likeButton.textContent === 'Unlike') {
                likeButton.textContent = 'I like this!';
            } else {
                likeButton.textContent = 'Unlike';
            }
        });
    }
});
