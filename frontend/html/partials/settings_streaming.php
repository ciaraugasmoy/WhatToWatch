<style scoped>
        .providers{
            display: flex;
            gap: 10px;
            padding: 20px;
            align-items: center;
        }
        .providers img{
            max-width: 60px;
            border-radius: 5px;
        }
        .providers img:hover{
            border: 1px solid cyan;
        }
        .suggestions .providers:hover::after {
            content: '+';
            position:relative;
            top: 15px;
            right: -15px;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #3498db; /* Change the background color as needed */
            color: #fff; /* Change the text color as needed */
            font-size: 20px; /* Adjust the font size as needed */
            text-align: center;
            line-height: 30px;
        }
        #suggestions .providers:hover::after {
            content: '+';
            position:relative;
            top: 15px;
            right: -15px;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #3498db; /* Change the background color as needed */
            color: #fff; /* Change the text color as needed */
            font-size: 20px; /* Adjust the font size as needed */
            text-align: center;
            line-height: 30px;
        }
        #user-services .providers:hover::after {
            content: '-';
            position:relative;
            top: 15px;
            right: -15px;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: red;
            color: #fff;
            font-size: 30px; 
            text-align: center;
            line-height: 30px;
        }
    </style>

<section id='user-services'>
    <h3>Your Streaming Services</h3>
    <div class="providers" id="userProviderList"></div>
</section>
<section id='suggestions'>
    <h3>Popular Streaming Services</h3>
    <div class="providers" id="curatedProviderList"></div>
</section>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Perform a fetch request to login.php
        fetch('../requests/get_curated_watch_providers.php', {   
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                for (const element of data.watch_provider_info) {
                    console.log(element['logo_path']);
                    var logo_path = element['logo_path'];
                    var baseURL = "https://image.tmdb.org/t/p/w500/";
                    var imageElement = document.createElement("img");
                    imageElement.src = baseURL + logo_path;
                    imageElement.alt = element['provider_name'];
                    imageElement.setAttribute('data-provider-id', element['provider_id']); 
                    var container = document.getElementById("curatedProviderList");
                    container.appendChild(imageElement);
                }
            } else {
                // Display error message
                console.log('oh');
            }
        })
    });
    document.getElementById('curatedProviderList').addEventListener('click', function(event) {
    // Check if the clicked element has the 'provider-id' attribute
    if (event.target.hasAttribute('data-provider-id')) {
        var providerId = event.target.getAttribute('data-provider-id');
        console.log('Clicked on element with provider-id: ' + providerId);
        var formData = new FormData();
        formData.append('watch_provider_id', providerId);
        // Make a Fetch request to addProvider.php
        fetch('../requests/set_watch_provider.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === "success") {
                var parentElement = document.getElementById('userProviderList');
                var clickedImage = event.target;
                var clickedImageClone = clickedImage.cloneNode(true);
                clickedImage.parentNode.removeChild(clickedImage); 
                parentElement.appendChild(clickedImageClone);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Perform a fetch request to login.php
        fetch('../requests/get_user_watch_providers.php', {  
            credentials: 'include'  
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                for (const element of data.watch_provider_info) {
                    console.log(element['logo_path']);
                    var logo_path = element['logo_path'];
                    var baseURL = "https://image.tmdb.org/t/p/w500/";
                    var imageElement = document.createElement("img");
                    imageElement.src = baseURL + logo_path;
                    imageElement.alt = element['provider_name'];
                    imageElement.setAttribute('data-provider-id', element['provider_id']); 
                    var container = document.getElementById("userProviderList");
                    container.appendChild(imageElement);
                }
            } else {
                // Display error message
                console.log('oh');
            }
        })
    });
    document.getElementById('userProviderList').addEventListener('click', function(event) {
    // Check if the clicked element has the 'provider-id' attribute
    if (event.target.hasAttribute('data-provider-id')) {
        var providerId = event.target.getAttribute('data-provider-id');
        console.log('Clicked on element with provider-id: ' + providerId);
        var formData = new FormData();
        formData.append('watch_provider_id', providerId);
        // Make a Fetch request to addProvider.php
        fetch('../requests/unset_watch_provider.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === "success") {
                var parentElement = document.getElementById('curatedProviderList');
                var clickedImage = event.target;
                var clickedImageClone = clickedImage.cloneNode(true);
                clickedImage.parentNode.removeChild(clickedImage); 
                parentElement.appendChild(clickedImageClone);
            }
        })
        .catch(error => {
            console.error('Error:', error);         
            
        });
    }
    });

</script>
