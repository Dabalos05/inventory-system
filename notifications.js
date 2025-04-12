document.addEventListener("DOMContentLoaded", function () {
    fetch("notifications.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById("notification-table");
            tableBody.innerHTML = ""; // Clear previous content

            if (data.notifications.length > 0) {
                data.notifications.forEach((notification, index) => {
                    let row = `<tr>
                        <td>${index + 1}</td>
                        <td>${notification}</td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="2">No new notifications</td></tr>`;
            }
        })
        .catch(error => console.error("Error loading notifications:", error));
});
