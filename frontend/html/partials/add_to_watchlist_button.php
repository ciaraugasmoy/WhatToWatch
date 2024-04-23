<button id="addToWatchlistBtn">Add to Watchlist</button>
<script>
    // JavaScript code to handle button click
    document.getElementById('addToWatchlistBtn').addEventListener('click', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const movieId = urlParams.get('id');
    const url = `../requests/add_to_watchlist.php?movie_id=${encodeURIComponent(movieId)}`;
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
            alert('Item added to watchlist!');
            window.location.reload();
        } else {
            alert('Failed to add item to watchlist.');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
});
</script>