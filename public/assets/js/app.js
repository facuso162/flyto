(function () {
    function updateCityDescription(select) {
        var targetId = select.dataset.descriptionTarget;
        var target = targetId ? document.getElementById(targetId) : null;
        var option = select.options[select.selectedIndex];

        if (target && option) {
            target.textContent = option.dataset.description || option.textContent;
        }
    }

    document.querySelectorAll('[data-city-select]').forEach(function (select) {
        updateCityDescription(select);
        select.addEventListener('change', function () {
            updateCityDescription(select);
        });
    });

    var swapButton = document.getElementById('change-destiny-origin');
    if (swapButton) {
        swapButton.addEventListener('click', function () {
            var origin = document.querySelector('select[name="origen"]');
            var destination = document.querySelector('select[name="destino"]');

            if (!origin || !destination) {
                return;
            }

            var originValue = origin.value;
            origin.value = destination.value;
            destination.value = originValue;
            updateCityDescription(origin);
            updateCityDescription(destination);
        });
    }

    document.querySelectorAll('[data-news-carousel]').forEach(function (carousel) {
        var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-news-slide]'));
        var currentIndex = 0;

        if (slides.length === 0) {
            return;
        }

        function showSlide(nextIndex) {
            slides.forEach(function (slide, index) {
                slide.classList.toggle('hidden', index !== nextIndex);
            });
            currentIndex = nextIndex;
        }

        carousel.querySelectorAll('.js-next-news').forEach(function (button) {
            button.addEventListener('click', function () {
                if (slides.length === 0) {
                    return;
                }

                showSlide((currentIndex + 1) % slides.length);
            });
        });
    });

    var recuperarContrasenaEmail = document.getElementById('recuperar-contrasena-email');
    var recuperarContrasenaSubmit = document.getElementById('recuperar-contrasena-submit');

    if (recuperarContrasenaEmail && recuperarContrasenaSubmit) {
        var activeClasses = ['bg-flyto-navy', 'text-flyto-sand'];
        var disabledClasses = ['cursor-not-allowed', 'border', 'border-flyto-ink/10', 'bg-[#e5e4e0]', 'text-flyto-muted'];

        function setRecoverySubmitClasses(classes, enabled) {
            classes.forEach(function (className) {
                recuperarContrasenaSubmit.classList.toggle(className, enabled);
            });
        }

        function updateRecoverySubmitState() {
            var isValid = recuperarContrasenaEmail.checkValidity();

            recuperarContrasenaSubmit.disabled = !isValid;
            setRecoverySubmitClasses(activeClasses, isValid);
            setRecoverySubmitClasses(disabledClasses, !isValid);
        }

        recuperarContrasenaEmail.addEventListener('input', updateRecoverySubmitState);
        recuperarContrasenaEmail.addEventListener('blur', updateRecoverySubmitState);
        updateRecoverySubmitState();
    }

    var recuperarContrasenaToken = document.getElementById('recuperar-contrasena-token');
    var recuperarContrasenaTokenSubmit = document.getElementById('recuperar-contrasena-token-submit');

    if (recuperarContrasenaToken && recuperarContrasenaTokenSubmit) {
        var tokenActiveClasses = ['bg-flyto-navy', 'text-flyto-sand'];
        var tokenDisabledClasses = ['cursor-not-allowed', 'border', 'border-flyto-ink/10', 'bg-[#e5e4e0]', 'text-flyto-muted'];

        function setRecoveryTokenSubmitClasses(classes, enabled) {
            classes.forEach(function (className) {
                recuperarContrasenaTokenSubmit.classList.toggle(className, enabled);
            });
        }

        function updateRecoveryTokenSubmitState() {
            var isValid = recuperarContrasenaToken.value.trim() !== '';

            recuperarContrasenaTokenSubmit.disabled = !isValid;
            setRecoveryTokenSubmitClasses(tokenActiveClasses, isValid);
            setRecoveryTokenSubmitClasses(tokenDisabledClasses, !isValid);
        }

        recuperarContrasenaToken.addEventListener('input', updateRecoveryTokenSubmitState);
        recuperarContrasenaToken.addEventListener('blur', updateRecoveryTokenSubmitState);
        updateRecoveryTokenSubmitState();
    }

    var recuperarContrasenaPassword = document.getElementById('recuperar-contrasena-password');
    var recuperarContrasenaPasswordConfirmation = document.getElementById('recuperar-contrasena-password-confirmation');
    var recuperarContrasenaCambiarSubmit = document.getElementById('recuperar-contrasena-cambiar-submit');

    if (recuperarContrasenaPassword && recuperarContrasenaPasswordConfirmation && recuperarContrasenaCambiarSubmit) {
        var changePasswordActiveClasses = ['bg-flyto-navy', 'text-flyto-sand'];
        var changePasswordDisabledClasses = ['cursor-not-allowed', 'border', 'border-flyto-ink/10', 'bg-[#e5e4e0]', 'text-flyto-muted'];

        function setChangePasswordSubmitClasses(classes, enabled) {
            classes.forEach(function (className) {
                recuperarContrasenaCambiarSubmit.classList.toggle(className, enabled);
            });
        }

        function isStrongRecoveryPassword(password) {
            return password.length >= 8 &&
                /[A-Z]/.test(password) &&
                /[0-9]/.test(password) &&
                /[^a-zA-Z0-9]/.test(password);
        }

        function updateChangePasswordSubmitState() {
            var password = recuperarContrasenaPassword.value;
            var passwordConfirmation = recuperarContrasenaPasswordConfirmation.value;
            var isValid = isStrongRecoveryPassword(password) &&
                passwordConfirmation !== '' &&
                passwordConfirmation === password;

            recuperarContrasenaCambiarSubmit.disabled = !isValid;
            setChangePasswordSubmitClasses(changePasswordActiveClasses, isValid);
            setChangePasswordSubmitClasses(changePasswordDisabledClasses, !isValid);
        }

        recuperarContrasenaPassword.addEventListener('input', updateChangePasswordSubmitState);
        recuperarContrasenaPassword.addEventListener('blur', updateChangePasswordSubmitState);
        recuperarContrasenaPasswordConfirmation.addEventListener('input', updateChangePasswordSubmitState);
        recuperarContrasenaPasswordConfirmation.addEventListener('blur', updateChangePasswordSubmitState);
        updateChangePasswordSubmitState();
    }
})();
