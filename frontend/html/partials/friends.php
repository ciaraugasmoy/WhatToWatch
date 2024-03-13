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
.friend{
  display: flex;
  width:300px;
  padding: 10px 10px;
  justify-content:space-around;
  box-sizing:border-box;
  border: 1px #0075DE20 solid;
}
.friend>*{
  display:inline-block;
}
.friend .options{
  display:flex;
  flex-direction:column;
  gap: 5px;
}
.friend button{
  border: none;
  border-radius:5px;
  width:60px;
  font-family: 'Courier New', monospace;
}
.friend>a{
  color:white;
}
.friend[data-status='pending'],.friend[data-status='requested']{
  background:#0075DE80 
}
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
                var friendElement = document.createElement("div");
                friendElement.setAttribute('data-friend-id', element['friend_id']); 
                friendElement.setAttribute('data-status', element['status']); 
                friendElement.classList.add('friend');

                var nameLink = document.createElement('a');
                nameLink.textContent=element['friend_name'];
                friendElement.appendChild(nameLink);

                switch (element['status']) {
                case 'requested':
                    var cancelButton = document.createElement("button");
                    cancelButton.textContent='cancel';
                    friendElement.appendChild(cancelButton);
                    break;
                case 'pending':
                    var optionGroup = document.createElement("div");
                    optionGroup.classList.add('options');
                    var acceptButton = document.createElement("button");
                    var rejectButton = document.createElement("button");
                    acceptButton.textContent='accept';
                    rejectButton.textContent='reject';
                    optionGroup.appendChild(rejectButton);
                    optionGroup.appendChild(acceptButton);
                    friendElement.appendChild(optionGroup)
                    break;
                default:
                    var removeButton = document.createElement("button");
                    removeButton.textContent='remove';
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

</script>
