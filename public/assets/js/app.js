(() => {
    'use strict';

    // ---------- Shared Helpers ----------
    const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const NAME_REGEX = /^[\p{L}\s.'-]+$/u;

    async function apiRequest(url, { method = 'GET', body = null } = {}) {
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };
        const options = { method, headers };

        if (body !== null) {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        const response = await fetch(url, options);
        let data = {};
        try {
            data = await response.json();
        } catch {
            data = { ok: false, message: 'Respuesta inesperada del servidor.' };
        }

        return { status: response.status, ok: response.ok && data.ok !== false, data };
    }

    function showAlert(el, message, type = 'error') {
        if (!el) return;
        el.textContent = message;
        el.className = `alert alert-${type} show`;
    }

    function hideAlert(el) {
        if (!el) return;
        el.textContent = '';
        el.className = 'alert';
    }

    function setFieldError(input, message) {
        if (!input) return;
        const group = input.closest('.form-group');
        const errorEl = group ? group.querySelector('.field-error') : null;
        if (message) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (errorEl) errorEl.textContent = message;
        } else {
            input.classList.remove('is-invalid');
            if (input.value.trim() !== '') {
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
            }
            if (errorEl) errorEl.textContent = '';
        }
    }

    function clearFormErrors(form) {
        if (!form) return;
        form.querySelectorAll('input').forEach((input) => {
            input.classList.remove('is-invalid', 'is-valid');
        });
        form.querySelectorAll('.field-error').forEach((el) => {
            el.textContent = '';
        });
    }

    function applyBackendErrors(form, errors) {
        if (!form || !errors || typeof errors !== 'object') return;
        Object.entries(errors).forEach(([field, message]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                setFieldError(input, String(message));
            }
        });
    }

    function setButtonLoading(button, isLoading, loadingText = 'Procesando...') {
        if (!button) return;
        if (isLoading) {
            button.dataset.originalText = button.textContent;
            button.textContent = loadingText;
            button.disabled = true;
        } else {
            button.textContent = button.dataset.originalText || button.textContent;
            button.disabled = false;
        }
    }

    function showToast(message, type = 'success') {
        let toast = document.getElementById('app-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'app-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.className = `toast ${type}`;
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => {
            toast.classList.add('hidden');
        }, 3400);
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ---------- Frontend Validators ----------
    const Validators = {
        name(value) {
            const v = value.trim();
            if (!v) return 'El nombre es obligatorio.';
            if (v.length < 2 || v.length > 100) return 'El nombre debe tener entre 2 y 100 caracteres.';
            if (!NAME_REGEX.test(v)) return 'El nombre solo puede contener letras y espacios.';
            return '';
        },
        email(value) {
            const v = value.trim();
            if (!v) return 'El correo es obligatorio.';
            if (v.length > 254 || !EMAIL_REGEX.test(v)) return 'Ingresa un correo electrónico válido.';
            return '';
        },
        loginPassword(value) {
            if (!value.trim()) return 'La contraseña es obligatoria.';
            return '';
        },
        strongPassword(value, { required = true } = {}) {
            if (!value && !required) return '';
            if (!value) return 'La contraseña es obligatoria.';
            if (value.length < 6) return 'La contraseña debe tener al menos 6 caracteres.';
            if (!/[A-Za-z]/.test(value) || !/\d/.test(value)) {
                return 'La contraseña debe incluir al menos una letra y un número.';
            }
            return '';
        },
        confirmPassword(password, confirmation, { required = true } = {}) {
            if (!password && !confirmation && !required) return '';
            if (!confirmation) return 'Debes confirmar la contraseña.';
            if (password !== confirmation) return 'Las contraseñas no coinciden.';
            return '';
        },
        token(value) {
            const v = value.trim();
            if (!v) return 'El código de recuperación es obligatorio.';
            if (v.length < 16) return 'El código de recuperación no es válido.';
            return '';
        },
    };

    function bindLiveValidation(form, rulesMap) {
        Object.entries(rulesMap).forEach(([fieldName, validateFn]) => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (!input) return;
            const run = () => setFieldError(input, validateFn(input.value, form));
            input.addEventListener('blur', run);
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid') || input.value.trim() !== '') {
                    run();
                }
            });
        });
    }

    function validateForm(form, rulesMap) {
        let valid = true;
        Object.entries(rulesMap).forEach(([fieldName, validateFn]) => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (!input) return;
            const msg = validateFn(input.value, form);
            setFieldError(input, msg);
            if (msg) valid = false;
        });
        return valid;
    }

    // ---------- 1. Login Page ----------
    function initLoginPage() {
        const form = document.getElementById('login-form');
        if (!form) return;

        const alertEl = document.getElementById('login-alert');
        const submitBtn = form.querySelector('button[type="submit"]');
        const rules = {
            email: (v) => Validators.email(v),
            password: (v) => Validators.loginPassword(v),
        };

        bindLiveValidation(form, rules);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(alertEl);

            if (!validateForm(form, rules)) {
                showAlert(alertEl, 'Corrige los errores marcados antes de continuar.', 'error');
                return;
            }

            setButtonLoading(submitBtn, true, 'Iniciando sesión...');
            const payload = {
                email: form.email.value.trim(),
                password: form.password.value,
            };

            try {
                const res = await apiRequest('/api/login', { method: 'POST', body: payload });
                if (!res.ok) {
                    applyBackendErrors(form, res.data.errors);
                    showAlert(alertEl, res.data.message || 'No se pudo iniciar sesión.', 'error');
                    return;
                }

                showAlert(alertEl, res.data.message || 'Acceso concedido. Redirigiendo...', 'success');
                window.location.href = res.data.redirect || '/';
            } catch {
                showAlert(alertEl, 'Error de conexión con el servidor.', 'error');
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });
    }

    // ---------- 2. Register Page ----------
    function initRegisterPage() {
        const form = document.getElementById('register-form');
        if (!form) return;

        const alertEl = document.getElementById('register-alert');
        const submitBtn = form.querySelector('button[type="submit"]');
        const rules = {
            name: (v) => Validators.name(v),
            email: (v) => Validators.email(v),
            password: (v) => Validators.strongPassword(v, { required: true }),
            password_confirmation: (v, f) =>
                Validators.confirmPassword(f.password.value, v, { required: true }),
        };

        bindLiveValidation(form, rules);
        form.password.addEventListener('input', () => {
            if (form.password_confirmation.value) {
                setFieldError(
                    form.password_confirmation,
                    Validators.confirmPassword(form.password.value, form.password_confirmation.value, { required: true })
                );
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(alertEl);

            if (!validateForm(form, rules)) {
                showAlert(alertEl, 'Revisa los campos marcados antes de crear tu cuenta.', 'error');
                return;
            }

            setButtonLoading(submitBtn, true, 'Creando usuario...');
            const payload = {
                name: form.name.value.trim(),
                email: form.email.value.trim(),
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
            };

            try {
                const res = await apiRequest('/api/register', { method: 'POST', body: payload });
                if (!res.ok) {
                    applyBackendErrors(form, res.data.errors);
                    showAlert(alertEl, res.data.message || 'No se pudo crear el usuario.', 'error');
                    return;
                }

                showAlert(alertEl, '¡Usuario creado con éxito! Redirigiendo al login...', 'success');
                setTimeout(() => {
                    window.location.href = res.data.redirect || '/login';
                }, 1100);
            } catch {
                showAlert(alertEl, 'Error de conexión con el servidor.', 'error');
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });
    }

    // ---------- 3. Forgot / Reset Password Page (Direct, no email) ----------
    function initForgotPasswordPage() {
        const resetForm = document.getElementById('reset-form');
        if (!resetForm) return;

        const alertEl = document.getElementById('recovery-alert');
        const resetRules = {
            email: (v) => Validators.email(v),
            password: (v) => Validators.strongPassword(v, { required: true }),
            password_confirmation: (v, f) =>
                Validators.confirmPassword(f.password.value, v, { required: true }),
        };

        bindLiveValidation(resetForm, resetRules);
        resetForm.password.addEventListener('input', () => {
            if (resetForm.password_confirmation.value) {
                setFieldError(
                    resetForm.password_confirmation,
                    Validators.confirmPassword(resetForm.password.value, resetForm.password_confirmation.value, { required: true })
                );
            }
        });

        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(alertEl);

            if (!validateForm(resetForm, resetRules)) {
                showAlert(alertEl, 'Corrige los errores marcados antes de cambiar la contraseña.', 'error');
                return;
            }

            const btn = resetForm.querySelector('button[type="submit"]');
            setButtonLoading(btn, true, 'Actualizando...');

            try {
                const res = await apiRequest('/api/reset-password', {
                    method: 'POST',
                    body: {
                        email: resetForm.email.value.trim(),
                        password: resetForm.password.value,
                        password_confirmation: resetForm.password_confirmation.value,
                    },
                });

                if (!res.ok) {
                    applyBackendErrors(resetForm, res.data.errors);
                    showAlert(alertEl, res.data.message || 'No se pudo cambiar la contraseña.', 'error');
                    return;
                }

                showAlert(alertEl, res.data.message + ' Redirigiendo...', 'success');
                setTimeout(() => {
                    window.location.href = res.data.redirect || '/login';
                }, 1100);
            } catch {
                showAlert(alertEl, 'Error de conexión con el servidor.', 'error');
            } finally {
                setButtonLoading(btn, false);
            }
        });
    }

    // ---------- 4. Dashboard User CRUD ----------
    function initDashboardCrud() {
        const tbody = document.getElementById('users-tbody');
        if (!tbody) return;

        const currentEmailEl = document.getElementById('current-user-email');
        const searchInput = document.getElementById('user-search');
        const countBadge = document.getElementById('users-count');
        const newUserBtn = document.getElementById('btn-new-user');

        // User Create/Edit Modal
        const userModal = document.getElementById('user-modal');
        const userModalTitle = document.getElementById('user-modal-title');
        const userForm = document.getElementById('user-form');
        const userModalAlert = document.getElementById('user-modal-alert');
        const passwordHint = document.getElementById('password-optional-hint');

        // Delete Confirm Modal
        const deleteModal = document.getElementById('delete-modal');
        const deleteUserLabel = document.getElementById('delete-user-label');
        const confirmDeleteBtn = document.getElementById('btn-confirm-delete');

        let usersCache = [];
        let editingUserId = null;
        let deletingUserId = null;
        let searchTimer = null;

        function getCrudRules() {
            const isCreate = editingUserId === null;
            return {
                name: (v) => Validators.name(v),
                email: (v) => Validators.email(v),
                password: (v, f) => {
                    const hasConfirm = f.password_confirmation.value.length > 0;
                    return Validators.strongPassword(v, { required: isCreate || hasConfirm });
                },
                password_confirmation: (v, f) => {
                    const hasPass = f.password.value.length > 0;
                    return Validators.confirmPassword(f.password.value, v, { required: isCreate || hasPass });
                },
            };
        }

        // Live validation for modal inputs
        ['name', 'email', 'password', 'password_confirmation'].forEach((field) => {
            const input = userForm.querySelector(`[name="${field}"]`);
            if (!input) return;
            const validateField = () => {
                const rules = getCrudRules();
                setFieldError(input, rules[field](input.value, userForm));
            };
            input.addEventListener('blur', validateField);
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid') || input.value.trim() !== '') {
                    validateField();
                }
            });
        });

        async function loadUsers(search = '') {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Cargando usuarios...</td></tr>';
            const query = search.trim() ? `?search=${encodeURIComponent(search.trim())}` : '';
            try {
                const res = await apiRequest(`/api/users${query}`);
                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    tbody.innerHTML = `<tr><td colspan="5" class="empty-state">${escapeHtml(res.data.message)}</td></tr>`;
                    return;
                }

                usersCache = res.data.users || [];
                renderUsers(usersCache);
            } catch {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Error de conexión al cargar usuarios.</td></tr>';
            }
        }

        function renderUsers(users) {
            const currentEmail = (currentEmailEl?.textContent || '').trim().toLowerCase();
            countBadge.textContent = `${users.length} ${users.length === 1 ? 'usuario' : 'usuarios'}`;

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">No se encontraron usuarios.</td></tr>';
                return;
            }

            tbody.innerHTML = users
                .map((user) => {
                    const isSelf = String(user.email).toLowerCase() === currentEmail;
                    return `
                        <tr>
                            <td>#${escapeHtml(user.id)}</td>
                            <td>
                                <strong>${escapeHtml(user.name)}</strong>
                                ${isSelf ? '<span class="user-pill" style="margin-left:6px;padding:2px 8px;font-size:0.75rem;">Tú</span>' : ''}
                            </td>
                            <td>${escapeHtml(user.email)}</td>
                            <td>${escapeHtml(user.created_at)}</td>
                            <td>
                                <div class="actions-cell">
                                    <button type="button" class="btn btn-secondary btn-sm" data-action="edit" data-id="${user.id}">Editar</button>
                                    <button type="button" class="btn btn-danger btn-sm" data-action="delete" data-id="${user.id}" ${isSelf ? 'disabled title="No puedes eliminar tu propia cuenta"' : ''}>Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                })
                .join('');
        }

        function openUserModal(user = null) {
            clearFormErrors(userForm);
            hideAlert(userModalAlert);
            userForm.reset();

            if (user) {
                editingUserId = user.id;
                userModalTitle.textContent = `Editar usuario #${user.id}`;
                userForm.name.value = user.name;
                userForm.email.value = user.email;
                passwordHint.classList.remove('hidden');
            } else {
                editingUserId = null;
                userModalTitle.textContent = 'Nuevo usuario';
                passwordHint.classList.add('hidden');
            }

            userModal.classList.remove('hidden');
            userForm.name.focus();
        }

        function closeUserModal() {
            userModal.classList.add('hidden');
            editingUserId = null;
        }

        function openDeleteModal(user) {
            deletingUserId = user.id;
            deleteUserLabel.textContent = `${user.name} (${user.email})`;
            deleteModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            deletingUserId = null;
        }

        newUserBtn.addEventListener('click', () => openUserModal(null));

        document.querySelectorAll('[data-close-modal="user"]').forEach((btn) => {
            btn.addEventListener('click', closeUserModal);
        });
        document.querySelectorAll('[data-close-modal="delete"]').forEach((btn) => {
            btn.addEventListener('click', closeDeleteModal);
        });

        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const id = Number(btn.dataset.id);
            const user = usersCache.find((u) => Number(u.id) === id);
            if (!user) return;

            if (btn.dataset.action === 'edit') {
                openUserModal(user);
            } else if (btn.dataset.action === 'delete') {
                openDeleteModal(user);
            }
        });

        userForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert(userModalAlert);

            const rules = getCrudRules();
            if (!validateForm(userForm, rules)) {
                showAlert(userModalAlert, 'Corrige los errores marcados antes de guardar.', 'error');
                return;
            }

            const submitBtn = userForm.querySelector('button[type="submit"]');
            setButtonLoading(submitBtn, true, 'Guardando...');

            const payload = {
                name: userForm.name.value.trim(),
                email: userForm.email.value.trim(),
                password: userForm.password.value,
                password_confirmation: userForm.password_confirmation.value,
            };

            const isEdit = editingUserId !== null;
            const url = isEdit ? `/api/users/${editingUserId}` : '/api/users';
            const method = isEdit ? 'PUT' : 'POST';
            const previousEmail = isEdit
                ? (usersCache.find((u) => Number(u.id) === editingUserId)?.email || '').toLowerCase()
                : '';

            try {
                const res = await apiRequest(url, { method, body: payload });
                if (!res.ok) {
                    applyBackendErrors(userForm, res.data.errors);
                    showAlert(userModalAlert, res.data.message || 'No se pudo guardar el usuario.', 'error');
                    return;
                }

                // Update navbar pill if user edited their own email
                if (
                    isEdit
                    && currentEmailEl
                    && previousEmail === currentEmailEl.textContent.trim().toLowerCase()
                    && res.data.user?.email
                ) {
                    currentEmailEl.textContent = res.data.user.email;
                }

                closeUserModal();
                showToast(res.data.message || 'Operación realizada con éxito.', 'success');
                await loadUsers(searchInput.value);
            } catch {
                showAlert(userModalAlert, 'Error de conexión con el servidor.', 'error');
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });

        confirmDeleteBtn.addEventListener('click', async () => {
            if (deletingUserId === null) return;
            setButtonLoading(confirmDeleteBtn, true, 'Eliminando...');

            try {
                const res = await apiRequest(`/api/users/${deletingUserId}`, { method: 'DELETE' });
                if (!res.ok) {
                    showToast(res.data.message || 'No se pudo eliminar el usuario.', 'error');
                    return;
                }

                closeDeleteModal();
                showToast(res.data.message || 'Usuario eliminado.', 'success');
                await loadUsers(searchInput.value);
            } catch {
                showToast('Error de conexión al eliminar el usuario.', 'error');
            } finally {
                setButtonLoading(confirmDeleteBtn, false);
            }
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadUsers(searchInput.value);
            }, 250);
        });

        loadUsers();
    }

    // Boot
    document.addEventListener('DOMContentLoaded', () => {
        initLoginPage();
        initRegisterPage();
        initForgotPasswordPage();
        initDashboardCrud();
    });
})();
