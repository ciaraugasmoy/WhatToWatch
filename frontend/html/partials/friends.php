<style scoped>

section{
  width:max-content;
}
#friends{
   width:max-content;
   border: 2px #0075DE solid;
   border-radius: 10px;
   max-height:300px;
   overflow:scroll;
}
#friends:empty{
    display: none;
}
.friend{
  display: flex;
  width:300px;
  padding: 10px 10px;
  justify-content:space-around;
  box-sizing:border-box;
  border: 1px #0075DE20 solid;
  transition: 300ms;
}
.friend>*{
  display:inline-block;
}
.friend .options{
    margin-left: auto;
  display:flex;
  flex-direction:column;
  gap: 5px;
}
.friend button{
    margin-left: auto;
  border: none;
  border-radius:5px;
  width:60px;
  font-family: 'Courier New', monospace;
}
.friend>a{
  color:white;
}
.friend[data-status='pending']{
  background:#0075DE80; 
  transition: 300ms;
}
.friend[data-status='requested']{
  background:#0075DE50; 
  transition: 300ms;
}
form{
  display: flex;
  max-width:100%;
  gap:10px;
  align-content: center;
  margin-bottom:10px;
}
input[type=text]{
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
<h3>Friends</h3>
<section>
 <form id="friend-form">
    <input type="text" id="friend_username" name="friend_username" placeholder="Add friend by Username" required>
    <input type='submit' value='Send Request'>
 </form>
<div id='friends'></div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Perform a fetch request to login.php
    fetch('../requests/get_friends.php', {   
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            for (const element of data.friend_list) {
                console.log(element);
                var friendElement = document.createElement("div");
                friendElement.setAttribute('data-friend-id', element['friend_id']); 
                friendElement.setAttribute('data-status', element['status']); 
                friendElement.classList.add('friend');

                var nameLink = document.createElement('a');
                nameLink.setAttribute("href", "profile.php?username=" + element['friend_name']);
                nameLink.textContent=element['friend_name'];
                friendElement.appendChild(nameLink);
                
                switch (element['status']) {
                case 'requested':
                    var cancelButton = document.createElement("button");
                    cancelButton.setAttribute('onclick', 'deleteFriendRequest(this)');
                    cancelButton.setAttribute('data-friend-name', element['friend_name']);
                    cancelButton.textContent='cancel';
                    friendElement.appendChild(cancelButton);
                    break;
                case 'pending':
                    var optionGroup = document.createElement("div");
                    optionGroup.classList.add('options');

                    var acceptButton = document.createElement("button");
                    acceptButton.textContent='accept';
                    acceptButton.setAttribute('data-friend-name', element['friend_name']);
                    acceptButton.setAttribute('onclick', 'acceptFriendRequest(this)');

                    var rejectButton = document.createElement("button");
                    rejectButton.textContent='reject';
                    rejectButton.setAttribute('data-friend-name', element['friend_name']);
                    rejectButton.setAttribute('onclick', 'deleteFriendRequest(this)');

                    optionGroup.appendChild(rejectButton);
                    optionGroup.appendChild(acceptButton);
                    friendElement.appendChild(optionGroup)
                    break;
                default:
                    var removeButton = document.createElement("button");
                    removeButton.textContent='remove';
                    removeButton.setAttribute('data-friend-name', element['friend_name']);
                    removeButton.setAttribute('onclick', 'deleteFriendRequest(this)');
                    friendElement.appendChild(removeButton);
                }
                

                var container = document.getElementById("friends");
                container.appendChild(friendElement);
            }
        } else {
            // Display error message
            console.log('error getting friendlist');
        }
    })
});
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('friend-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        // Perform a fetch request to login.php
        fetch('../requests/send_friend_request.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                console.log('sent request');
                var friendElement = document.createElement("div");
                friendElement.setAttribute('data-status', 'pending'); 
                friendElement.classList.add('friend');

                var nameLink = document.createElement('a');
                nameLink.textContent= formData.get('friend_username');
                friendElement.appendChild(nameLink);

                var cancelButton = document.createElement("button");
                cancelButton.textContent='cancel';
                cancelButton.setAttribute('onclick', 'deleteFriendRequest(this)');
                friendElement.appendChild(cancelButton);

                var container = document.getElementById("friends");
                container.prepend(friendElement);

            } else {
                // Display error message
                console.log(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
function deleteFriendRequest(button) {
    const friendName = button.getAttribute('data-friend-name');
    const formData = new FormData();
    formData.append('friend_username', friendName);
    fetch('../requests/delete_friend_request.php', {
        method: 'POST',
        body: formData // Sending data as FormData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            console.log('Friend request deleted');
            if (button.parentNode.classList.contains('options')) {
                button.parentNode.parentNode.remove();
            } else {
                button.parentNode.remove();
            }
        } else {
            console.log(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function acceptFriendRequest(button) {
    const friendName = button.getAttribute('data-friend-name');
    const formData = new FormData();
    formData.append('friend_username', friendName);
    fetch('../requests/accept_friend_request.php', {
        method: 'POST',
        body: formData // Sending data as FormData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            console.log('Friend request accepted');
            if (button.parentNode.classList.contains('options')) {
                button.parentNode.parentNode.setAttribute('data-status', 'accepted');
                
                var removeButton = document.createElement("button");
                removeButton.textContent='remove';
                removeButton.setAttribute('data-friend-name', friendName);
                removeButton.setAttribute('onclick', 'deleteFriendRequest(this)');
                button.parentNode.parentNode.append(removeButton);
                button.parentNode.remove();
            } else {
                button.parentNode.setAttribute('data-status', 'accepted');
            }
        } else {
            console.log(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>
