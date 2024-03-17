<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <script src="../javascript/globalscript.js"></script>
    <script>
        // Function to load more posts via AJAX
        function loadMorePosts() {
            var offset = document.getElementsByClassName('thread').length; // Calculate offset based on already loaded posts
            var limit = 5; // Number of posts to load
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../requests/load_posts.php?offset=' + offset + '&limit=' + limit, true);
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
<body>
    <h2>Explore</h2>
    <h3>Latest Discussions</h3>
    <div id="threads-container">
    </div>
    <!-- Button to load more posts -->
    <button onclick="loadMorePosts()">See More</button>
    <script>
        loadMorePosts();
    </script>
</body>
</html>
