<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requisitos para correr el proyecto

- Contar con un entorno de desarrollo de PHP y MySQL como [Xampp](https://www.apachefriends.org/es/index.html).
- Es necesario tener en el sistema operativo [composer de manera global](https://www.youtube.com/watch?v=lPabQsgHvu0).
- Tener instalado y configurado **[GIT](https://www.youtube.com/watch?v=wHh3IgJvXcE)**.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## 1. Clonar el repositorio del proyecto en Laravel

Para clonar el proyecto abre una terminal o consola de comandos y escribe la siguiente nomenclatura:
```bash
git clone https://github.com/gisuss/Back-end_Auth_SC.git
```

## 2. Instalar dependencias del proyecto

Cuando guardas tu proyecto Laravel en un repositorio GIT, en el archivo **.gitignore** se excluye la carpeta vendor que es donde están las librerías que usa tu proyecto, es por eso que se debe correr en la terminal una instrucción que tome del archivo **composer.json** todas las referencias de las librerías que deben estar instaladas en tu proyecto.

Ingresa desde la terminal a la carpeta del proyecto y escribe:
```bash
composer install
```
Este comando instalará todas las librerías que están declaradas para el proyecto.

### 3. Generar archivo .env

Por seguridad el archivo **.env** está excluido del repositorio, para generar uno nuevo se toma como plantilla el archivo **.env.example** para copiar este archivo en uno nuevo escribe en tu terminal:
```bash
cp .env.example .env
```

## 4. Generar Key

Para que el proyecto en Laravel corra sin problemas es necesario generar una key de seguirdad, para ello en tu terminal corre el siguiente comando:
```bash
php artisan key:generate
```
Esta key nueva se agregará a tu archivo **.env**

## 5. Crear base de datos

Sí tu proyecto en Laravel funciona haciendo consultas a una base de datos entonces tienes que crear una nueva base de datos, la forma más rápida para crearla es desde tu administrador de base de datos (phpmyadmin si usas Xampp). Crea una nueva base de datos vacía con el nombre del proyecto **crudsc_sanctum**


## 6. Correr migraciones y seeds

Sí tu proyecto cuenta con seeders y factories para poblar ciertas tablas en tu base de datos como usuarios para tu sistema escribe en la terminal:
```bash
php artisan migrate --seed
```

## 7. Correr el Servidor

Por último, ponemos acorrer el proyecto escribiendo en la terminal el siguiente comando:
```bash
php artisan serve
```
