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
        // Function to load thread via AJAX
        function loadThread() {
            var threadId = <?php echo json_encode($_GET['thread_id']); ?>; // Get thread ID from PHP
            fetch('../requests/load_thread.php?thread_id=' + encodeURIComponent(threadId))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load thread');
                    }
                    return response.json();
                })
                .then(threadData => {
                    var threadContainer = document.getElementById('thread-container');
                    threadContainer.innerHTML = ''; // Clear existing thread content
                    var threadDiv = document.createElement('div');
                    threadDiv.classList.add('thread');
                    threadDiv.innerHTML = '<p>' + threadData.thread.title + '</p>' +
                        '<p>' + threadData.thread.body + '</p>' +
                        '<p>' + threadData.thread.posted_date + '</p>' +
                        '<p>' + threadData.thread.username + '</p>';
                    threadContainer.appendChild(threadDiv);
                })
                .catch(error => {
                    console.error('Error loading thread:', error.message);
                });
        }

        // Load thread when the page loads
        loadThread();
    });

document.addEventListener("DOMContentLoaded", function() {

    loadComments();

    // Function to post comment via AJAX
    function postComment() {
        var threadId = <?php echo json_encode($_GET['thread_id']); ?>; // Get thread ID from PHP
        var commentBody = document.getElementById('comment-body').value; // Get comment body from textarea

        fetch('../requests/post_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                thread_id: threadId,
                body: commentBody
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to post comment');
            }
            return response.json();
        })
        .then(result => {
            if (result.status === 'success') {
                // If the comment is successfully posted, reload comments
                loadComments();
            } else {
                console.error('Failed to post comment:', result.message);
            }
        })
        .catch(error => {
            console.error('Error posting comment:', error.message);
        });
    }
});
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
function postComment() {
    var threadId = <?php echo json_encode($_GET['thread_id']); ?>; // Get thread ID from PHP
    var commentBody = document.getElementById('comment-body').value; // Get comment body from textarea

    var formData = new URLSearchParams();
    formData.append('thread_id', threadId);
    formData.append('body', commentBody);

    fetch('../requests/post_comment.php?thread_id=' + encodeURIComponent(threadId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
    if (result.status === 'success') {
        loadComments();
        document.getElementById('comment-body').value = '';
    } else {
        console.error('Failed to post comment. Server response:', result);
    }
})
.catch(error => {
    console.error('Error posting comment:', error);
});
}

    </script>
</head>
<body>
<div id="thread-container"></div>
<div id="comment-box">
    <form onsubmit="event.preventDefault(); postComment();">
        <textarea id="comment-body" name="body" placeholder="Write your comment here..." required></textarea>
        <button type="submit" name="submit">Submit</button>
    </form>
</div>
<div id="comments-container"></div>
</body>
</html>
