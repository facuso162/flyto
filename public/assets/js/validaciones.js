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

    // ============================
    // 5. CONTROL DE RECUPERACIÓN DE CONTRASEÑA - EMAIL
    // ============================
    const recupEmail = document.getElementById('recuperar-contrasena-email');
    const btnSubmitRecupEmail = document.getElementById('recuperar-contrasena-submit');

    if (recupEmail && btnSubmitRecupEmail) {
        const checkRecupEmailValidity = () => {
            const emailVal = recupEmail.value.trim();
            const isEmailValid = emailVal !== '' && emailVal.includes('@') && recupEmail.checkValidity();

            if (isEmailValid) {
                btnSubmitRecupEmail.removeAttribute('disabled');
            } else {
                btnSubmitRecupEmail.setAttribute('disabled', 'true');
            }
        };

        checkRecupEmailValidity();

        ['input', 'change'].forEach(evt => {
            recupEmail.addEventListener(evt, checkRecupEmailValidity);
        });
    }

    // ============================
    // 6. CONTROL DE RECUPERACIÓN DE CONTRASEÑA - TOKEN/CÓDIGO
    // ============================
    const recupToken = document.getElementById('recuperar-contrasena-token');
    const btnSubmitRecupToken = document.getElementById('recuperar-contrasena-token-submit');

    if (recupToken && btnSubmitRecupToken) {
        const checkRecupTokenValidity = () => {
            const tokenVal = recupToken.value.trim();
            const isTokenValid = tokenVal.length === 6 && /^[0-9]+$/.test(tokenVal);

            if (isTokenValid) {
                btnSubmitRecupToken.removeAttribute('disabled');
            } else {
                btnSubmitRecupToken.setAttribute('disabled', 'true');
            }
        };

        checkRecupTokenValidity();

        ['input', 'change'].forEach(evt => {
            recupToken.addEventListener(evt, checkRecupTokenValidity);
        });
    }

    // ============================
    // 7. CONTROL DE BÚSQUEDA DE VUELOS
    // ============================
    const formBuscarVuelos = document.getElementById('buscar-vuelos');
    if (formBuscarVuelos) {
        const origenField = document.getElementById('origen_field');
        const destinoField = document.getElementById('destino_field');
        const fechaField = document.getElementById('fecha_field');
        const pasajerosField = document.getElementById('pasajeros_field');
        const btnSubmitBuscar = formBuscarVuelos.querySelector('button[type="submit"]');

        if (origenField && destinoField && fechaField && pasajerosField && btnSubmitBuscar) {
            const showBuscarError = (input, message) => {
                if (!input) return;
                const errorId = `error-${input.id}`;
                let span = document.getElementById(errorId);
                if (!span) {
                    span = document.createElement('span');
                    span.id = errorId;
                    span.className = 'mt-1 block text-xs text-red-700';
                    span.setAttribute('role', 'alert');
                    input.insertAdjacentElement('afterend', span);
                }
                span.textContent = message;
            };

            const hideBuscarError = (input) => {
                if (!input) return;
                const errorId = `error-${input.id}`;
                let span = document.getElementById(errorId);
                if (span) {
                    span.remove();
                }
            };

            const touchedFields = new Set();

            const checkBuscarVuelosValidity = () => {
                let isValid = true;
                
                if (origenField.value === '') {
                    isValid = false;
                    if (touchedFields.has(origenField)) showBuscarError(origenField, 'Seleccioná la ciudad de origen.');
                } else {
                    hideBuscarError(origenField);
                }

                if (destinoField.value === '') {
                    isValid = false;
                    if (touchedFields.has(destinoField)) showBuscarError(destinoField, 'Seleccioná la ciudad de destino.');
                } else {
                    hideBuscarError(destinoField);
                }
                
                if (!fechaField.value) {
                    isValid = false;
                    if (touchedFields.has(fechaField)) showBuscarError(fechaField, 'La fecha de salida es obligatoria.');
                } else {
                    const todayString = new Date().toLocaleDateString('en-CA'); 
                    
                    if (fechaField.value < todayString) {
                        isValid = false;
                        if (touchedFields.has(fechaField)) 
                            showBuscarError(fechaField, 'La fecha de salida no puede ser anterior a hoy.');
                    } else {
                        hideBuscarError(fechaField);
                    }
                }

                const pasajerosVal = parseInt(pasajerosField.value, 10);
                if (isNaN(pasajerosVal) || pasajerosVal <= 0) {
                    isValid = false;
                    if (touchedFields.has(pasajerosField)) showBuscarError(pasajerosField, 'Seleccioná la cantidad de pasajeros.');
                } else {
                    hideBuscarError(pasajerosField);
                }

                if (isValid) {
                    btnSubmitBuscar.removeAttribute('disabled');
                } else {
                    btnSubmitBuscar.setAttribute('disabled', 'true');
                }
            };

            checkBuscarVuelosValidity();

            const fieldsBuscar = [origenField, destinoField, fechaField, pasajerosField];
            fieldsBuscar.forEach(field => {
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, (e) => {
                        touchedFields.add(e.target);
                        checkBuscarVuelosValidity();
                    });
                });
            });
        }
    }

    // Utilidades para Crear/Editar Vuelo
    const setFieldError = (input, fieldName, message) => {
        if (!input) return;
        let span = document.getElementById(`error-${fieldName}`);
        if (!span) {
            span = document.createElement('span');
            span.id = `error-${fieldName}`;
            span.className = 'mt-1 block text-xs text-red-700';
            input.parentNode.appendChild(span);
        }
        span.textContent = message;
        span.style.display = 'block';
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', `error-${fieldName}`);
        input.classList.remove('border-flyto-ink/15');
        input.classList.add('border-red-700');
    };

    const clearFieldError = (input, fieldName) => {
        if (!input) return;
        const span = document.getElementById(`error-${fieldName}`);
        if (span) {
            span.style.display = 'none';
            span.textContent = '';
        }
        input.removeAttribute('aria-invalid');
        input.classList.remove('border-red-700');
        input.classList.add('border-flyto-ink/15');
    };

    const setupFlightValidation = (form, isEdit) => {
        const fields = {
            codigoVuelo: form.querySelector('[name="codigoVuelo"]'),
            precio: form.querySelector('[name="precio"]'),
            asientosDisponibles: form.querySelector('[name="asientosDisponibles"]'),
            fechaSalida: form.querySelector('[name="fechaSalida"]'),
            fechaLlegada: form.querySelector('[name="fechaLlegada"]'),
            origenCiudadId: form.querySelector('[name="origenCiudadId"]'),
            destinoCiudadId: form.querySelector('[name="destinoCiudadId"]'),
            duracionHoras: form.querySelector('[name="duracionHoras"]'),
            distanciaKm: form.querySelector('[name="distanciaKm"]')
        };
        const btnSubmit = form.querySelector('button[type="submit"]');

        if (!btnSubmit || !fields.codigoVuelo) return null;

        const checkValidity = (isInitial = false) => {
            let isValid = true;
            const now = new Date();

            const validateField = (field, fieldName, condition, errorMessage) => {
                if (!condition) {
                    isValid = false;
                    if (!isInitial || field.classList.contains('border-red-700')) {
                        setFieldError(field, fieldName, errorMessage);
                    }
                } else {
                    if (!isInitial || field.classList.contains('border-red-700')) {
                        clearFieldError(field, fieldName);
                    }
                }
            };

            const codigoVal = fields.codigoVuelo.value.trim();
            validateField(fields.codigoVuelo, 'codigoVuelo', codigoVal !== '' && codigoVal.length <= 10, codigoVal === '' ? 'El código del vuelo es obligatorio.' : 'El código del vuelo no puede superar 10 caracteres.');

            const precioVal = parseFloat(fields.precio.value);
            validateField(fields.precio, 'precio', !isNaN(precioVal) && precioVal > 0, 'El precio debe ser un número mayor a 0.');

            const asientosVal = parseInt(fields.asientosDisponibles.value, 10);
            validateField(fields.asientosDisponibles, 'asientosDisponibles', !isNaN(asientosVal) && asientosVal > 0 && Number.isInteger(Number(fields.asientosDisponibles.value)), 'Los asientos deben ser un número entero mayor a 0.');

            const salidaVal = fields.fechaSalida.value;
            validateField(fields.fechaSalida, 'fechaSalida', salidaVal && new Date(salidaVal) > now, !salidaVal ? 'La fecha de salida es obligatoria.' : 'La fecha y hora de salida debe ser futura.');

            const llegadaVal = fields.fechaLlegada.value;
            validateField(fields.fechaLlegada, 'fechaLlegada', llegadaVal && new Date(llegadaVal) > now, !llegadaVal ? 'La fecha de llegada es obligatoria.' : 'La fecha y hora de llegada debe ser futura.');

            validateField(fields.origenCiudadId, 'origenCiudadId', fields.origenCiudadId.value !== '', 'Seleccioná la ciudad de origen.');
            
            validateField(fields.destinoCiudadId, 'destinoCiudadId', fields.destinoCiudadId.value !== '', 'Seleccioná la ciudad de destino.');

            const duracionVal = parseFloat(fields.duracionHoras.value);
            validateField(fields.duracionHoras, 'duracionHoras', !isNaN(duracionVal) && duracionVal > 0, 'La duración debe ser un número mayor a 0.');

            const distanciaVal = parseFloat(fields.distanciaKm.value);
            validateField(fields.distanciaKm, 'distanciaKm', !isNaN(distanciaVal) && distanciaVal > 0, 'La distancia debe ser un número mayor a 0.');

            if (isValid) {
                btnSubmit.removeAttribute('disabled');
            } else {
                btnSubmit.setAttribute('disabled', 'true');
            }
        };

        checkValidity(true);

        const checkFn = () => checkValidity(false);

        Object.values(fields).forEach(field => {
            if (field) {
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, checkFn);
                });
            }
        });

        return checkFn;
    };

    // ============================
    // 8. CONTROL DE CREAR VUELO
    // ============================
    const formCrearVuelo = document.querySelector('form[action*="/vuelos/crear"]');
    if (formCrearVuelo) {
        const checkCrearVueloValidity = setupFlightValidation(formCrearVuelo, false);
    }

    // ============================
    // 9. CONTROL DE EDITAR VUELO
    // ============================
    const formEditarVuelo = document.querySelector('form[action*="/vuelos/editar"]');
    if (formEditarVuelo) {
        const checkEditarVueloValidity = setupFlightValidation(formEditarVuelo, true);
    // ============================
    // 10. CONTROL DE PASAJEROS (RESERVA)
    // ============================
    const formPasajeros = document.querySelector('form[action$="/reservas/pasajeros"]');
    if (formPasajeros) {
        const btnSubmitPasajeros = formPasajeros.querySelector('[type="submit"]');
        const touchedFields = new Set();
        
        const checkPasajerosValidity = () => {
            if (!btnSubmitPasajeros) return;
            let formIsValid = true;
            
            const fieldsets = formPasajeros.querySelectorAll('fieldset');
            fieldsets.forEach((fieldset, index) => {
                const fields = {
                    nombre: fieldset.querySelector('[name$="][nombre]"]'),
                    apellido: fieldset.querySelector('[name$="][apellido]"]'),
                    documento: fieldset.querySelector('[name$="][documento]"]'),
                    pasaporte: fieldset.querySelector('[name$="][pasaporte]"]'),
                    fechaNacimiento: fieldset.querySelector('[name$="][fechaNacimiento]"]'),
                    nacionalidad: fieldset.querySelector('[name$="][nacionalidad]"]'),
                    correoElectronico: fieldset.querySelector('[name$="][correoElectronico]"]'),
                    telefonoContacto: fieldset.querySelector('[name$="][telefonoContacto]"]')
                };

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    const fieldName = `pasajeros-${index}-${field.name.match(/\]\[([^\]]+)\]$/)?.[1] || field.name.replace(/[^a-zA-Z0-9]/g, '-')}`;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, fieldName, errorMessage);
                        }
                    } else {
                        clearFieldError(field, fieldName);
                    }
                };

                const isNameValid = (val) => val !== '' && val.length <= 80 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(val);

                if (fields.nombre) {
                    validateField(fields.nombre, isNameValid(fields.nombre.value.trim()), 'El nombre es inválido (solo letras, máx 80).');
                }

                if (fields.apellido) {
                    validateField(fields.apellido, isNameValid(fields.apellido.value.trim()), 'El apellido es inválido (solo letras, máx 80).');
                }

                if (fields.documento) {
                    validateField(fields.documento, fields.documento.value.trim() !== '', 'El documento es obligatorio.');
                }

                if (fields.pasaporte) {
                    validateField(fields.pasaporte, fields.pasaporte.value.trim() !== '', 'El pasaporte es obligatorio.');
                }

                if (fields.fechaNacimiento) {
                    const val = fields.fechaNacimiento.value;
                    let validDate = false;
                    if (val) {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        const parts = val.split('-');
                        if (parts.length === 3) {
                            const selected = new Date(parts[0], parts[1]-1, parts[2]);
                            if (selected < today) validDate = true;
                        }
                    }
                    validateField(fields.fechaNacimiento, validDate, 'La fecha debe ser anterior a hoy.');
                }

                if (fields.nacionalidad) {
                    validateField(fields.nacionalidad, fields.nacionalidad.value !== '', 'Seleccioná la nacionalidad.');
                }

                if (fields.correoElectronico) {
                    const val = fields.correoElectronico.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    validateField(fields.correoElectronico, val !== '' && emailRegex.test(val), 'El correo electrónico no es válido.');
                }

                if (fields.telefonoContacto) {
                    validateField(fields.telefonoContacto, fields.telefonoContacto.value.trim() !== '', 'El teléfono es obligatorio.');
                }
            });

            if (formIsValid) {
                btnSubmitPasajeros.removeAttribute('disabled');
            } else {
                btnSubmitPasajeros.setAttribute('disabled', 'true');
            }
        };

        checkPasajerosValidity();

        const allInputs = formPasajeros.querySelectorAll('input, select');
        allInputs.forEach(input => {
            ['input', 'change'].forEach(evt => {
                input.addEventListener(evt, (e) => {
                    touchedFields.add(e.target);
                    checkPasajerosValidity();
                });
            });
        });
    }

    // ============================
    // 11. CONTROL DE PAGO
    // ============================
    const formPago = document.querySelector('form[action$="/reservas/crear"]');
    if (formPago) {
        const fields = {
            numeroTarjeta: formPago.querySelector('[name="numeroTarjeta"]'),
            nombreTitular: formPago.querySelector('[name="nombreTitular"]'),
            vencimiento: formPago.querySelector('[name="vencimiento"]'),
            cvv: formPago.querySelector('[name="cvv"]'),
            aceptaTerminos: formPago.querySelector('[name="aceptaTerminos"]')
        };
        const btnSubmitPago = formPago.querySelector('[type="submit"]');
        const touchedFields = new Set();

        if (btnSubmitPago) {
            if (fields.numeroTarjeta) {
                fields.numeroTarjeta.addEventListener('input', (e) => {
                    let val = e.target.value.replace(/\D/g, '');
                    val = val.substring(0, 19);
                    e.target.value = val.match(/.{1,4}/g)?.join(' ') || val;
                });
            }

            if (fields.vencimiento) {
                fields.vencimiento.addEventListener('input', (e) => {
                    let val = e.target.value.replace(/\D/g, '');
                    val = val.substring(0, 4);
                    if (val.length > 2) {
                        e.target.value = val.substring(0, 2) + '/' + val.substring(2);
                    } else {
                        e.target.value = val;
                    }
                });
            }
            
            if (fields.cvv) {
                fields.cvv.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
                });
            }

            const checkPagoValidity = () => {
                let formIsValid = true;

                const validateField = (field, fieldName, condition, errorMessage) => {
                    if (!field) return;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, fieldName, errorMessage);
                        }
                    } else {
                        clearFieldError(field, fieldName);
                    }
                };

                if (fields.numeroTarjeta) {
                    const rawVal = fields.numeroTarjeta.value.replace(/\D/g, '');
                    validateField(fields.numeroTarjeta, 'numeroTarjeta', rawVal.length >= 13 && rawVal.length <= 19, 'El número debe tener entre 13 y 19 dígitos.');
                }

                if (fields.nombreTitular) {
                    const val = fields.nombreTitular.value.trim();
                    const isLettersSpaces = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(val);
                    const hasTwoWords = val.split(/\s+/).length >= 2;
                    validateField(fields.nombreTitular, 'nombreTitular', val !== '' && isLettersSpaces && hasTwoWords, 'Debe contener nombre y apellido.');
                }

                if (fields.vencimiento) {
                    const val = fields.vencimiento.value;
                    let validVencimiento = false;
                    const parts = val.split('/');
                    if (parts.length === 2 && parts[0].length === 2 && parts[1].length === 2) {
                        const month = parseInt(parts[0], 10);
                        const year = parseInt(parts[1], 10);
                        if (month >= 1 && month <= 12) {
                            const now = new Date();
                            const currentYear = now.getFullYear() % 100;
                            const currentMonth = now.getMonth() + 1;
                            if (year > currentYear || (year === currentYear && month >= currentMonth)) {
                                validVencimiento = true;
                            }
                        }
                    }
                    validateField(fields.vencimiento, 'vencimiento', validVencimiento, 'Fecha inválida o tarjeta vencida.');
                }

                if (fields.cvv) {
                    const val = fields.cvv.value.trim();
                    validateField(fields.cvv, 'cvv', val.length === 3 || val.length === 4, 'El CVV debe tener 3 o 4 dígitos.');
                }

                if (fields.aceptaTerminos) {
                    validateField(fields.aceptaTerminos, 'aceptaTerminos', fields.aceptaTerminos.checked, 'Debes aceptar los términos y condiciones.');
                }

                if (formIsValid) {
                    btnSubmitPago.removeAttribute('disabled');
                } else {
                    btnSubmitPago.setAttribute('disabled', 'true');
                }
            };

            checkPagoValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkPagoValidity();
                        });
                    });
                }
            });
        }
    // ============================
    // 12. AEROLÍNEAS (CREAR Y EDITAR)
    // ============================
    const setupAerolineaValidation = (form) => {
        const fields = {
            nombre: form.querySelector('[name="nombre"]'),
            codigoIata: form.querySelector('[name="codigoIata"]'),
            paisId: form.querySelector('[name="paisId"]'),
            descripcion: form.querySelector('[name="descripcion"]')
        };
        const btnSubmit = form.querySelector('[type="submit"]');
        const touchedFields = new Set();

        if (btnSubmit) {
            if (fields.codigoIata) {
                fields.codigoIata.addEventListener('input', (e) => {
                    e.target.value = e.target.value.toUpperCase();
                });
            }

            const checkValidity = () => {
                let formIsValid = true;

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, field.name, errorMessage);
                        }
                    } else {
                        clearFieldError(field, field.name);
                    }
                };

                if (fields.nombre) {
                    const val = fields.nombre.value.trim();
                    validateField(fields.nombre, val !== '' && val.length >= 2, 'El nombre debe tener al menos 2 caracteres.');
                }

                if (fields.codigoIata) {
                    const val = fields.codigoIata.value.trim();
                    validateField(fields.codigoIata, /^[A-Z]{2,3}$/.test(val), 'El código IATA debe tener 2 o 3 letras mayúsculas.');
                }

                if (fields.paisId) {
                    validateField(fields.paisId, fields.paisId.value !== '', 'Debe seleccionar un país.');
                }

                if (fields.descripcion) {
                    const val = fields.descripcion.value.trim();
                    validateField(fields.descripcion, val !== '' && val.length >= 10, 'La descripción debe tener al menos 10 caracteres.');
                }

                if (formIsValid) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'true');
                }
            };

            checkValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkValidity();
                        });
                    });
                }
            });
        }
    };

    const formCrearAerolinea = document.querySelector('form[action$="/admin/aerolineas/crear"]');
    if (formCrearAerolinea) setupAerolineaValidation(formCrearAerolinea);

    const formEditarAerolinea = document.querySelector('form[action$="/admin/aerolineas/editar"]');
    if (formEditarAerolinea) setupAerolineaValidation(formEditarAerolinea);

    // ============================
    // 13. NOVEDADES (CREAR Y EDITAR)
    // ============================
    const setupNovedadValidation = (form) => {
        const fields = {
            titulo: form.querySelector('[name="titulo"]'),
            categoria: form.querySelector('[name="categoria"]'),
            texto: form.querySelector('[name="texto"]'),
            fechaExpiracion: form.querySelector('[name="fechaExpiracion"]')
        };
        const btnSubmit = form.querySelector('[type="submit"]');
        const touchedFields = new Set();
        
        let counterSpan = null;
        if (fields.texto) {
            counterSpan = document.createElement('span');
            counterSpan.className = 'mt-1 block text-xs text-flyto-muted';
            fields.texto.insertAdjacentElement('afterend', counterSpan);
            
            const updateCounter = () => {
                const len = fields.texto.value.length;
                counterSpan.textContent = `${len} / 200 caracteres`;
                if (len > 200) {
                    counterSpan.classList.replace('text-flyto-muted', 'text-red-700');
                } else {
                    counterSpan.classList.replace('text-red-700', 'text-flyto-muted');
                }
            };
            fields.texto.addEventListener('input', updateCounter);
            updateCounter();
        }

        if (btnSubmit) {
            const checkValidity = () => {
                let formIsValid = true;

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, field.name, errorMessage);
                        }
                    } else {
                        clearFieldError(field, field.name);
                    }
                };

                if (fields.titulo) {
                    const val = fields.titulo.value.trim();
                    validateField(fields.titulo, val !== '' && val.length <= 100, 'El título es obligatorio y máximo 100 caracteres.');
                }

                if (fields.categoria) {
                    const val = fields.categoria.value.trim();
                    validateField(fields.categoria, val !== '' && val.length <= 50, 'La categoría es obligatoria y máximo 50 caracteres.');
                }

                if (fields.texto) {
                    const val = fields.texto.value.trim();
                    validateField(fields.texto, val !== '' && val.length <= 200, 'El texto es obligatorio y máximo 200 caracteres.');
                }

                if (fields.fechaExpiracion) {
                    const val = fields.fechaExpiracion.value;
                    let validDate = false;
                    if (val) {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        const parts = val.split('-');
                        if (parts.length === 3) {
                            const selected = new Date(parts[0], parts[1]-1, parts[2]);
                            if (selected > today) validDate = true;
                        }
                    }
                    validateField(fields.fechaExpiracion, validDate, 'La fecha debe ser estrictamente posterior a hoy.');
                }

                if (formIsValid) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'true');
                }
            };

            checkValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkValidity();
                        });
                    });
                }
            });
        }
    };

    const formCrearNovedad = document.querySelector('form[action$="/admin/novedades/crear"]');
    if (formCrearNovedad) setupNovedadValidation(formCrearNovedad);

    const formEditarNovedad = document.querySelector('form[action$="/admin/novedades/editar"]');
    if (formEditarNovedad) setupNovedadValidation(formEditarNovedad);

    // ============================
    // 14. CREAR CEO
    // ============================
    const formCrearCeo = document.querySelector('form[action$="/admin/ceos/crear"]');
    if (formCrearCeo) {
        const fields = {
            nombre: formCrearCeo.querySelector('[name="nombre"]'),
            apellido: formCrearCeo.querySelector('[name="apellido"]'),
            email: formCrearCeo.querySelector('[name="email"]'),
            password: formCrearCeo.querySelector('[name="password"]'),
            password_confirmation: formCrearCeo.querySelector('[name="password_confirmation"]'),
            aerolineaId: formCrearCeo.querySelector('[name="aerolineaId"]')
        };
        const btnSubmit = formCrearCeo.querySelector('[type="submit"]');
        const reqListItems = formCrearCeo.querySelectorAll('ul li');
        const touchedFields = new Set();

        const updateReqItem = (li, isMet) => {
            if (!li) return;
            const icon = li.querySelector('span[aria-hidden="true"]');
            if (isMet) {
                li.classList.replace('text-flyto-muted', 'text-emerald-600');
                if (icon) {
                    icon.textContent = '✓';
                    icon.classList.replace('text-flyto-muted', 'text-emerald-600');
                }
            } else {
                li.classList.replace('text-emerald-600', 'text-flyto-muted');
                if (icon) {
                    icon.textContent = '×';
                    icon.classList.replace('text-emerald-600', 'text-flyto-muted');
                }
            }
        };

        if (btnSubmit) {
            const checkValidity = () => {
                let formIsValid = true;

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, field.name, errorMessage);
                        }
                    } else {
                        clearFieldError(field, field.name);
                    }
                };

                const isLettersSpaces = (val) => val !== '' && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(val);

                if (fields.nombre) {
                    validateField(fields.nombre, isLettersSpaces(fields.nombre.value.trim()), 'El nombre es obligatorio y solo debe contener letras.');
                }

                if (fields.apellido) {
                    validateField(fields.apellido, isLettersSpaces(fields.apellido.value.trim()), 'El apellido es obligatorio y solo debe contener letras.');
                }

                if (fields.email) {
                    const val = fields.email.value.trim();
                    validateField(fields.email, val !== '' && fields.email.checkValidity() && val.includes('@'), 'El correo electrónico no es válido.');
                }

                const pwdVal = fields.password ? fields.password.value : '';
                const confVal = fields.password_confirmation ? fields.password_confirmation.value : '';
                
                const hasLength = pwdVal.length >= 8;
                const hasUpper = /[A-Z]/.test(pwdVal);
                const hasNumber = /[0-9]/.test(pwdVal);
                const hasSpecial = /[^a-zA-Z0-9]/.test(pwdVal);
                const isMatch = pwdVal !== '' && pwdVal === confVal;

                if (reqListItems.length >= 5) {
                    updateReqItem(reqListItems[0], hasLength);
                    updateReqItem(reqListItems[1], hasUpper);
                    updateReqItem(reqListItems[2], hasNumber);
                    updateReqItem(reqListItems[3], hasSpecial);
                    updateReqItem(reqListItems[4], isMatch);
                }

                const isPwdValid = hasLength && hasUpper && hasNumber && hasSpecial;
                if (fields.password) {
                    validateField(fields.password, isPwdValid, 'La contraseña no cumple con los requisitos.');
                }

                if (fields.password_confirmation) {
                    validateField(fields.password_confirmation, isMatch, 'Las contraseñas no coinciden.');
                }

                if (fields.aerolineaId) {
                    validateField(fields.aerolineaId, fields.aerolineaId.value !== '', 'Debe seleccionar una aerolínea.');
                }

                if (formIsValid) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'true');
                }
            };

            checkValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkValidity();
                        });
                    });
                }
            });
        }
    }

    // ============================
    // 15. PROMOCIONES (CREAR Y EDITAR)
    // ============================
    const setupPromocionValidation = (form) => {
        const fields = {
            descripcion: form.querySelector('[name="descripcion"]'),
            descuento: form.querySelector('[name="descuento"]')
        };
        const btnSubmit = form.querySelector('[type="submit"]');
        const touchedFields = new Set();
        
        let counterSpan = null;
        if (fields.descripcion) {
            counterSpan = document.createElement('span');
            counterSpan.className = 'mt-1 block text-xs text-flyto-muted';
            fields.descripcion.insertAdjacentElement('afterend', counterSpan);
            
            const updateCounter = () => {
                const len = fields.descripcion.value.length;
                counterSpan.textContent = `${len} / 200 caracteres`;
                if (len > 200) {
                    counterSpan.classList.replace('text-flyto-muted', 'text-red-700');
                } else {
                    counterSpan.classList.replace('text-red-700', 'text-flyto-muted');
                }
            };
            fields.descripcion.addEventListener('input', updateCounter);
            updateCounter();
        }

        if (btnSubmit) {
            const checkValidity = () => {
                let formIsValid = true;

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, field.name, errorMessage);
                        }
                    } else {
                        clearFieldError(field, field.name);
                    }
                };

                if (fields.descripcion) {
                    const val = fields.descripcion.value.trim();
                    validateField(fields.descripcion, val !== '' && val.length <= 200, 'La descripción es obligatoria y máximo 200 caracteres.');
                }

                if (fields.descuento) {
                    const num = parseInt(fields.descuento.value, 10);
                    validateField(fields.descuento, !isNaN(num) && num >= 1 && num <= 100, 'El descuento debe ser un número entre 1 y 100.');
                }

                if (formIsValid) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'true');
                }
            };

            checkValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkValidity();
                        });
                    });
                }
            });
        }
    };

    const formCrearPromocion = document.querySelector('form[action$="/ceo/promociones/crear"]');
    if (formCrearPromocion) setupPromocionValidation(formCrearPromocion);

    const formEditarPromocion = document.querySelector('form[action$="/ceo/promociones/editar"]');
    if (formEditarPromocion) setupPromocionValidation(formEditarPromocion);

    // ============================
    // 16. SOLICITAR ACTIVACIÓN
    // ============================
    const formSolicitarActivacion = document.querySelector('form[action$="/ceo/promociones/solicitar-activacion"]');
    if (formSolicitarActivacion) {
        const fieldFechaFin = formSolicitarActivacion.querySelector('[name="fecha_fin"]');
        const btnSubmit = formSolicitarActivacion.querySelector('[type="submit"]');
        let isTouched = false;

        if (btnSubmit && fieldFechaFin) {
            const checkValidity = () => {
                const val = fieldFechaFin.value;
                let validDate = false;
                if (val) {
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    const parts = val.split('-');
                    if (parts.length === 3) {
                        const selected = new Date(parts[0], parts[1]-1, parts[2]);
                        if (selected > today) validDate = true;
                    }
                }
                
                if (!validDate) {
                    if (isTouched) setFieldError(fieldFechaFin, 'fecha_fin', 'La fecha debe ser estrictamente posterior a hoy.');
                    btnSubmit.setAttribute('disabled', 'true');
                } else {
                    clearFieldError(fieldFechaFin, 'fecha_fin');
                    btnSubmit.removeAttribute('disabled');
                }
            };

            checkValidity();

            ['input', 'change'].forEach(evt => {
                fieldFechaFin.addEventListener(evt, () => {
                    isTouched = true;
                    checkValidity();
                });
            });
        }
    }

    // ============================
    // 17. CONTACTO
    // ============================
    const formsContacto = document.querySelectorAll('form[action$="/contacto/enviar"]');
    formsContacto.forEach((form, index) => {
        const fields = {
            nombre: form.querySelector('[name="nombre"]'),
            apellido: form.querySelector('[name="apellido"]'),
            email: form.querySelector('[name="email"]'),
            asunto: form.querySelector('[name="asunto"]'),
            mensaje: form.querySelector('[name="mensaje"]')
        };
        const btnSubmit = form.querySelector('[type="submit"]');
        const touchedFields = new Set();

        if (btnSubmit) {
            const checkValidity = () => {
                let formIsValid = true;

                const validateField = (field, condition, errorMessage) => {
                    if (!field) return;
                    const fieldName = `${field.name}-${index}`;
                    if (!condition) {
                        formIsValid = false;
                        if (touchedFields.has(field)) {
                            setFieldError(field, fieldName, errorMessage);
                        }
                    } else {
                        clearFieldError(field, fieldName);
                    }
                };

                if (fields.nombre) {
                    validateField(fields.nombre, fields.nombre.value.trim() !== '', 'El nombre es obligatorio.');
                }

                if (fields.apellido) {
                    validateField(fields.apellido, fields.apellido.value.trim() !== '', 'El apellido es obligatorio.');
                }

                if (fields.email) {
                    const val = fields.email.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    validateField(fields.email, val !== '' && emailRegex.test(val) && fields.email.checkValidity(), 'El correo electrónico no es válido.');
                }

                if (fields.asunto) {
                    validateField(fields.asunto, fields.asunto.value !== '', 'Debe seleccionar un asunto.');
                }

                if (fields.mensaje) {
                    const val = fields.mensaje.value.trim();
                    validateField(fields.mensaje, val !== '' && val.length >= 10, 'El mensaje debe tener al menos 10 caracteres.');
                }

                if (formIsValid) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'true');
                }
            };

            checkValidity();

            Object.values(fields).forEach(field => {
                if (field) {
                    ['input', 'change'].forEach(evt => {
                        field.addEventListener(evt, (e) => {
                            touchedFields.add(e.target);
                            checkValidity();
                        });
                    });
                }
            });
        }
    });
});
