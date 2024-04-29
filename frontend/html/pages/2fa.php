<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <title>2fa Form</title>
</head>
<body>
<?php include '../partials/2faform.php';?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var loginForm = document.getElementById('2fa-form');
            loginForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Get form data
                const formData = new FormData(event.target);

                // Perform a fetch request to login.php
                fetch('../requests/2fa.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Redirect to the success page
                        window.location.href = data.redirect;
                    } else {
                        // Display error message
                        alert(data.message);
                        console.log(data.message);
                    }
                })
            });
        });
    </script>
</body>
</html>
