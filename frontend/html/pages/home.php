<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home</title>
    <script src="../javascript/template.js"></script>
    <script src="../javascript/globalscript.js"></script>
    <style>
        .providers{
            display: flex;
        }
        .providers img{
            max-width: 30px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Home</h2>
    <section>
    <h3>MAKE EACH ICON AN ADD TO MY PROVIDERS BUTTON</h3>
    <div class="providers" id="providerList">
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
                    var container = document.getElementById("providerList");
                    container.appendChild(imageElement);
                }
            } else {
                // Display error message
                console.log('oh');
            }
        })
    });
</script>
</body>
</html>
