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
                data.reviews.forEach(reviewData => { // Rename review to reviewData
                    const review = document.createElement('div');
                    review.classList.add('review'); 
                    const paragraph = document.createElement('p');
                    paragraph.textContent = reviewData.review;

                    const usernameHeading = document.createElement('h3');
                    usernameHeading.textContent = reviewData.username; 

                    review.appendChild(usernameHeading); 
                    review.appendChild(paragraph); 
                    reviewBox.appendChild(review); 
                    for (let i = 0; i < reviewData.rating; i++) {
                        const star = document.createTextNode('★');
                        paragraph.appendChild(star); 
                    }

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
<section id='review-box'>
</section>
<button id='get-reviews' type="submit" onclick="getReviews()">Get Reviews</button>
<script>
    getReviews();
</script>