# 🏈 NFL Pick'em All

Aplicación web de pronósticos de la NFL desarrollada con **Laravel y Livewire** como proyecto personal.

El objetivo es permitir que un grupo de usuarios realice sus pronósticos semanales de los partidos de la NFL, gestione los resultados reales y consulte los aciertos y la clasificación de la competición.

## 🚀 Características

- Gestión de temporadas y jornadas.
- Gestión de partidos por jornada.
- Pronósticos **1X2**:
    - `1` — victoria del equipo local.
    - `X` — empate.
    - `2` — victoria del equipo visitante.

- Cierre de las predicciones al finalizar el plazo establecido.
- Validación para garantizar que se pronostiquen todos los partidos de una jornada.
- Modificación de pronósticos mientras la jornada permanece abierta.
- Introducción de resultados reales por parte del administrador.
- Cálculo automático de aciertos.
- Sistema de puntuación semanal.
- Clasificación general.
- Diferenciación entre usuarios y administradores.
- Panel de administración para gestionar jornadas y partidos.
- Estados de las jornadas:
    - `upcoming`
    - `open`
    - `closed`
    - `finished`

- Registro de nuevos participantes mediante código de invitación.
- Limitación del número de participantes.
- Interfaz responsive adaptada a escritorio y dispositivos móviles.

## 🛠️ Tecnologías

| Tecnología      | Uso                                          |
| --------------- | -------------------------------------------- |
| PHP 8.4         | Lenguaje principal                           |
| Laravel 13      | Framework backend                            |
| Livewire 4      | Componentes dinámicos e interacción frontend |
| Laravel Fortify | Autenticación                                |
| Flux            | Componentes de interfaz                      |
| Tailwind CSS    | Estilos y diseño                             |
| SQLite          | Base de datos                                |
| Vite            | Gestión de assets                            |
| Git             | Control de versiones                         |
| GitHub          | Repositorio y distribución del código        |

## 🏗️ Arquitectura

La aplicación está organizada alrededor de las principales entidades del dominio:

```text
User
 │
 ├── Predictions
 │
 └── RoundResults

Round
 │
 ├── Games
 │    └── Predictions
 │
 └── RoundResults
```

### Principales modelos

- `User` — usuarios y permisos de administración.
- `Round` — temporada, jornada, fechas y estado.
- `Game` — partidos de cada jornada y sus resultados.
- `Prediction` — pronóstico realizado por cada usuario para un partido.
- `RoundResult` — resultado obtenido por cada usuario en una jornada.

Las relaciones entre estos modelos se gestionan mediante **Eloquent ORM**.

## 🎯 Sistema de puntuación

Cada usuario obtiene un número de aciertos por jornada en función de sus pronósticos y los resultados reales.

La puntuación semanal se determina comparando los aciertos obtenidos por todos los participantes:

- El usuario o usuarios con el mayor número de aciertos obtienen **1 punto**.
- El resto obtiene **0 puntos**.
- Si ningún participante consigue acertar ningún partido, todos reciben **0 puntos**.

Los resultados de cada jornada quedan almacenados para poder utilizarlos posteriormente en la clasificación.

## 🔐 Autenticación y permisos

La aplicación utiliza **Laravel Fortify** para gestionar la autenticación.

Existen dos tipos de usuario:

### Usuario

Puede:

- consultar las jornadas;
- realizar pronósticos;
- modificar sus pronósticos mientras estén abiertas;
- consultar sus resultados;
- consultar la clasificación.

### Administrador

Además puede:

- crear jornadas;
- añadir partidos;
- modificar partidos mientras la jornada lo permite;
- introducir los resultados reales;
- cerrar/finalizar jornadas;
- gestionar la información necesaria para calcular los resultados.

El acceso a las funcionalidades administrativas está protegido mediante comprobaciones de permisos.

## 📅 Estados de las jornadas

Cada jornada tiene un estado que controla qué operaciones están disponibles:

```text
upcoming
    ↓
open
    ↓
closed
    ↓
finished
```

Esto permite controlar de forma explícita qué puede hacer un usuario o administrador en cada momento.

Por ejemplo, los pronósticos solamente pueden guardarse mientras la jornada está abierta y no se ha superado la fecha límite.

## 👥 Registro mediante invitación

El registro está limitado mediante un **código de invitación** almacenado como variable de entorno.

De esta forma, el proyecto puede mantenerse como una competición privada sin necesidad de permitir registros públicos.

El código de invitación no forma parte del repositorio y se configura mediante `.env`.

## 🧪 Validaciones

La aplicación incorpora diferentes validaciones para evitar estados incorrectos, entre ellas:

- todos los partidos deben tener un pronóstico antes de guardar una jornada;
- un partido no puede pertenecer a otra jornada;
- un equipo no puede enfrentarse contra sí mismo;
- una jornada no puede duplicarse para la misma temporada y semana;
- las predicciones no pueden modificarse una vez cerrado el periodo;
- las operaciones administrativas requieren permisos de administrador.

## 🎨 Interfaz

La interfaz está construida utilizando **Livewire, Flux y Tailwind CSS**.

El diseño está orientado a una experiencia sencilla y rápida para consultar las jornadas, realizar pronósticos y visualizar los resultados.

La aplicación utiliza una identidad visual inspirada en la NFL, con una combinación de tonos azul oscuro, rojo y blanco.

## 💻 Instalación local

### Requisitos

- PHP 8.4+
- Composer
- Node.js y npm
- Git
- SQLite

### Clonar el repositorio

```bash
git clone https://github.com/AlejandroDeLaBarrera/nfl-porra.git

cd nfl-porra
```

### Instalar dependencias

```bash
composer install
npm install
```

### Configurar el entorno

Copiar `.env.example` como `.env`:

```bash
cp .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

Configurar la conexión SQLite en `.env`.

Crear el archivo de base de datos si es necesario:

```bash
touch database/database.sqlite
```

Ejecutar las migraciones:

```bash
php artisan migrate
```

### Compilar los assets

```bash
npm run build
```

### Ejecutar la aplicación

```bash
php artisan serve
```

## 🔒 Variables de entorno

Las variables de entorno se mantienen fuera del repositorio.

Entre ellas se encuentra el código de invitación utilizado para controlar el registro:

```env
REGISTRATION_INVITE_CODE=
```

**No se deben introducir credenciales ni valores sensibles directamente en el código fuente.**

## 📌 Estado del proyecto

Proyecto personal desarrollado para practicar y consolidar conocimientos de desarrollo web con **Laravel, Livewire, Eloquent, autenticación, gestión de permisos y diseño de interfaces**.

La aplicación está pensada inicialmente para un grupo reducido de participantes, pero la estructura del proyecto permite ampliar posteriormente funcionalidades como:

- integración con datos reales de partidos;
- estadísticas;
- historial de temporadas;
- notificaciones;
- gestión de más participantes;
- automatización de resultados;
- API externa para obtener calendarios y resultados de la NFL.

## 👨‍💻 Autor

**Alejandro De La Barrera**

Full Stack Developer especializado en desarrollo de aplicaciones web con PHP, Laravel y tecnologías frontend modernas.

---

> Proyecto desarrollado con fines personales y de portfolio.
