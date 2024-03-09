document.addEventListener('DOMContentLoaded', function () {
    // Fetch header.php content
    fetch('../partials/header.php')
        .then(response => response.text())
        .then(headerHtml => {
            // Update the body with the fetched header content
            document.body.innerHTML = headerHtml + document.body.innerHTML;
        })
        .catch(error => {
            console.error('Error fetching header.php:', error);
        });
});
document.addEventListener('DOMContentLoaded', function () {
    fetch('../partials/footer.php')
        .then(response => response.text())
        .then(footerHtml => {
            // Update the body with the fetched footer content
            document.body.innerHTML += footerHtml;
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
});