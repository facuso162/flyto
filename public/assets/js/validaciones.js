document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================
    // FORMULARIO DE LOGIN
    // ==========================================
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        const emailLogin = document.getElementById('email');
        const passLogin = document.getElementById('password');
        const toggleLogin = document.getElementById('togglePasswordLogin');
        const btnSubmitLogin = document.getElementById('btnSubmitLogin');

        // 1. Mostrar/Ocultar contraseña (Botón Ojo)
        if (toggleLogin && passLogin) {
            toggleLogin.addEventListener('click', () => {
                const type = passLogin.getAttribute('type') === 'password' ? 'text' : 'password';
                passLogin.setAttribute('type', type);
                // Cambia el emoji (podés reemplazarlo por un SVG de ojo tachado si preferís)
                toggleLogin.textContent = type === 'password' ? '👁️' : '🙈'; 
            });
        }

        // 2. Validación Cliente: Habilitar botón Submit
        const checkLoginValidity = () => {
            const isEmailFilled = emailLogin && emailLogin.value.trim() !== '';
            const isPasswordFilled = passLogin && passLogin.value.trim() !== '';

            // Verifica que los elementos existan (IDs correctos) y tengan valor, 
            // además de la validación nativa (ej. el "@" en el email)
            if (isEmailFilled && isPasswordFilled && loginForm.checkValidity()) {
                btnSubmitLogin.removeAttribute('disabled');
            } else {
                btnSubmitLogin.setAttribute('disabled', 'true');
            }
        };

        // Estado inicial
        checkLoginValidity();

        // Escuchar cambios
        loginForm.addEventListener('input', checkLoginValidity);
        loginForm.addEventListener('change', checkLoginValidity);
    }

    // ==========================================
    // FORMULARIO DE REGISTRO
    // ==========================================
    const registroForm = document.getElementById('registroForm');
    
    if (registroForm) {
        const passRegistro = document.getElementById('password');
        const passConfirm = document.getElementById('password_confirmation');
        const toggleRegistro = document.getElementById('togglePasswordRegistro');
        const btnSubmitRegistro = document.getElementById('btnSubmitRegistro');
        
        // Elementos de la checklist
        const reqLargo = document.getElementById('req-largo');
        const reqNumero = document.getElementById('req-numero');
        const reqMayuscula = document.getElementById('req-mayuscula');

        // 1. Mostrar/Ocultar contraseña principal en Registro
        if (toggleRegistro && passRegistro) {
            toggleRegistro.addEventListener('click', () => {
                const type = passRegistro.getAttribute('type') === 'password' ? 'text' : 'password';
                passRegistro.setAttribute('type', type);
                toggleRegistro.textContent = type === 'password' ? '👁️' : '🙈';
            });
        }

        // Función para cambiar ícono y estilo de los requisitos
        const actualizarRequisito = (elemento, cumple) => {
            if (!elemento) return;
            const icono = elemento.querySelector('span');
            if (cumple) {
                icono.textContent = '✅';
                elemento.classList.add('text-flyto-navy');
            } else {
                icono.textContent = '❌';
                elemento.classList.remove('text-flyto-navy');
            }
        };

        // 2. Escuchar cambios en todo el formulario para validar
        registroForm.addEventListener('input', () => {
            const passValue = passRegistro.value;
            
            // Validar regex de contraseña
            const tieneLargo = passValue.length >= 8;
            const tieneNumero = /\d/.test(passValue);
            const tieneMayuscula = /[A-Z]/.test(passValue);
            
            // Actualizar interfaz visual
            actualizarRequisito(reqLargo, tieneLargo);
            actualizarRequisito(reqNumero, tieneNumero);
            actualizarRequisito(reqMayuscula, tieneMayuscula);

            const passCoinciden = passValue === passConfirm.value && passValue !== "";
            const passCumpleTodo = tieneLargo && tieneNumero && tieneMayuscula;

            // Habilitar submit SOLO si todo el HTML5 es válido, 
            // la contraseña cumple requisitos y ambas contraseñas coinciden
            if (registroForm.checkValidity() && passCumpleTodo && passCoinciden) {
                btnSubmitRegistro.removeAttribute('disabled');
            } else {
                btnSubmitRegistro.setAttribute('disabled', 'true');
            }
        });
    }

    // ==========================================
    // FORMULARIO DE BÚSQUEDA DE VUELOS
    // ==========================================
    const buscarVuelosForm = document.getElementById('buscar-vuelos');
    
    if (buscarVuelosForm) {
        const btnSubmitBuscarVuelos = document.getElementById('btnSubmitBuscarVuelos');
        
        const checkBuscarVuelosValidity = () => {
            if (buscarVuelosForm.checkValidity()) {
                btnSubmitBuscarVuelos.removeAttribute('disabled');
            } else {
                btnSubmitBuscarVuelos.setAttribute('disabled', 'true');
            }
        };

        // Estado inicial
        checkBuscarVuelosValidity();

        // Escuchar cambios en los select e inputs
        buscarVuelosForm.addEventListener('input', checkBuscarVuelosValidity);
        buscarVuelosForm.addEventListener('change', checkBuscarVuelosValidity);
    }
});