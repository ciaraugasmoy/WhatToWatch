<section id='review-box' class='reviews'>
</section>

<input type="hidden" id="movie_id" value="<?php $movie_id?>"> <!-- Assuming movie_id is set to 1 -->
<button id='get-reviews' type="submit" onclick="getReviews()">Get Reviews</button>

<script>
    function getReviews() {
        const reviewBox = document.getElementById('review-box');

        fetch('../requests/get_reviews.php', {
            method: 'POST',
            credentials: 'include' // Include cookies in the request
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('success');
                data.reviews.forEach(review => {
                    const paragraph = document.createElement('p');
                    paragraph.textContent = review.review;
                    reviewBox.appendChild(paragraph);
                });
            }
            else {
                console.log('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
