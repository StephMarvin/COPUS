document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("show-password");

    checkbox.addEventListener("click", function() {
        const passwordInput = document.getElementById("password");

        if(passwordInput.type === "password") {
            passwordInput.type = "text";
        }

        else {
            passwordInput.type = "password";
        }
    });

    const hideNotification = () => {
        setTimeout(() => {
            const notification = document.getElementById("notification")
    
                if(notification) {
                    notification.classList.add("fade-out");
                    setTimeout(() => {
                        notification.style.display = "none";
                    }, 1000)
                }
            }, 3000);
    }

    hideNotification();
})