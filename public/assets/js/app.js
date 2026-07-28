(function () {
    'use strict';

    var activeClasses = ['bg-flyto-navy', 'text-flyto-sand'];
    var disabledClasses = ['cursor-not-allowed', 'border', 'border-flyto-ink/10', 'bg-[#e5e4e0]', 'text-flyto-muted'];
    var passwordMessage = 'La contrasena debe tener entre 8 y 40 caracteres, una mayuscula, una minuscula, un numero y un caracter especial.';

    function fieldKey(field) {
        return (field.name || 'campo').replace(/\[\]/g, '').replace(/[\[\].]+/g, '-').replace(/-+$/, '');
    }

    function errorSlot(field) {
        var key = field.dataset.fieldKey || fieldKey(field);
        field.dataset.fieldKey = key;
        var scope = field.closest('label, fieldset, .field-group') || field.parentElement;
        var slot = scope && (
            scope.querySelector('[data-field-error="' + key + '"]') ||
            scope.querySelector('[id="error-' + key + '"]') ||
            scope.querySelector('.form-field-error') ||
            scope.querySelector('p')
        );
        if (!slot && scope && scope.nextElementSibling && scope.nextElementSibling.matches('p, span')) {
            slot = scope.nextElementSibling;
        }
        if (!slot) {
            slot = document.createElement('p');
            if (scope) scope.appendChild(slot);
        }
        slot.dataset.fieldError = key;
        slot.id = slot.id || ('error-' + key);
        slot.classList.add('form-field-error', 'mt-1', 'text-xs', 'leading-5', 'text-red-700');
        slot.setAttribute('role', 'alert');
        slot.setAttribute('aria-live', 'polite');
        return slot;
    }

    function customError(field, form) {
        var value = field.value || '';
        var name = field.name || '';
        var validatesPasswordPolicy = field.dataset.passwordPolicy !== 'none';
        if (field.type === 'password' && name !== 'pago[cvv]' && validatesPasswordPolicy && field.required && value !== '' && !/^.{8,40}$/.test(value)) return passwordMessage;
        if (field.type === 'password' && name !== 'pago[cvv]' && validatesPasswordPolicy && field.required && value !== '' && !/[A-Z]/.test(value)) return passwordMessage;
        if (field.type === 'password' && name !== 'pago[cvv]' && validatesPasswordPolicy && field.required && value !== '' && !/[a-z]/.test(value)) return passwordMessage;
        if (field.type === 'password' && name !== 'pago[cvv]' && validatesPasswordPolicy && field.required && value !== '' && !/[0-9]/.test(value)) return passwordMessage;
        if (field.type === 'password' && name !== 'pago[cvv]' && validatesPasswordPolicy && field.required && value !== '' && !/[^a-zA-Z0-9]/.test(value)) return passwordMessage;
        if (name === 'password_confirmation' && value !== '' && value !== (form.querySelector('[name="password"]') || {}).value) return 'Las contrasenas no coinciden.';
        if (name === 'telefono' || name.indexOf('telefonoContacto') !== -1) {
            if (value !== '' && !/^[0-9]+$/.test(value)) return 'Este telefono solo puede contener digitos.';
        }
        if (name === 'token' && value !== '' && !/^[0-9]{6}$/.test(value)) return 'El codigo debe tener exactamente 6 digitos.';
        if (name === 'pago[vencimiento]' && value !== '' && !/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(value)) return 'El vencimiento debe tener el formato MM/AA.';
        if (name === 'origen' && value !== '' && value === (form.querySelector('[name="destino"]') || {}).value) return 'El origen y el destino deben ser distintos.';
        if (name === 'destino' && value !== '' && value === (form.querySelector('[name="origen"]') || {}).value) return 'El origen y el destino deben ser distintos.';
        if ((name === 'fechaLlegada' || name === 'fechaSalida') && value !== '') {
            var other = form.querySelector('[name="' + (name === 'fechaLlegada' ? 'fechaSalida' : 'fechaLlegada') + '"]');
            if (other && name === 'fechaLlegada' && other.value && value <= other.value) return 'La llegada debe ser posterior a la salida.';
        }
        return '';
    }

    function valid(field, form) {
        return !customError(field, form) && field.checkValidity();
    }

    function updatePasswordRequirements(form) {
        var password = form.querySelector('[name="password"]');
        var confirmation = form.querySelector('[name="password_confirmation"]');
        if (!password) return;

        var value = password.value || '';
        var values = {
            length: Array.from(value).length >= 8 && Array.from(value).length <= 40,
            uppercase: /[A-Z]/.test(value),
            lowercase: /[a-z]/.test(value),
            number: /[0-9]/.test(value),
            special: /[^a-zA-Z0-9]/.test(value),
            match: Boolean(confirmation && value !== '' && confirmation.value !== '' && confirmation.value === value)
        };

        form.querySelectorAll('[data-password-requirement]').forEach(function (item) {
            var fulfilled = Boolean(values[item.dataset.passwordRequirement]);
            var icon = item.querySelector('[data-password-requirement-icon]');
            var label = item.querySelector('span:last-child');
            item.dataset.fulfilled = fulfilled ? 'true' : 'false';
            item.setAttribute('aria-label', (fulfilled ? 'Cumplido: ' : 'Pendiente: ') + (label ? label.textContent : 'requisito'));
            if (icon) icon.textContent = fulfilled ? '\u2713' : '\u00d7';
        });
    }

    function setButtonState(form, enabled) {
        var button = form.querySelector('button[type="submit"], input[type="submit"]');
        if (!button) return;
        button.disabled = !enabled;
        var swapsVisualState = button.dataset.validationStyle === 'swap' || button.classList.contains('bg-[#e5e4e0]');
        if (swapsVisualState) {
            activeClasses.forEach(function (name) { button.classList.toggle(name, enabled); });
            disabledClasses.forEach(function (name) { button.classList.toggle(name, !enabled); });
            return;
        }
        button.classList.toggle('opacity-50', !enabled);
    }

    function updateField(field, form, show) {
        if (field.type === 'hidden') return true;
        var message = customError(field, form);
        if (!message && !field.checkValidity()) message = field.validationMessage;
        var slot = errorSlot(field);
        var touched = field.dataset.touched === '1';
        var shouldShow = (show || touched) && message !== '';
        slot.textContent = shouldShow ? message : '';
        slot.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
        field.setAttribute('aria-invalid', message ? 'true' : 'false');
        if (message) field.setAttribute('aria-describedby', slot.id);
        else field.removeAttribute('aria-describedby');
        return !message;
    }

    function updateForm(form, show) {
        var fields = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));
        var salida = form.querySelector('[name="fechaSalida"]');
        var llegada = form.querySelector('[name="fechaLlegada"]');
        if (salida && llegada && salida.value) llegada.min = salida.value;
        updateFlightDuration(form);
        updatePasswordRequirements(form);
        var allValid = fields.every(function (field) {
            if (field.type === 'hidden') {
                var isId = /(^|\[)(id|.*Id|usuario_id|vueloId|reservaId|aerolineaId)(\]|$)/.test(field.name || '');
                return !isId || /^[1-9][0-9]*$/.test(String(field.value || '').trim());
            }
            return updateField(field, form, show);
        });
        setButtonState(form, allValid);
        return allValid;
    }

    function formatExpiry(field) {
        if (field.name !== 'pago[vencimiento]') return;
        var digits = field.value.replace(/[^0-9]/g, '').slice(0, 4);
        if (digits === '0') {
            digits = '01';
        }
        if (digits.length >= 2) {
            var month = parseInt(digits.slice(0, 2), 10);
            if (month > 12) digits = '12' + digits.slice(2);
            if (month === 0) digits = '01' + digits.slice(2);
        }
        field.value = digits.length >= 2 ? digits.slice(0, 2) + '/' + digits.slice(2) : digits;
    }

    function formatCardNumber(field) {
        if (field.name !== 'pago[numeroTarjeta]') return;
        var digits = field.value.replace(/[^0-9]/g, '').slice(0, 16);
        field.value = (digits.match(/.{1,4}/g) || []).join(' ');
    }

    function updateFlightDuration(form) {
        var salida = form.querySelector('[name="fechaSalida"]');
        var llegada = form.querySelector('[name="fechaLlegada"]');
        var duracion = form.querySelector('[name="duracionHoras"]');
        if (!salida || !llegada || !duracion) return;

        var inicio = new Date(salida.value);
        var fin = new Date(llegada.value);
        var diferencia = fin.getTime() - inicio.getTime();
        if (!salida.value || !llegada.value || !Number.isFinite(diferencia) || diferencia <= 0) {
            duracion.value = '';
            return;
        }

        duracion.value = (Math.round((diferencia / 3600000) * 100) / 100).toFixed(2);
    }

    function clampDiscount(field) {
        if (field.name !== 'descuento' || field.value === '') return;
        var value = Number(field.value);
        if (!Number.isFinite(value)) return;
        field.value = String(Math.min(100, Math.max(0, Math.trunc(value))));
    }

    function requiresDigitsOnly(field) {
        var name = field.name || '';
        return field.type === 'tel' ||
            name === 'telefono' ||
            name.indexOf('telefonoContacto') !== -1 ||
            name === 'token' ||
            name === 'pago[cvv]' ||
            (field.type === 'number' && field.step === '1');
    }

    function sanitizeField(field) {
        if (requiresDigitsOnly(field)) {
            field.value = field.value.replace(/[^0-9]/g, '');
            return;
        }
        if (field.name === 'pago[numeroTarjeta]') {
            formatCardNumber(field);
            return;
        }
        if (field.type === 'number' && field.value !== '') {
            var value = field.value.replace(/[^0-9.]/g, '');
            var parts = value.split('.');
            field.value = parts.shift() + (parts.length ? '.' + parts.join('') : '');
        }
    }

    function initForm(form) {
        var fields = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));
        fields.forEach(function (field) {
            if (field.type !== 'hidden') errorSlot(field);
            field.addEventListener('beforeinput', function (event) {
                if (!event.data) return;
                if (requiresDigitsOnly(field) && /[^0-9]/.test(event.data)) event.preventDefault();
                if ((field.name === 'pago[numeroTarjeta]' || field.name === 'pago[vencimiento]') && /[^0-9]/.test(event.data)) event.preventDefault();
                if (field.type === 'number' && /[^0-9.]/.test(event.data)) event.preventDefault();
            });
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () {
                    formatCardNumber(field);
                    formatExpiry(field);
                    clampDiscount(field);
                    sanitizeField(field);
                    if (field.name === 'codigoIata') field.value = field.value.toUpperCase();
                    updateForm(form, false);
                });
            });
            field.addEventListener('blur', function () { field.dataset.touched = '1'; updateForm(form, false); });
        });
        updateForm(form, false);
        form.addEventListener('submit', function (event) {
            var allValid = updateForm(form, true);
            if (!allValid) { event.preventDefault(); return; }
            fields.forEach(function (field) {
                if (field.type !== 'password' && field.name !== 'pago[cvv]' && field.name !== 'pago[numeroTarjeta]') field.value = field.value.trim();
            });
            var button = form.querySelector('button[type="submit"], input[type="submit"]');
            if (button) { button.disabled = true; button.dataset.originalText = button.textContent; if (button.tagName === 'BUTTON') button.textContent = 'Procesando...'; }
        });
    }

    function initPasswordToggle(button) {
        var input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;

        button.addEventListener('click', function () {
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.setAttribute('aria-pressed', visible ? 'false' : 'true');
            button.setAttribute('aria-label', visible ? 'Mostrar contrase\u00f1a' : 'Ocultar contrase\u00f1a');
            var openEye = button.querySelector('[data-password-eye-open]');
            var closedEye = button.querySelector('[data-password-eye-closed]');
            if (openEye) openEye.classList.toggle('hidden', !visible);
            if (closedEye) closedEye.classList.toggle('hidden', visible);
            input.focus();
        });
    }

    function initPriceRange(range) {
        var container = range.closest('[data-price-range-container]');
        var output = container && container.querySelector('[data-price-output]');
        if (!container || !output) return;

        var formatter = new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',
            maximumFractionDigits: 0
        });

        function updatePriceLabel() {
            var min = Number(range.min) || 0;
            var max = Number(range.max) || min;
            var value = Math.min(max, Math.max(min, Number(range.value) || min));
            var progress = max > min ? (value - min) / (max - min) : 0;

            output.value = 'Hasta ' + formatter.format(value).replace('ARS', '').trim();

            var thumbCenter = progress * range.clientWidth;
            var halfLabel = output.offsetWidth / 2;
            var labelCenter = Math.min(
                Math.max(thumbCenter, halfLabel),
                Math.max(halfLabel, range.clientWidth - halfLabel)
            );
            output.style.left = labelCenter + 'px';
            output.style.transform = 'translateX(-50%)';
        }

        range.addEventListener('input', updatePriceLabel);
        range.addEventListener('change', updatePriceLabel);
        window.addEventListener('resize', updatePriceLabel);
        updatePriceLabel();
    }

    document.querySelectorAll('form').forEach(initForm);
    document.querySelectorAll('[data-password-toggle]').forEach(initPasswordToggle);
    document.querySelectorAll('[data-price-range]').forEach(initPriceRange);
    document.querySelectorAll('[data-toast]').forEach(function (toast) {
        var close = toast.querySelector('[data-toast-close]');
        var dismiss = function () {
            toast.classList.add('hidden');
        };
        if (close) close.addEventListener('click', dismiss);
        window.setTimeout(dismiss, 5000);
    });

    document.querySelectorAll('[data-city-select]').forEach(function (select) {
        var update = function () { var target = document.getElementById(select.dataset.descriptionTarget); var option = select.options[select.selectedIndex]; if (target && option) target.textContent = option.dataset.description || option.textContent; };
        update(); select.addEventListener('change', update);
    });

    var swap = document.getElementById('change-destiny-origin');
    if (swap) swap.addEventListener('click', function () {
        var origin = document.querySelector('select[name="origen"]'), destination = document.querySelector('select[name="destino"]');
        if (!origin || !destination) return;
        var value = origin.value; origin.value = destination.value; destination.value = value;
        origin.dispatchEvent(new Event('change', { bubbles: true })); destination.dispatchEvent(new Event('change', { bubbles: true }));
    });
})();
