<style scoped>
form{
  display: flex;
  max-width:100%;
  gap:10px;
  align-content: center;
  margin-bottom:10px;
}
input[type=text],input[type=password]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  padding: 8px;
}
input[type=submit]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  background-color:#0075DE;
  content:'add friend';
  border-radius: 20px;
  justify-self: right;
  padding: 8px;
}
</style>

<form id="search-form" action="movie_results.php" method="GET">
    <input type="text" id="search_bar" name="query" placeholder="Search Movie" required>
    <input type="hidden" name="page" value="1"> <!-- Add a hidden input for the page -->
    <input type="submit" value="Search">
</form>

<script>
    document.getElementById("search-form").onsubmit = function() {
        var query = document.getElementById("search_bar").value.trim(); // Get the query value
        if (query === "") {
            alert("Please enter a search query.");
            return false; // Prevent form submission if query is empty
        }
        // Append query to action URL
        this.action = "movie_results.php?query=" + encodeURIComponent(query) + "&page=1";
    };
</script>
