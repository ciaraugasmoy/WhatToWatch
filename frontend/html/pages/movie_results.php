<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Results</title>
</head>
<body>

<h1>Movie Results</h1>

<?php
// Retrieve query and page parameters from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';

// Print out the query and page parameters
echo "<p>Query: $query</p>";
echo "<p>Page: $page</p>";
?>

<!-- Form to navigate to the previous or next page -->
<form action="movie_results.php" method="GET">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    <input type="hidden" name="page" value="<?php echo max(1, $page - 1); ?>"> <!-- Decrease page number -->
    <button type="submit" <?php echo ($page <= 1) ? 'disabled' : ''; ?>>Back</button>
</form>

<form action="movie_results.php" method="GET">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    <input type="hidden" name="page" value="<?php echo $page + 1; ?>"> <!-- Increase page number -->
    <button type="submit">Next</button>
</form>

</body>
</html>
