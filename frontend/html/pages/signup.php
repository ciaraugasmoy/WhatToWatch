<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <title>Signup Form</title>
</head>
<body>
    <?php include '../partials/signupform.php';?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const signupForm = document.getElementById('signup-form');
            const errorMessage = document.getElementById('error-message');

            signupForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(signupForm);

                fetch('../requests/signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Redirect to the specified URL
                        window.location.href = data.redirect;
                    } else {
                        // Display an error message on the same page
                        errorMessage.innerText = data.error || 'Signup failed. Please try again.';
                    }
                })
                .catch(error => {
                    console.error('Error during signup:', error);
                });
            });
        });
    </script>
</body>
</html>
