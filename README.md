<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Descripción del Repositorio

Este repositorio implementa una API REST que consume la API abierta de la NASA, específicamente el proyecto DONKI (Data on National Key Indicators). El objetivo principal es extraer y procesar información sobre los instrumentos utilizados en las mediciones realizadas por DONKI, así como las actividades asociadas a esos instrumentos.

### Cambios Realizados

1. **Configuración de la API de NASA**:
   - Se creó una API Key para autenticar las solicitudes a la API de NASA, permitiendo el acceso a los datos necesarios.

2. **Consumo de la API DONKI**:
   - Se identificaron y documentaron todas las rutas de la API de DONKI que devuelven información sobre los instrumentos utilizados en las mediciones y los identificadores de cada actividad.

3. **Implementación de Rutas API REST**:
   - **Ruta para Obtener Todos los Instrumentos**:
     - `GET /api/instruments`
     - Devuelve todos los instrumentos utilizados en las mediciones de DONKI.
     - **Ejemplo de respuesta**: 
       ```json
       {
           "instruments": [
               "MODEL: SWMF",
               "INSTRUMENTO_X",
               "INSTRUMENTO_Y"
           ]
       }
       ```

   - **Ruta para Obtener Todas las IDs de Actividades**:
     - `GET /api/activity-ids`
     - Devuelve todas las IDs de actividades sin incluir información sobre las fechas.
     - **Ejemplo de respuesta**:
       ```json
       {
           "activity_ids": [
               "IPS-001",
               "HSS-001",
               "GST-001"
           ]
       }
       ```

   - **Ruta para Calcular el Porcentaje de Uso de Instrumentos**:
     - `GET /api/instrument-usage`
     - Calcula el porcentaje de uso de cada instrumento respecto al total de apariciones.
     - **Ejemplo de respuesta**:
       ```json
       {
           "usage_percentage": {
               "MODEL: SWMF": 0.3,
               "INSTRUMENTO_X": 0.5,
               "INSTRUMENTO_Y": 0.2
           }
       }
       ```

   - **Ruta para Obtener el Porcentaje de Uso de un Instrumento Específico**:
     - `POST /api/instrument-usage`
     - Permite enviar el nombre de un instrumento en el cuerpo de la solicitud y recibir el porcentaje de uso de ese instrumento en las actividades.
     - **Ejemplo de cuerpo de solicitud**:
       ```json
       {
           "instrument": "MODEL: SWMF"
       }
       ```
     - **Ejemplo de respuesta**:
       ```json
       {
           "instrument": "MODEL: SWMF",
           "usage_percentage": 0.3
       }
       ```

### Configuración de Variables de Entorno

Para acceder a la API de NASA, necesitarás configurar las siguientes variables de entorno. Puedes hacerlo creando un archivo `.env` en la raíz de tu proyecto y agregando las siguientes líneas:

```plaintext
NASA_API_USER="tu_correo@ejemplo.com"  # Reemplaza con tu dirección de correo electrónico
NASA_API_ID="tu_id"                      # Reemplaza con tu ID de usuario
NASA_API_KEY="tu_clave"                  # Reemplaza con tu clave de API
```

Asegúrate de reemplazar los valores de ejemplo con tus propios datos. Puedes obtener tu `NASA_API_KEY` registrándote en el [sitio web de la API de NASA](https://api.nasa.gov/).

### Propósito General

El propósito de este repositorio es proporcionar una interfaz sencilla y accesible para interactuar con los datos de la API de DONKI. Al implementar las rutas mencionadas, se facilita la obtención de información relevante sobre los instrumentos y actividades, permitiendo a los usuarios analizar el uso de los instrumentos en las mediciones realizadas por DONKI.

### Conclusión

Este repositorio no solo permite acceder a datos de la API de NASA, sino que también organiza y presenta esa información de manera que sea fácil de consumir a través de una API REST. Esto es especialmente útil para investigadores, desarrolladores y cualquier persona interesada en los datos de la NASA relacionados con las actividades solares y espaciales.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
