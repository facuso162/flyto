# 🚀 Flyto - Plataforma de Reservas de Vuelos

## 1. INTRODUCCIÓN Y OBJETIVO DEL PROYECTO
**Flyto** es una plataforma web integral diseñada para la búsqueda y reserva de vuelos comerciales. Nuestro objetivo principal es brindar una experiencia de usuario ágil, segura y altamente accesible, permitiendo a los viajeros buscar vuelos, consultar requisitos, iniciar sesión y gestionar sus reservas bajo la modalidad de «reservar ahora, pagar después». Todo esto se desarrolla con un enfoque estricto en la semántica web, accesibilidad y una arquitectura robusta en el backend.

---

## 2. 🛠️ STACK TECNOLÓGICO Y ENTORNO

El proyecto se encuentra desarrollado con tecnologías nativas y ligeras, priorizando el rendimiento, la accesibilidad y el control absoluto sobre el código:

- **Backend:** PHP Vanilla (versión 8.0 o superior).
- **Frontend:** HTML5 Semántico, Tailwind CSS (para el estilizado utilitario) y JavaScript Vanilla.
- **Base de Datos:** MySQL utilizando PDO (PHP Data Objects) para acceso seguro y prevención de inyección SQL.
- **Entorno de Desarrollo:** XAMPP (Apache, MySQL escuchando en el puerto configurado) y control de versiones distribuido con Git.

---

## 3. 🏗️ PATRONES DE ARQUITECTURA CORE

El backend del proyecto sigue una arquitectura fuertemente orientada a objetos (POO) inspirada en MVC y DDD (Domain-Driven Design).

### Patrón Front Controller
Toda petición HTTP que recibe la aplicación pasa única y obligatoriamente por `public/index.php`. Este script funciona como el "Front Controller", interceptando las peticiones, configurando el entorno global (carga del archivo `.env`, manejo de errores) y delegando la ejecución al Router.

### Sistema de Enrutamiento (Router)
Poseemos un enrutador personalizado (`Router`) que mapea la URI solicitada y el método HTTP (GET/POST) hacia un controlador específico. Si una ruta requiere redirección (por ejemplo, tras un login exitoso o un fallo de validación), se retorna un objeto `RedirectResponse`, asegurando un manejo coherente y testeable de los encabezados de redirección.

### Inyección de Dependencias y Modularidad
La aplicación utiliza un contenedor de inyección de dependencias (`Container`) ligero para instanciar repositorios y controladores, lo que facilita el Testing y el desacoplamiento. El código está organizado modularmente por dominio dentro de la carpeta `app/`:
- `auth/`: Autenticación, registro y gestión de contraseñas.
- `vuelos/`: Buscador, resultados y reservas.
- `faq/` y `novedades/`: Gestión de contenidos y ayuda al pasajero.
- `shared/`: Componentes comunes como la conexión a Base de Datos (Database) y layouts globales.

---

## 4. 📂 ESTRUCTURA DE DIRECTORIOS

El proyecto separa rigurosamente el código de acceso público del código de la aplicación por motivos de seguridad arquitectónica.

```text
flyto/
├── .env                     # Variables de entorno (DB_HOST, DB_PORT, etc.)
├── README.md                # Documentación maestra del proyecto
├── app/                     # Lógica core, no accesible directamente por el navegador
│   ├── auth/                # Dominio de autenticación
│   │   ├── controllers/
│   │   └── views/
│   ├── faq/                 # Dominio de preguntas frecuentes
│   ├── novedades/           # Dominio de noticias
│   ├── vuelos/              # Dominio del negocio principal
│   └── shared/              # Recursos compartidos
│       ├── database/        # Configuración PDO
│       └── views/           # Layouts (ej. public.layout.php)
└── public/                  # Document Root del servidor (Apache)
    ├── assets/
    │   ├── css/
    │   └── js/              # app.js, validaciones.js
    └── index.php            # Front Controller (único punto de entrada)
```

---

## 5. ♿ ESTÁNDARES DE FRONTEND Y ACCESIBILIDAD (MUY IMPORTANTE)

Como regla inquebrantable, **Flyto cumple estrictamente con las Pautas de Accesibilidad al Contenido en la Web (WCAG 2.1 nivel AA)**, validadas a través de herramientas profesionales como TAW.

### HTML Semántico
- Los formularios nunca utilizan el patrón de "etiquetas envolventes" para los inputs. Todo `<label>` debe cerrarse inmediatamente e incluir un atributo `for="..."` apuntando a un ID único (`id="..."`) del control asociado.
- La jerarquía de encabezados (`<h1>`, `<h2>`, `<h3>`) nunca debe tener saltos, garantizando una navegación secuencial predecible para lectores de pantalla.

### JavaScript Seguro y Accesible
- Nuestro código cliente (`validaciones.js`) es 100% Vanilla.
- Las funciones son Null-Safe: siempre se comprueba la existencia de los nodos del DOM (`if (elemento)`) antes de asignar *Event Listeners*, evitando que la aplicación colapse en rutas donde los formularios no existan.
- Compatibilidad de autocompletado: Se escuchan simultáneamente los eventos `input` y `change` para reaccionar al relleno automático de los gestores de contraseñas de los navegadores.
- Los elementos interactivos personalizados (como el botón del "ojo" para mostrar contraseña) manipulan dinámicamente sus atributos de estado `aria-pressed` (true/false) y `aria-label` para comunicar los cambios a las tecnologías asistivas en tiempo real.

---

## 6. ⚙️ INSTRUCCIONES DE DESPLIEGUE LOCAL

Para desplegar y ejecutar Flyto en un entorno de desarrollo local, sigue estos pasos:

1. **Clonar el Repositorio:**
   ```bash
   git clone <url-del-repositorio> flyto
   cd flyto
   ```

2. **Configurar el Entorno:**
   Copia o renombra el archivo de entorno de ejemplo a `.env` en la raíz del proyecto. Edítalo para configurar los parámetros de conexión a tu Base de Datos:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3308      # O el puerto que utilice tu MySQL local
   DB_NAME=flyto_db
   DB_USER=root
   DB_PASS=
   ```

3. **Configurar Servidor Local (XAMPP):**
   - Asegúrate de que el *Document Root* de tu servidor Apache en XAMPP apunte a la carpeta `flyto/public`.
   - Inicia los módulos de Apache y MySQL desde el Panel de Control de XAMPP.
   - Crea la base de datos `flyto_db` e importa los esquemas/datos iniciales si corresponde.

4. **Acceder a la Plataforma:**
   Abre tu navegador y dirígete a `http://localhost/` o al virtual host que hayas configurado. El Front Controller (`index.php`) interceptará la petición y cargará la página principal.
