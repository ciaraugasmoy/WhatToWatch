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
        document.addEventListener("DOMContentLoaded", function() {
            // Function to load comments via AJAX
            function loadComments() {
                var threadId = <?php echo json_encode($_GET['thread_id']); ?>; // Get thread ID from PHP
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '../requests/load_comments.php?thread_id=' + encodeURIComponent(threadId), true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                var commentsContainer = document.getElementById('comments-container');
                                commentsContainer.innerHTML = ''; // Clear existing comments
                                response.comments.forEach(function(comment) {
                                    var commentBox = document.createElement('div');
                                    commentBox.classList.add('comment-box');
                                    commentBox.innerHTML = '<p>' + comment.username + '</p>' +
                                        '<p>' + comment.body + '</p>' +
                                        '<p>' + comment.posted_date + '</p>';
                                    commentsContainer.appendChild(commentBox);
                                });
                            } else {
                                console.error('Failed to load comments:', response.message);
                            }
                        } else {
                            console.error('Error loading comments. Status code:', xhr.status);
                        }
                    }
                };
                xhr.send();
            }

            // Load comments when the page loads
            loadComments();
        });
    </script>
</head>
<body>
    <div id="comments-container">
        <!-- Comments will be loaded here -->
    </div>
</body>
</html>
