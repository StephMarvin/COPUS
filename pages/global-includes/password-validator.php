<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div id="password-validation">
    
    <h6>Password must meet the following criteria:</h5>
    
    <ul>
        <li id="length" class="invalid text-danger"><i class="bi bi-x"></i>Minimum <b>8 characters</b></li>
        <li id="capital" class="invalid text-danger"><i class="bi bi-x"></i>At least <b>one uppercase letter</b></li>
        <li id="letter" class="invalid text-danger"><i class="bi bi-x"></i>At least <b>one lowercase letter</b></li>
        <li id="number" class="invalid text-danger"><i class="bi bi-x"></i>At least <b>one number</b></li>
        <li id="special" class="invalid text-danger"><i class="bi bi-x"></i>At least <b>one special character</b> (e.g., @, #, $, etc.)</li>
    </ul>
    
    <div id="formSuccess" class="text-success mb-2"></div>
    
</div>

<script>
    function updateValidation() {
        const password = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        const isLengthValid = password.length >= 8;
        document.getElementById('length').classList.toggle('text-success', isLengthValid);
        document.getElementById('length').classList.toggle('text-danger', !isLengthValid);
        document.getElementById('length').querySelector('i').classList.toggle('bi-check', isLengthValid);
        document.getElementById('length').querySelector('i').classList.toggle('bi-x', !isLengthValid);

        const hasUppercase = /[A-Z]/.test(password);
        document.getElementById('capital').classList.toggle('text-success', hasUppercase);
        document.getElementById('capital').classList.toggle('text-danger', !hasUppercase);
        document.getElementById('capital').querySelector('i').classList.toggle('bi-check', hasUppercase);
        document.getElementById('capital').querySelector('i').classList.toggle('bi-x', !hasUppercase);

        const hasLowercase = /[a-z]/.test(password);
        document.getElementById('letter').classList.toggle('text-success', hasLowercase);
        document.getElementById('letter').classList.toggle('text-danger', !hasLowercase);
        document.getElementById('letter').querySelector('i').classList.toggle('bi-check', hasLowercase);
        document.getElementById('letter').querySelector('i').classList.toggle('bi-x', !hasLowercase);

        const hasNumber = /\d/.test(password);
        document.getElementById('number').classList.toggle('text-success', hasNumber);
        document.getElementById('number').classList.toggle('text-danger', !hasNumber);
        document.getElementById('number').querySelector('i').classList.toggle('bi-check', hasNumber);
        document.getElementById('number').querySelector('i').classList.toggle('bi-x', !hasNumber);

        const hasSpecial = /[@$!%*?&]/.test(password);
        document.getElementById('special').classList.toggle('text-success', hasSpecial);
        document.getElementById('special').classList.toggle('text-danger', !hasSpecial);
        document.getElementById('special').querySelector('i').classList.toggle('bi-check', hasSpecial);
        document.getElementById('special').querySelector('i').classList.toggle('bi-x', !hasSpecial);

        // Confirm Password Validation: Must match the password
        const confirmPasswordError = password !== confirmPassword ? 'Passwords do not match.' : '';
        document.getElementById('confirmPasswordError').textContent = confirmPasswordError;

        // Show or hide success message
        const passwordError = !isLengthValid || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecial
            ? 'Password does not meet the required criteria.'
            : '';

        document.getElementById('passwordError').textContent = passwordError;

        if (passwordError === '' && confirmPasswordError === '') {
            document.getElementById('formSuccess').textContent = 'Password successfully validated!';
            document.getElementById('formSuccess').classList.remove('d-none');
        } else {
            document.getElementById('formSuccess').classList.add('d-none');
        }
    }

    document.getElementById('new-password').addEventListener('input', updateValidation);
    document.getElementById('confirm-password').addEventListener('input', updateValidation);

</script>