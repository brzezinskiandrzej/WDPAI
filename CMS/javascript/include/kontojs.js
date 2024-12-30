function isclicked()
{
    document.getElementById("podajhaslo").style.display = "block";
}
// kontojs.js

// Funkcja walidacji emaila
function validateEmail() {
    const emailInput = document.getElementById("cemail");
    const errorEl = document.getElementById("emailError");
    const email = emailInput.value.trim();

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email && !emailRegex.test(email)) {
        emailInput.style.border = "1px solid red";
        errorEl.textContent = "Nieprawidłowy format adresu email.";
        errorEl.style.display = "block";
    } else {
        emailInput.style.border = "1px solid green";
        errorEl.textContent = "";
        errorEl.style.display = "none";
    }
}

// Funkcja walidacji nowego hasła
function validateNewPassword() {
    const newPasswordInput = document.getElementById("new_password");
    const errorEl = document.getElementById("newPasswordError");
    const newPassword = newPasswordInput.value;

    if (newPassword && newPassword.length < 6) {
        newPasswordInput.style.border = "1px solid red";
        errorEl.textContent = "Nowe hasło musi mieć przynajmniej 6 znaków.";
        errorEl.style.display = "block";
    } else {
        newPasswordInput.style.border = "1px solid green";
        errorEl.textContent = "";
        errorEl.style.display = "none";
    }
}
