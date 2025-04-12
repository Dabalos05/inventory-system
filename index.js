$(document).ready(function () {
    let isSoundPlaying = false;
    let notificationSound;

    // Preload the notification sound to handle browser autoplay restrictions
    function preloadNotificationSound() {
        if (typeof Audio !== "undefined") {
            notificationSound = new Audio('sounds/notification_sound.mp3');
            notificationSound.volume = 0; // Mute initially for preloading
            notificationSound.play().catch(error => {
                console.error('Error preloading sound:', error);
            });
        } else {
            console.error('Audio is not supported in this browser.');
        }
    }

    // Function to play the notification sound
    function playNotificationSound() {
        if (isSoundPlaying) return; // Prevent multiple simultaneous plays

        if (notificationSound) {
            isSoundPlaying = true; // Set flag to indicate sound is playing
            notificationSound.currentTime = 0; // Rewind to the start
            notificationSound.volume = 1; // Unmute the sound
            notificationSound.play().catch(error => {
                console.error('Error playing sound:', error);
                isSoundPlaying = false; // Reset flag on error
            });

            notificationSound.addEventListener('ended', () => {
                isSoundPlaying = false; // Reset flag when sound ends
            });
        } else {
            console.error('Notification sound is not preloaded.');
        }
    }

    // Function to show the low stock alert
    function showLowStockAlert() {
        const alertBox = document.createElement('div');
        alertBox.style.position = 'fixed';
        alertBox.style.top = '50%';
        alertBox.style.left = '50%';
        alertBox.style.transform = 'translate(-50%, -50%)';
        alertBox.style.backgroundColor = 'white';
        alertBox.style.padding = '20px';
        alertBox.style.borderRadius = '8px';
        alertBox.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        alertBox.style.zIndex = '1000';
        alertBox.style.width = '300px';
        alertBox.style.display = 'flex';
        alertBox.style.flexDirection = 'column';
        alertBox.style.alignItems = 'center';
        alertBox.style.justifyContent = 'center';

        const warningIcon = document.createElement('span');
        warningIcon.innerHTML = '&#9888;';
        warningIcon.style.fontSize = '30px';
        warningIcon.style.color = 'orange';
        warningIcon.style.marginRight = '10px';

        const message = document.createElement('p');
        message.textContent = 'Low Stock Alert!';
        message.style.color = 'black';
        message.style.display = 'inline-block';
        message.style.verticalAlign = 'middle';
        message.style.marginTop = '18px';

        const okButton = document.createElement('button');
        okButton.textContent = 'OK';
        okButton.style.marginTop = '15px';
        okButton.style.padding = '10px 20px';
        okButton.style.backgroundColor = '#1e90ff';
        okButton.style.color = 'white';
        okButton.style.border = 'none';
        okButton.style.borderRadius = '4px';
        okButton.style.cursor = 'pointer';

        okButton.addEventListener('click', () => {
            alertBox.remove();
        });

        const messageContainer = document.createElement('div');
        messageContainer.style.display = 'flex';
        messageContainer.style.alignItems = 'center';

        messageContainer.appendChild(warningIcon);
        messageContainer.appendChild(message);

        alertBox.appendChild(messageContainer);
        alertBox.appendChild(okButton);

        document.body.appendChild(alertBox);
    }

    // Load notifications every 10 seconds
    function loadNotifications() {
        $.ajax({
            url: "fetch_notifications.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                let notificationContainer = $("#notification-container");
                notificationContainer.empty();

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        notificationContainer.append(`<li>${notification}</li>`);
                    });
                    playNotificationSound(); // Play sound when there are new notifications
                } else {
                    notificationContainer.append("<li>No new notifications</li>");
                }
            }
        });
    }

    setInterval(loadNotifications, 10000); // Poll for notifications every 10 seconds
    loadNotifications(); // Initial load

    // Fetch low stock products
    $.ajax({
        url: 'fetch_low_stock.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.lowStockProducts && data.lowStockProducts.length > 0) {
                playNotificationSound(); // Play sound immediately
                showLowStockAlert(); // Show alert after playing sound
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching low stock:', error);
        }
    });

    // Profile dropdown toggle functionality
    $(".profile-toggle").on("click", function (e) {
        e.preventDefault();
        $(this).next(".profile-dropdown").slideToggle();
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".profile-item").length) {
            $(".profile-dropdown").slideUp();
        }
    });

    // Preload the notification sound on page load
    preloadNotificationSound();
});