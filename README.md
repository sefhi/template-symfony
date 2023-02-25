![Symfony 6](https://img.shields.io/badge/Symfony-6.2-blueviolet)
![PHP Version](https://img.shields.io/badge/php-8.2-blue.svg)
[![CI](https://github.com/sefhirot69/template-symfony/actions/workflows/build.yml/badge.svg)](https://github.com/sefhirot69/template-symfony/actions/workflows/build.yml)
--------------------------------------

# 🚀 Template Symfony

Este es un template para Symfony 6 en php 8.2, con algunas configuraciones ya predefinidas. Para empezar a desarrollar tu propia
API o microservicio.

## 🛠️ Requisitos

- 🐳 Docker
- __Opcional__: Instalar el comando `make` para mejorar el punto de entrada a nuestra aplicación.
    1. [Instalar en OSX](https://formulae.brew.sh/formula/make)
    2. [Instalar en Window](https://parzibyte.me/blog/2020/12/30/instalar-make-windows/#Descargar_make)

## ⚙️ Configuración del entorno

1. Clona el repositorio o haz un fork
2. Escribe por terminal el comando `make`. Este comando instalara todo lo necesario para arrancar la aplicación.

## 🚀 Comandos Útiles

Este proyecto incluye un Makefile con algunos comandos útiles para el desarrollo. Puedes ejecutarlos con el comando *
*make** seguido del nombre del comando.

### Comandos de Docker Compose

* `make start`: Inicia los contenedores de Docker Compose.
* `make stop`: Detiene los contenedores de Docker Compose.
* `make down`: Detiene y elimina los contenedores de Docker Compose.
* `make recreate`: Reinicia los contenedores de Docker Compose.
* `make rebuild`: Reconstruye los contenedores de Docker Compose.

### Comandos de Composer

* `make deps`: Instala las dependencias del proyecto.
* `make update-deps`: Actualiza las dependencias del proyecto.
* `make clear`: Limpia la caché de Symfony.
* `make bash`: Abre una sesión de terminal en el contenedor de Docker.

### Otros comandos

* `make test`: Ejecuta los tests del proyecto.
* `make lint`: Verifica el cumplimiento de los estándares de codificación.
* `make style`: Corrige los problemas de formato de código.
* `make static-analysis`: Verifica la calidad del código fuente.


📝 Recuerda que puedes consultar los detalles de cada comando en el Makefile del proyecto.

¡Que lo disfrutes! 😎