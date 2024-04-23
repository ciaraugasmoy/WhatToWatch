<button id="removeFromWatchlistBtn">Remove from Watchlist</button>
<script>
    // JavaScript code to handle button click
    document.getElementById('removeFromWatchlistBtn').addEventListener('click', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const movieId = urlParams.get('id');
    const url = `../requests/remove_from_watchlist.php?movie_id=${encodeURIComponent(movieId)}`;
    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); 
    })
    .then(data => {
        console.log(data); 
        if (data.status==='success') {
            alert('Item removed from watchlist!');
            window.location.reload();
        } else {
            alert('Failed to remove item from watchlist.');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
});
</script>