<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home</title>
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <script src="../javascript/globalscript.js"></script>
</head>
<body>
<main style='margin: 10px 0;'>
<style scoped>
.getrecs{
  text-decoration: none;
  padding:20px;
  width:100%;
  color:#0075DE;;
  background: #001;
  text-align: center;
  border-radius: 20px;
  font-weight: 800;
  transition: 300ms;
}
.getrecs:hover{
  color:#0df;
  background: #0075DE;
  transition: 300ms;
}
main{
  display: grid;
  align-content: start;
  row:10px;
  
}
</style>
    <h2>Home</h2>
    <a class='getrecs' href='recommendation_results.php'>Get Recommendations</a>
    <?php include '../partials/search_movie.php'; ?>
</main>
</body>
</html>
