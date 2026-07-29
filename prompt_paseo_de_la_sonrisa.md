# Prompt para configurar el procesamiento de Transferencias Externas en Paseo de la Sonrisa

Copia y pega el siguiente prompt en el chat de IA del otro sistema (**Paseo de la Sonrisa**) para que configure la visualización e integración del módulo:

---

```markdown
Necesito implementar la vista y la lógica de administración para procesar (Aprobar/Rechazar) las "Transferencias Externas" registradas por las sucursales (ej. Goldent).

### 1. Conexión a la Base de Datos:
La base de datos ya existe y está configurada. La tabla que almacena esta información es `transferencias_externas` y se encuentra en la base de datos `paseodelasonrisa` (o la base de datos central configurada localmente).
*Nota: No debes crear la base de datos ni ejecutar scripts de creación de tablas. Utiliza la conexión existente en el proyecto.*

### 2. Estructura de la Tabla `transferencias_externas` en la Base de Datos:
La tabla cuenta con los siguientes campos que usaremos en este módulo:
- `id` (Clave primaria autoincremental)
- `empresa_origen` (Ej: 'Goldent')
- `quien_transfiere` (Nombre de usuario que envió el reporte)
- `monto` (Valor numérico sin formatear)
- `concepto` (Detalle o descripción de la transferencia)
- `fecha_envio` (Fecha en formato YYYY-MM-DD)
- `hora_envio` (Hora en formato HH:MM:SS)
- `comprobante_url` (Texto/URL de comprobante manual, o formato combinado `texto_comprobante|url_archivo_subido` si subieron una foto/archivo)
- `estado` ('PENDIENTE', 'APROBADA', 'ANULADA')
- `motivo_resolucion` (Texto que explica por qué se aprobó o anuló)
- `fecha_procesado` (Fecha y hora de resolución)
- `procesado_por` (Nombre del administrador central que resolvió la transferencia)

### 3. Requerimientos de la Interfaz (Listado de Transferencias):
Crea una tabla en HTML con las siguientes columnas y comportamiento visual:
- **ID**: ID de la transferencia.
- **Enviado por**: Mostrar el nombre de usuario (`quien_transfiere`) en negrita, y debajo en texto pequeño la fecha y hora (`fecha_envio` y `hora_envio`).
- **Estado**: Badge de color según el estado:
  * `PENDIENTE`: Amarillo (`label-warning`)
  * `APROBADA`: Verde (`label-success`)
  * `ANULADA`: Rojo (`label-danger`)
- **Concepto**: Texto del concepto (`concepto`).
- **Comprobante**: 
  - Si el campo `comprobante_url` tiene una barra vertical `|`, divídelo en dos partes.
  - Arriba: Mostrar el texto del comprobante manual.
  - Abajo: Si tiene una imagen/PDF, mostrar un botón con clase `btn-default` (color por defecto) para `"Ver Archivo/Imagen"` que se abra en una ventana flotante (usando la librería Fancybox configurada con `data-fancybox="gallery"`).
- **Monto**: Mostrar el monto formateado con separadores de miles y el sufijo "Gs.". (Ej: `1.800.000 Gs.`).
- **Motivo (Si aplica)**: Texto de `motivo_resolucion`.
- **Procesado**: 
  - Si ya fue procesada, mostrar el nombre del administrador (`procesado_por`) en negrita, y abajo la fecha y hora (`fecha_procesado`).
  - Si está pendiente, mostrar un guion `-`.
- **Acciones**:
  - Si el estado de la transferencia es `PENDIENTE`, mostrar botones/acciones para **Aprobar** o **Rechazar**.
  - Si el estado es distinto, mostrar un guion `-`.

### 4. Lógica de Aprobación y Rechazo (Resolución):
- Al hacer clic en **Aprobar** o **Rechazar** (Anular), se debe abrir una ventana modal que solicite un **Motivo de resolución** (campo de texto).
- Al confirmar el formulario, se debe ejecutar un `UPDATE` en la tabla `transferencias_externas` para el registro seleccionado:
  * Si aprueba: `estado` cambia a `'APROBADA'`.
  * Si rechaza: `estado` cambia a `'ANULADA'`.
  * Guardar el texto en `motivo_resolucion`.
  * Establecer `fecha_procesado` con la fecha/hora actual de la base de datos (`NOW()`).
  * Establecer `procesado_por` con el nombre de usuario de la sesión activa de Paseo de la Sonrisa.
- Tras realizar la operación, actualizar el listado y mostrar un SweetAlert indicando el resultado.
```
