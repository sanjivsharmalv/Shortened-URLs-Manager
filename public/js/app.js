/*
document.addEventListener('DOMContentLoaded', function () {
    // Add click event listener to all elements with class "delete-url"
    document.querySelectorAll('.delete-url').forEach(function (element) {
        element.addEventListener('click', function (event) {
            //event.preventDefault();

            // Get data attributes
            let urlDescription = this.getAttribute("data-description");

            let fullUrl = this.getAttribute("data-full-url");
            let shortUrl = this.getAttribute("data-shortened-url");
            let id = this.getAttribute("data-url-id");

            // Update modal content

            document.getElementById("modal-short-description").textContent = urlDescription;
            document.getElementById("modal-full-url").textContent = fullUrl;
            document.getElementById("modal-shortened-url").textContent = shortUrl;

            // Update href attribute of link
            let hrefLink = "/shortenedurls/delete/" + id;
            document.getElementById("url-submit").setAttribute("href", hrefLink);

        });
    });
});
*/

    document.getElementById('cancel').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default anchor behavior
        document.getElementById('messageBox').style.display = 'none'; // Hide the message box
    });