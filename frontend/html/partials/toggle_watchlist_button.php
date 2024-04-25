<style scoped>
    button{
    padding: 10px;
    background-color: #01404a90;
    color:aquamarine;
    border: none;
    transition:300ms;
  border-radius:30px;
}
    button:hover{
    background-color: #01404a;
    transition:300ms;
}
</style>
<script>
const buttons = document.querySelectorAll('.watchlistbtn');


buttons.forEach(button => {
    button.addEventListener('click', function() {
        const movieId = this.getAttribute('data-movie-id');
        const watchlistStatus = this.getAttribute('data-status');

        let newStatus;
        if (watchlistStatus === 'add_to_watchlist') {
            newStatus = 'remove_from_watchlist';
        } else if (watchlistStatus === 'remove_from_watchlist') {
            newStatus = 'add_to_watchlist';
        }

        const url = `../requests/toggle_watchlist.php?movie_id=${encodeURIComponent(movieId)}&watchlist_status=${encodeURIComponent(newStatus)}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Handle the response data here if needed
                console.log(data);
                // Update the button's data-status attribute based on the new status
                this.setAttribute('data-status', newStatus);
                // Optionally update the button text or styling based on the new status
                this.textContent = newStatus === 'add_to_watchlist' ? 'Remove from Watchlist': 'Add to Watchlist' ;
            })
            .catch(error => {
                console.log('Fetch error');
            });
    });
});

</script>