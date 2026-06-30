document.addEventListener("DOMContentLoaded", () => {
    // ============================
    // 1. UTILIDADES
    // ============================
    
    // Utilidad: Mostrar/Ocultar contraseña
    const setupPasswordToggle = (toggleBtnId) => {
        const btn = document.getElementById(toggleBtnId);
        
        if (btn) {
            btn.addEventListener('click', (event) => {
                const input = event.currentTarget.parentElement.querySelector('input');
                if (!input) return;

                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                
                // Actualización accesible
                btn.setAttribute('aria-pressed', !isText ? 'true' : 'false');
                btn.setAttribute('aria-label', !isText ? 'Ocultar contraseña' : 'Mostrar contraseña');
                
                // Cambiar ícono visualmente
                const svgOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
                const svgClosed = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
                
                btn.innerHTML = !isText ? svgClosed : svgOpen;
            });
        }
    };

    // Inicializamos asumiendo los IDs asignados a los botones
    setupPasswordToggle('toggle_login_password');
    setupPasswordToggle('toggle_reg_password');
    setupPasswordToggle('toggle_reg_password_conf');
    setupPasswordToggle('toggle_recovery_password');
    setupPasswordToggle('toggle_recovery_password_conf');

    // Utilidad: Requisitos de contraseña (Validación UI y Lógica en tiempo real)
    const updateRequirementsUI = (passVal, passConfVal, prefixId = 'req') => {
        const reqs = [
            { isValid: passVal.length >= 8, id: `${prefixId}-length` },
            { isValid: /[A-Z]/.test(passVal), id: `${prefixId}-upper` },
            { isValid: /[a-z]/.test(passVal), id: `${prefixId}-lower` },
            { isValid: /[0-9]/.test(passVal), id: `${prefixId}-number` },
            { isValid: /[^a-zA-Z0-9]/.test(passVal), id: `${prefixId}-special` },
            { isValid: passVal !== '' && passVal === passConfVal, id: `${prefixId}-match` }
        ];

        let allValid = true;

        reqs.forEach(req => {
            const li = document.getElementById(req.id);
            const dot = document.getElementById(`${req.id}-icon`);
            const srText = document.getElementById(`${req.id}-sr`);
            
            if (!li || !dot) {
                if (!req.isValid) allValid = false;
                return;
            }

            if (req.isValid) {
                li.className = 'flex items-center gap-3 text-xs leading-4 text-green-600';
                dot.className = 'font-bold';
                dot.textContent = '✓';
                if (srText) srText.textContent = 'Cumplido';
            } else {
                li.className = 'flex items-center gap-3 text-xs leading-4 text-flyto-muted';
                dot.className = '';
                dot.textContent = '✗';
                if (srText) srText.textContent = 'Pendiente';
                allValid = false;
            }
        });

        return allValid;
    };


    // ============================
    // 2. CONTROL DE LOGIN
    // ============================
    const loginEmail = document.getElementById('login_email');
    const loginPassword = document.getElementById('login_password');
    const btnSubmitLogin = document.getElementById('btnSubmitLogin');

    if (loginEmail && loginPassword && btnSubmitLogin) {
        const checkLoginValidity = () => {
            const emailVal = loginEmail.value.trim();
            const passVal = loginPassword.value.trim();
            
            const isEmailValid = emailVal.includes('@') && loginEmail.checkValidity();
            const isPassValid = passVal !== '';

            if (isEmailValid && isPassValid) {
                btnSubmitLogin.removeAttribute('disabled');
            } else {
                btnSubmitLogin.setAttribute('disabled', 'true');
            }
        };

        // Evaluamos el estado inicial por si el navegador autocompleta al cargar
        checkLoginValidity();

        ['input', 'change'].forEach(evt => {
            loginEmail.addEventListener(evt, checkLoginValidity);
            loginPassword.addEventListener(evt, checkLoginValidity);
        });
    }


    // ============================
    // 3. CONTROL DE REGISTRO
    // ============================
    const regNombre = document.getElementById('reg_nombre');
    const regApellido = document.getElementById('reg_apellido');
    const regEmail = document.getElementById('reg_email');
    const regTelefono = document.getElementById('reg_telefono');
    const regPassword = document.getElementById('reg_password');
    const regPasswordConf = document.getElementById('reg_password_conf');
    const btnSubmitRegistro = document.getElementById('btnSubmitRegistro');

    if (regNombre && regApellido && regEmail && regTelefono && regPassword && regPasswordConf && btnSubmitRegistro) {
        const checkRegistroValidity = () => {
            // Validaciones HTML5 nativas
            const basicValid = regNombre.checkValidity() &&
                               regApellido.checkValidity() &&
                               regEmail.checkValidity() && regEmail.value.includes('@') &&
                               regTelefono.checkValidity();
            
            // Validaciones de contraseña
            const reqsValid = updateRequirementsUI(regPassword.value, regPasswordConf.value);

            if (basicValid && reqsValid) {
                btnSubmitRegistro.removeAttribute('disabled');
            } else {
                btnSubmitRegistro.setAttribute('disabled', 'true');
            }
        };

        // Estado inicial
        checkRegistroValidity();

        const fields = [regNombre, regApellido, regEmail, regTelefono, regPassword, regPasswordConf];
        fields.forEach(field => {
            ['input', 'change'].forEach(evt => {
                field.addEventListener(evt, checkRegistroValidity);
            });
        });
    }


    // ============================
    // 4. CONTROL DE RECUPERACIÓN DE CONTRASEÑA
    // ============================
    const recupPassword = document.getElementById('recuperar-contrasena-password');
    const recupPasswordConf = document.getElementById('recuperar-contrasena-password-confirmation');
    const btnSubmitRecup = document.getElementById('recuperar-contrasena-cambiar-submit');

    if (recupPassword && recupPasswordConf && btnSubmitRecup) {
        const checkRecupValidity = () => {
            const reqsValid = updateRequirementsUI(recupPassword.value, recupPasswordConf.value, 'req-recup');
            
            if (reqsValid) {
                btnSubmitRecup.removeAttribute('disabled');
            } else {
                btnSubmitRecup.setAttribute('disabled', 'true');
            }
        };

        // Estado inicial
        checkRecupValidity();

        const fields = [recupPassword, recupPasswordConf];
        fields.forEach(field => {
            ['input', 'change'].forEach(evt => {
                field.addEventListener(evt, checkRecupValidity);
            });
        });
    }
});
