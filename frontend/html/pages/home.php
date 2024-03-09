<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home</title>
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <script src="../javascript/globalscript.js"></script>
    <style>
        .providers{
            display: flex;
            gap: 10px;
        }
        .providers img{
            max-width: 30px;
            border-radius: 5px;
        }
        .providers img:hover{
            border: 10px solid cyan;
        }
    </style>
</head>
<body>
    <h2>Home</h2>
    <section>
    <h3>Popular Streaming Services</h3>
    <div class="providers" id="curatedProviderList">
    </div>
    </section>
    <button id="logoutButton">Logout BUTTON</button>
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
    // Check if the clicked element is an image
    if (event.target.tagName === 'IMG') {
        var altText = event.target.alt;
        console.log('Clicked on image with alt: ' + altText);
    }
});

</script>
</body>
</html>
