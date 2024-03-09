document.addEventListener('DOMContentLoaded', function () {
    // Fetch header.php content
    fetch('../partials/header.php')
        .then(response => response.text())
        .then(headerHtml => {
            // Append the fetched header content to the beginning of the body
            document.body.insertAdjacentHTML('afterbegin', headerHtml);
        })
        .catch(error => {
            console.error('Error fetching header.php:', error);
        });

    // Fetch footer.php content
    fetch('../partials/footer.php')
        .then(response => response.text())
        .then(footerHtml => {
            // Append the fetched footer content to the end of the body
            document.body.insertAdjacentHTML('beforeend', footerHtml);
        })
        .catch(error => {
            console.error('Error fetching footer.php:', error);
        });
});
