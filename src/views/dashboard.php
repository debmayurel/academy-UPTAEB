<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Gestión de Usuarios</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">Academy UPTAEB</div>
        <div class="navbar-user">
            <span class="user-pill" id="current-user-email"><?= htmlspecialchars($currentUserEmail ?? $_SESSION['user_email'] ?? 'usuario', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="/logout" class="btn btn-secondary btn-sm">Cerrar sesión</a>
        </div>
    </header>

    <main class="dashboard-main">
        <section class="panel-card">
            <div class="panel-header">
                <div>
                    <h1>Gestión de Usuarios</h1>
                    <p class="panel-subtitle">Gestión de Usuarios</p>
                </div>
                <button type="button" id="btn-new-user" class="btn btn-primary">+ Nuevo usuario</button>
            </div>

            <div class="toolbar">
                <div class="search-wrap">
                    <input type="search" id="user-search" placeholder="Buscar por nombre o correo..." aria-label="Buscar usuarios">
                </div>
                <span class="count-badge" id="users-count">0 usuarios</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Fecha de registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        <tr>
                            <td colspan="5" class="empty-state">Cargando usuarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Create / Edit User Modal -->
    <div id="user-modal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
        <div class="modal">
            <div class="modal-header">
                <h3 id="user-modal-title">Nuevo usuario</h3>
                <button type="button" class="modal-close" data-close-modal="user" aria-label="Cerrar">&times;</button>
            </div>

            <div id="user-modal-alert" class="alert"></div>

            <form id="user-form" novalidate>
                <div class="form-group">
                    <label for="crud-name">Nombre completo</label>
                    <input type="text" id="crud-name" name="name" placeholder="Ej. Carlos Gómez" required>
                    <small class="field-error"></small>
                </div>

                <div class="form-group">
                    <label for="crud-email">Correo electrónico</label>
                    <input type="email" id="crud-email" name="email" placeholder="correo@ejemplo.com" required>
                    <small class="field-error"></small>
                </div>

                <div class="form-group">
                    <label for="crud-password">Contraseña</label>
                    <input type="password" id="crud-password" name="password" placeholder="Mínimo 6 caracteres, letras y números">
                    <small id="password-optional-hint" class="field-hint hidden">Déjala vacía si no deseas cambiar la contraseña.</small>
                    <small class="field-error"></small>
                </div>

                <div class="form-group">
                    <label for="crud-password-confirmation">Confirmar contraseña</label>
                    <input type="password" id="crud-password-confirmation" name="password_confirmation" placeholder="Repite la contraseña">
                    <small class="field-error"></small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal="user">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
        <div class="modal">
            <div class="modal-header">
                <h3 id="delete-modal-title">Eliminar usuario</h3>
                <button type="button" class="modal-close" data-close-modal="delete" aria-label="Cerrar">&times;</button>
            </div>

            <p>¿Seguro que deseas eliminar al usuario <strong id="delete-user-label"></strong>? Esta acción no se puede deshacer.</p>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-close-modal="delete">Cancelar</button>
                <button type="button" id="btn-confirm-delete" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
