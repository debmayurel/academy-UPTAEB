# Academy UPTAEB — Sistema de Autenticación y CRUD de Usuarios

Aplicación web desarrollada en **PHP** bajo arquitectura **MVC**, con peticiones **AJAX (`fetch`)**, **validaciones sincronizadas en Frontend y Backend**, registro de usuarios, recuperación directa de contraseña (sin correo) y un **CRUD completo de usuarios**.

---

## Características principales

- **Arquitectura MVC**: separación clara en Modelos, Repositorios, Servicios, Validadores, Controladores, Vistas y un Router propio.
- **Autenticación**: inicio y cierre de sesión con contraseñas cifradas (`password_hash` / `password_verify`) y manejo de sesiones PHP.
- **Crear usuario (Registro público)**: creación de nuevas cuentas desde `/register`.
- **Recuperar contraseña (sin correo)**: flujo directo y sencillo desde `/forgot-password` para restablecer la contraseña validando la cuenta registrada.
- **CRUD de usuarios**: listar, buscar en tiempo real, crear, editar (con contraseña opcional) y eliminar usuarios desde el panel principal, protegiendo la cuenta con sesión activa contra auto-eliminación.
- **AJAX**: todos los formularios (login, registro, recuperación y CRUD) se comunican con el backend en JSON sin recargar la página.
- **Validaciones Frontend y Backend**: validación en tiempo real en el navegador (`input`/`blur`) y validación en el servidor con respuestas `422` detalladas por campo.
- **Soporte de base de datos**: compatible con **SQLite** (por defecto) y **MySQL** mediante `.env`.

---

## Requisitos

- **PHP 8.2+** con extensiones `pdo_sqlite` (o `pdo_mysql`) y `mbstring` habilitadas.
- **Composer** (opcional si ya tienes la carpeta `vendor/`).

---

## Instalación y ejecución

1. **Clonar el repositorio** e ingresar al directorio del proyecto:
   ```bash
   git clone <url-del-repositorio>
   cd academy-UPTAEB
   ```

2. **Instalar dependencias** (si no existe `vendor/`):
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**:
   Copia `.env.example` a `.env` (por defecto usa SQLite en `database/login.db`):
   ```env
   DB_CONNECTION=sqlite
   DB_SQLITE_PATH=database/login.db
   ```
   Si deseas usar MySQL, cambia `DB_CONNECTION=mysql` y completa `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

4. **Inicializar la base de datos**:
   ```bash
   php database/init_db.php
   ```
   Esto crea las tablas necesarias y registra un usuario administrador por defecto:
   - **Correo:** `admin@beba.com`
   - **Contraseña:** `123456`

5. **Levantar el servidor local de PHP**:
   ```bash
   php -S localhost:8000 -t public public/index.php
   ```

6. Abrir en el navegador: **http://localhost:8000**

---

## Estructura del proyecto

```text
academy-UPTAEB/
├── database/
│   ├── init_db.php                # Inicialización y migración de BD + usuario semilla
│   ├── schema.sql                 # Esquema SQLite
│   ├── schema.mysql.sql           # Esquema MySQL
│   └── login.db                   # Base de datos SQLite local
├── public/
│   ├── index.php                  # Front controller y definición de rutas
│   └── assets/
│       ├── css/app.css            # Estilos globales, formularios, tabla y modales
│       └── js/app.js              # Peticiones AJAX y validaciones Frontend
├── src/
│   ├── Config/
│   │   └── DatabaseConnectionFactory.php
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── PasswordResetController.php
│   │   └── UserController.php
│   ├── Core/
│   │   └── Router.php
│   ├── Interfaces/
│   ├── Models/
│   │   └── User.php
│   ├── Repositories/
│   │   └── UserRepository.php
│   ├── Services/
│   │   ├── AuthenticationService.php
│   │   ├── DbAuthenticator.php
│   │   ├── PasswordResetService.php
│   │   ├── PhpSessionManager.php
│   │   └── UserService.php
│   ├── Validation/
│   │   ├── LoginInputValidator.php
│   │   ├── PasswordResetValidator.php
│   │   ├── UserValidator.php
│   │   └── ValidationResult.php
│   └── views/
│       ├── login.php
│       ├── register.php
│       ├── forgot_password.php
│       └── dashboard.php
├── .env.example
└── composer.json
```

---

## Rutas disponibles

### Vistas (HTML)

| Método | Ruta | Descripción |
| :--- | :--- | :--- |
| `GET` | `/` | Dashboard con CRUD de usuarios (requiere sesión) |
| `GET` | `/login` | Formulario de inicio de sesión |
| `GET` | `/register` | Formulario de creación de usuario |
| `GET` | `/forgot-password` | Formulario de recuperación directa de contraseña |
| `GET` | `/logout` | Cerrar sesión y redirigir a `/login` |

### API JSON (AJAX)

| Método | Ruta | Descripción |
| :--- | :--- | :--- |
| `POST` | `/api/login` | Autenticar usuario |
| `POST` | `/api/register` | Registrar un nuevo usuario |
| `POST` | `/api/logout` | Cerrar sesión |
| `POST` | `/api/reset-password` | Cambiar contraseña directamente por correo |
| `GET` | `/api/users` | Listar usuarios (soporta `?search=texto`) |
| `GET` | `/api/users/{id}` | Obtener un usuario por ID |
| `POST` | `/api/users` | Crear usuario desde el CRUD |
| `PUT` | `/api/users/{id}` | Actualizar usuario (contraseña opcional) |
| `DELETE` | `/api/users/{id}` | Eliminar usuario |

---

## Reglas de validación (Frontend y Backend)

- **Nombre**: obligatorio, entre 2 y 100 caracteres, solo letras y espacios.
- **Correo**: obligatorio, formato válido, único en la base de datos.
- **Contraseña** (registro, creación, recuperación y cambio en edición): mínimo 6 caracteres, al menos una letra y un número.
- **Confirmación de contraseña**: debe coincidir exactamente con la contraseña ingresada.
