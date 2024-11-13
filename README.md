# 🌟 CampusHub - MVP de Gestión de Eventos Universitarios

## 📋 Descripción

**CampusHub** es una plataforma web minimalista diseñada como un **Producto Mínimo Viable (MVP)** para que las universidades puedan crear, gestionar y promover eventos académicos y extracurriculares. Esta herramienta permite a los estudiantes ver los eventos disponibles en su campus y registrarse de manera gratuita, incentivando su participación en actividades de su interés. CampusHub está construido en **PHP, HTML y CSS** y utiliza **MySQL** para el almacenamiento de datos.

## ✨ Características

- **Autenticación de Usuarios**: Sistema de registro e inicio de sesión para estudiantes y administradores.
- **Gestión de Eventos**: Los administradores pueden crear, actualizar y gestionar eventos en la plataforma.
- **Inscripciones de Estudiantes**: Los estudiantes pueden visualizar los eventos y registrarse de forma gratuita.
- **Panel de Administración**: Los administradores cuentan con herramientas para gestionar eventos y ver las inscripciones de los estudiantes.
- **Interfaz Sencilla**: Diseñada para un acceso rápido y una navegación intuitiva.
- **Seguridad**: Gestión de sesiones y cookies, con protección contra inyecciones SQL mediante consultas preparadas.

## 🎨 Paleta de Colores

La plataforma usa una paleta de tonos verdes para reflejar una estética fresca y profesional:

- **Color principal (Botones, Títulos)**: #2E7D32 (verde oscuro)
- **Color secundario (Resaltados, Etiquetas)**: #66BB6A (verde claro)
- **Fondo**: #E8F5E9 (verde muy claro)
- **Textos primarios**: #1B5E20 (verde oscuro)
- **Textos secundarios**: #4CAF50 (verde intermedio)

## 📦 Requisitos

- **Servidor Web**: Apache
- **PHP**
- **MySQL**
- **PHP PDO**: Extensión para manejo de bases de datos con PDO

## ⚙️ Instalación

1. **Clonar el Repositorio**
   - Clona el repositorio a tu servidor local o de desarrollo:
     ```bash
     git clone <url-del-repositorio>
     cd campusHub
     ```

2. **Configurar la Base de Datos**
   - Crea una base de datos en MySQL llamada **campushubdb**.
   - Importa el archivo **campushubdb.sql** para crear las tablas necesarias.

3. **Configuración de Conexión a la Base de Datos**
   - Edita el archivo **includes/config.php** con los datos de tu base de datos:

     ```php
     <?php
     $host = 'localhost';
     $dbname = 'campushubdb';
     $username = 'tu_usuario';
     $password = 'tu_contraseña';
     $port = 'tu_puerto'
     ?>
     ```

4. **Iniciar el Servidor**
   - Inicia tu servidor y accede a la aplicación en **http://localhost/campusHub**.
