<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <script src="../js/globalscript.js"></script>
    <script>
        // Function to load more posts via AJAX
        function loadMorePosts() {
            var offset = document.getElementsByClassName('thread').length; // Calculate offset based on already loaded posts
            var limit = 5; // Number of posts to load
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../requests/load_posts.php?offset=' + offset + '&limit=' + limit +'&sort='+'<?php $sort = isset($_GET['sort']) ?$_GET['sort'] :'recent'; echo $sort ?>', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        var threadsContainer = document.getElementById('threads-container');
                        response.threads.forEach(function (thread) {
                            var threadDiv = document.createElement('div');
                            threadDiv.classList.add('thread');
                            threadDiv.innerHTML = 
                             '<div class="title-bar">'
                                +'<h4>' + thread.title + '</h4>' 
                                +'<p>'+thread.username+ '</p>'
                              +'</div>'
                                +'<p>' + thread.body + '</p>'
                                +'<p> ↑ ' + thread.upvotes + '<br>↓ '+ thread.downvotes +'</p>'
                                +'<a class="see-more-link" href="thread.php?thread_id=' + thread.id + '">' + 'See More' + '</a>';
                            threadsContainer.appendChild(threadDiv);
                        });
                    }
                }
            };
            xhr.send();
        }
    </script>
</head>
<style scoped>
h2,h3{
  text-align: center;
  margin:10px 0 10px;
}
#threads-container{
  justify-self: center;
}
.thread{
    justify-self: center;
    margin:10px 0px 20px;
    border-radius: 20px;
    background: linear-gradient(#000000 5px,#000000DD);
    width:500px;
    max-width: 100vw;
    padding: 20px;
    justify-self: center;
    justify-content: center;
    justify-items: center;
    gap:10px;
    display: grid;
}
.title-bar{
  width:100%;
  background:#01404a99;
  padding: 10px 30px;
  border-radius: 40px;
}
.thread .title-bar h4{
  color:aquamarine;
  float:left;
}
.thread .title-bar p{
  color:grey;
  float: right;
}
.thread a{
  justify-self: end;
    padding: 10px;
    background-color:  #01404a99;
    color:aquamarine;
    border: none;
    transition:300ms;
  text-decoration: none;
  border-radius:5px;
}
.thread a:hover{
    background-color: #01404a;
    transition:300ms;
}
button, input, option, select{
    padding: 10px;
    background-color: #000;
    color:aquamarine;
    border: none;
    transition:300ms;
}
button:hover, input, option, select{
    background-color: #01404a;
    transition:300ms;
}
input:hover{
  background-color: #01404a99;
   transition:300ms;
}
</style>
<body>
    <h2>Explore</h2>
    <h3>Latest Discussions</h3>


    <div id="threads-container">
    <form action="explore.php" method="get">
      <select name="sort" id="sort">
      <option value="recent">recent</option>
      <option value="best">best</option>
      <option value="controversial">controversial</option>
    </select>
    <input type="submit" value="GO">
  </form>
    </div>
    <!-- Button to load more posts -->
    <button onclick="loadMorePosts()">See More</button>
    <script>
  var selectElement = document.getElementById('sort');
  for (var i = 0; i < selectElement.options.length; i++) {
      var option = selectElement.options[i];
      if (option.value === '<?php $sort = isset($_GET['sort']) ?$_GET['sort'] :'recent'; echo $sort ?>') {
          option.selected = true;
          break;
      }
  }

        loadMorePosts();
    </script>
</body>
</html>
