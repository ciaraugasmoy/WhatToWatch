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
    function checkVoteStatus() {
            var threadId = <?php echo json_encode($_GET['thread_id']); ?>;
            fetch('../requests/get_vote.php?thread_id=' + encodeURIComponent(threadId))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to get vote status');
                    }
                    return response.json();
                })
                .then(voteData => {
                    // Check the vote status and update icons accordingly
                    var upvoteIcon = document.querySelector('.arrow.up');
                    var downvoteIcon = document.querySelector('.arrow.down');

                    if (voteData.status === 'success') {
                        console.log(voteData.vote);
                        if (voteData.vote === 'upvote') {
                            upvoteIcon.classList.add('active');
                        }else if (voteData.vote === 'downvote') {
                            downvoteIcon.classList.add('active');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error getting vote status:', error.message);
                });
        }
    function vote(action) {
        var threadId = <?php echo json_encode($_GET['thread_id']); ?>;
        var upvoteIcon = document.querySelector('.arrow.up');
        var downvoteIcon = document.querySelector('.arrow.down');
        var isUpvoteActive = upvoteIcon.classList.contains('active');
        var isDownvoteActive = downvoteIcon.classList.contains('active');

        if (action === 'upvote' && isUpvoteActive) {action = 'unset';
        }else if(action ==='downvote' &&isDownvoteActive){action = 'unset';}
        fetch(`../requests/alter_vote.php?thread_id=${encodeURIComponent(threadId)}&vote=${action}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    // Update vote UI based on action
                    if (action === 'upvote') {
                        upvoteIcon.classList.add('active');
                        downvoteIcon.classList.remove('active');
                    } else if (action === 'downvote') {
                        upvoteIcon.classList.remove('active');
                        downvoteIcon.classList.add('active');
                    } else if (action === 'unset' && isUpvoteActive) {
                        upvoteIcon.classList.remove('active');
                    }
                    else if (action === 'unset' && isDownvoteActive) {
                        downvoteIcon.classList.remove('active');
                    }
                } else {
                    console.error('Failed to set vote:', result.message);
                }
            })
            .catch(error => {
                console.error('Error setting vote:', error);
            });
    }

    function checkSubscribeStatus() {
    var threadId = <?php echo json_encode($_GET['thread_id']); ?>;
    fetch(`../requests/subscribe_status.php?thread_id=${encodeURIComponent(threadId)}`)
        .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to get vote status');
                    }
                    return response.json();
                })
        .then(data => {
            if (data.status === 'success') {
                var subscribeButton = document.getElementById('subscribe');
                subscribeButton.textContent = data.subscribed ? 'Unsubscribe' : 'Subscribe';
                console.log(data.status);
            } else {
                console.error('Failed to get subscribe status');
            }
        })
        .catch(error => {
            console.error('Error getting subscribe status rpc:',error);
        });
}

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
                    //threadContainer.innerHTML = ''; // Clear existing thread content
                    var threadDiv = document.createElement('div');
                    threadDiv.classList.add('thread');
                    threadDiv.innerHTML = '<div>' + threadData.thread.title + ' <div id="vote"> <i class="arrow up"> </i> <i class="arrow down"> </i> </div> </div>' +
                        '<p>' + threadData.thread.body + '</p>' +
                        '<p>' + threadData.thread.posted_date + '</p>' +
                        '<p>' + threadData.thread.username + '</p>'+
                        '<p><button id="subscribe">subscribe</button></p>';
                    threadContainer.appendChild(threadDiv);
                    document.querySelector('.arrow.up').addEventListener('click', () => vote('upvote'));
                    document.querySelector('.arrow.down').addEventListener('click', () => vote('downvote'));
                    checkVoteStatus();
                    checkSubscribeStatus();
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
<style scoped>
.thread>*:first-child{
  color:gold;
  font-weight:bold;
}
.thread>p:nth-last-child(2){
  color:grey;
  justify-self: right;
}
.thread>p:nth-last-child(1){
  color:orange;
  justify-self: right;
  font-weight:600;
}
.comment-box>*:first-child{
  color: #0df;
  font-weight:600;
}
#thread-container, #comments-container,#comment-box{
  justify-self: center;
  width: min-content;
}
.thread, .comment-box, form{
    justify-self: center;
    background: linear-gradient(#000000 5px,#000000DD);
    width:500px;
    max-width: 100vw;
    padding: 20px;
    justify-self: center;
    gap:10px;
    display: grid;
    border-bottom: 3px #01404a99 solid;
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
form{
  width:500px;
  max-width:100vw;
  padding:10px;
  display:grid;
  justify-content:center;
  color:white;
  gap:5px;
}
form>*{
  justify-self:center;
}
textarea{
  box-sizing:border-box;
  border: 2px solid #0df;
  border-radius:10px;
  height:100px;
  width:500px;
  max-width:98vw;
  resize: none;
  transition:300ms;
  padding:12px;
}

.arrow {
  border: solid grey;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 3px;
  justify-self:center;
}
.arrow:hover{
	border: solid red;
    border-width: 0 3px 3px 0;
}
.arrow.active{
	border: solid red;
    border-width: 0 3px 3px 0;
}

.up {
  transform: rotate(-135deg);
  -webkit-transform: rotate(-135deg);
}

.down {
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
}
#vote{
 padding:10px 5px;
 display:grid;
 width:30px;
 background-color:cyan;
 gap:5px;
 border-radius:5px;
 float:right;
}
</style>

<div id="thread-container">
</div>
<div id="comment-box">
    <form onsubmit="event.preventDefault(); postComment();">
        <textarea id="comment-body" name="body" placeholder="Write your comment here..." required></textarea>
        <button type="submit" name="submit">Submit</button>
    </form>
</div>
<div id="comments-container"></div>


</body>
</html>
