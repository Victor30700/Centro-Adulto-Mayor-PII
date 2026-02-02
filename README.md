# 🏥 Sistema Integral de Gestión - Centro de Adulto Mayor

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
    <img src="https://img.shields.io/badge/Vite-6.0-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

---

## 📖 Descripción del Proyecto

El **Sistema de Gestión para Centro de Adulto Mayor** es una plataforma web integral desarrollada para optimizar la administración, atención y seguimiento de los adultos mayores beneficiarios. Este sistema permite la digitalización de expedientes y la interconexión entre las áreas **Médica, Legal, Social y Administrativa**.

El objetivo principal es brindar una herramienta eficiente para el registro de historias clínicas, seguimiento legal, intervenciones sociales y terapias de rehabilitación, asegurando un control detallado y profesional de cada caso.

## 🚀 Características Principales

### 🛠️ Módulo Administrativo
- **Gestión de Usuarios y Roles:** Control de acceso granular (Administrador, Legal, Responsable de Salud).
- **Registro de Beneficiarios:** Base de datos centralizada de adultos mayores.
- **Auditoría:** Papelera de reciclaje y restauración de registros eliminados.

### 🩺 Módulo Médico y Salud
- **Historia Clínica Digital:** Registro completo de antecedentes, diagnósticos y evoluciones.
- **Enfermería:** Control de signos vitales, administración de medicamentos y curaciones.
- **Exámenes Complementarios:** Gestión de resultados y archivos adjuntos.

### ⚖️ Módulo Legal y Social (Protección)
- **Gestión de Casos:** Registro de denuncias, seguimiento de casos y orientaciones legales.
- **Fichas Sociales:** Croquis de vivienda, composición del grupo familiar y situación socioeconómica.
- **Intervenciones:** Registro de visitas domiciliarias y acciones tomadas.
- **Documentación:** Generación automática de Anexos (N3, N5) y reportes.

### 🤸 Módulo de Rehabilitación (Fisioterapia y Kinesiología)
- **Fichas de Tratamiento:** Registro de sesiones de fisioterapia y kinesiología.
- **Evolución del Paciente:** Seguimiento del progreso físico y funcional.
- **Reportes Especializados:** Exportación de fichas en formatos Word y Excel.

## 💻 Stack Tecnológico

- **Backend:** [Laravel Framework](https://laravel.com) (v12.x)
- **Lenguaje:** PHP ^8.2
- **Frontend:** Blade Templates, [Tailwind CSS](https://tailwindcss.com) v4, [Vite](https://vitejs.dev)
- **Base de Datos:** MySQL / MariaDB
- **Generación de Documentos:**
  - `barryvdh/laravel-dompdf`: Exportación a PDF.
  - `phpoffice/phpword`: Generación de documentos Word.
  - `phpoffice/phpspreadsheet`: Reportes en Excel.

## ⚙️ Requisitos del Sistema

Asegúrese de tener instalado lo siguiente antes de comenzar:

- PHP >= 8.2
- Composer
- Node.js & NPM
- Servidor de Base de Datos (MySQL/MariaDB)

## 🔧 Instalación y Configuración

Siga estos pasos para desplegar el proyecto en su entorno local:

1.  **Clonar el repositorio**
    ```bash
    git clone https://github.com/usuario/centro-adulto-mayor.git
    cd centro-adulto-mayor
    ```

2.  **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de Frontend**
    ```bash
    npm install
    ```

4.  **Configurar el entorno**
    Duplique el archivo de ejemplo y configure sus credenciales de base de datos.
    ```bash
    cp .env.example .env
    ```
    Edite el archivo `.env` con su configuración de base de datos:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nombre_de_su_base_de_datos
    DB_USERNAME=su_usuario
    DB_PASSWORD=su_contraseña
    ```

5.  **Generar clave de aplicación**
    ```bash
    php artisan key:generate
    ```

6.  **Ejecutar migraciones y seeders**
    Esto creará las tablas y poblará la base de datos con los roles y usuarios iniciales.
    ```bash
    php artisan migrate --seed
    ```

7.  **Compilar activos y ejecutar servidor**
    Abra dos terminales:
    
    *Terminal 1 (Vite - Desarrollo Frontend):*
    ```bash
    npm run dev
    ```
    
    *Terminal 2 (Servidor Laravel):*
    ```bash
    php artisan serve
    ```

El sistema estará accesible en `http://localhost:8000`.

## 📂 Estructura de Directorios Clave

```
app/
├── Http/Controllers/   # Controladores (Lógica de negocio por módulos)
├── Models/             # Modelos Eloquent (Representación de datos)
database/
├── migrations/         # Definición del esquema de base de datos
├── seeders/            # Datos iniciales de prueba y configuración
resources/
├── views/              # Vistas Blade (Interfaz de usuario)
routes/
└── web.php             # Definición de rutas del sistema
```

## 📄 Licencia

Este proyecto es software privado y confidencial. Todos los derechos reservados.

---
**Desarrollado para la Alcaldía - Gestión 2026**