document.addEventListener("DOMContentLoaded", function() {
    
    document.getElementById("show-passwords").addEventListener("click", function() {
        const newPassword = document.getElementById("new-password");
        const confirmPassword = document.getElementById("confirm-password");

        if(newPassword.type === "password") {
            newPassword.type = "text";
            confirmPassword.type = "text";
        }

        else {
            newPassword.type = "password";
            confirmPassword.type = "password";
        }
    })
})