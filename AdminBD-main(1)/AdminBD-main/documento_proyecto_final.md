# Corporación Unificada Nacional de Educación Superior
## Ingeniería de Sistemas

**Título del Proyecto:** Administración de Bases de Datos para la Gestión en la plataforma web ¿Qué hay pa hacer Girardot?

**Presentado por:**
- Fredy Martin Urueña Durango
- Sergio David Rodriguez Robayo
- David Santiago Forero Galindo
- Sergio Andres Parra Ibañez

**Bogotá, D.C., Septiembre de 2026**

---

## Resumen

Este documento presenta el diseño, desarrollo, implementación y optimización de una arquitectura de software y base de datos relacional de alta disponibilidad para la plataforma local interactiva *¿Qué hay pa hacer Girardot?*. La solución aborda la problemática histórica de desestructuración de la información comercial y turística en el municipio mediante la construcción de un modelo de datos estrictamente normalizado en Tercera Forma Normal (3FN), garantizando la integridad referencial y eliminando las anomalías de actualización. 

El desarrollo tecnológico consta de una arquitectura monolítica ágil basada en el ecosistema XAMPP (Apache, MySQL, PHP 8+) operando bajo un entorno de red local. La capa de base de datos se robustece con la implementación de controles transaccionales explícitos (ACID), rutinas de auditoría embebidas, y políticas de restricción de claves foráneas. A nivel aplicativo, se construyó una interfaz de usuario bajo la estética moderna de *Glassmorphism* que se comunica con el backend mediante sentencias preparadas de PHP Data Objects (PDO), mitigando vectores de ataque como la inyección SQL. El esquema incluye un sistema de seguridad avanzado basado en control de acceso por roles (RBAC) y algoritmos de encriptación de vía única para la protección de credenciales, ofreciendo así una vitrina digital escalable y segura para la reactivación turística.

---

## 1 Introducción

En el marco de la asignatura *Administración de Bases de Datos*, este proyecto consolida la ingeniería de software y de datos requerida para soportar y operar el ecosistema digital *¿Qué hay pa hacer Girardot?*. Más allá de concebirse como un simple directorio estático de sitios web, el sistema actúa como un motor dinámico de intermediación, gestión y validación entre los comerciantes locales, la administración turística y los usuarios finales o turistas. 

Para asegurar la viabilidad técnica y la sostenibilidad operacional del software, se diseñó e implementó una infraestructura relacional robusta desde sus cimientos. El proceso abarcó desde el modelado conceptual y lógico hasta la materialización física del esquema en un motor MySQL/MariaDB. La solución resultante encapsula complejas reglas de negocio directamente en el flujo de la aplicación y en el motor de base de datos, desplegando un entorno con seguridad perimetral estricta para el control de sesiones, cifrado asimétrico de accesos y una trazabilidad granular de acciones administrativas, asegurando así un rendimiento óptimo sin comprometer la integridad y fiabilidad de la información almacenada.

---

## 2 Planteamiento del problema y Argumentación

### 2.1 Descripción de la Problemática
El municipio de Girardot cuenta con una economía fuertemente impulsada por el turismo estacional. Este modelo económico genera una alta volatilidad en la oferta de servicios, establecimientos comerciales, eventos recreativos y horarios de atención. Previo a la implementación de esta plataforma, la ausencia de una arquitectura de datos centralizada, estandarizada y tecnológicamente optimizada ocasionaba los siguientes problemas críticos que impactaban negativamente el flujo económico:

* **Inconsistencia, Redundancia y Falta de Integridad:** El registro empírico y redundante de comercios provocaba que los usuarios finales (turistas) consumieran información inconsistente, obsoleta o duplicada (locales que ya cerraron, direcciones erróneas o negocios en categorías incorrectas), degradando la experiencia turística y la confianza en la oferta del municipio.
* **Fallas de Rendimiento y Escalabilidad en Temporada Alta:** Durante los periodos de alta afluencia (puentes festivos, temporada vacacional), el volumen de consultas de información se multiplica drásticamente. Sin un sistema con consultas pre-optimizadas, uso de índices estructurados y un motor relacional sólido, las alternativas previas colapsaban o sufrían de degradación severa en los tiempos de respuesta.
* **Vulnerabilidades de Seguridad y Trazabilidad:** La falta de una gobernanza de datos y de un modelo de Control de Acceso Basado en Roles (RBAC) permitía alteraciones de información sin control. La ausencia de registros de auditoría transaccional imposibilitaba rastrear el ciclo de vida de los datos, identificar aprobaciones indebidas de comercios fantasma o gestionar los permisos de los dueños de negocios de forma segura.

### 2.2 Formulación del Problema
¿De qué manera el diseño y desarrollo de una arquitectura de software relacional avanzada, que integre PHP con un motor MySQL optimizado, controles de acceso jerárquicos (RBAC), encriptación de credenciales, trazabilidad de auditoría y sentencias SQL seguras, permite gestionar de forma eficiente, íntegra y escalable la información turística y comercial en la plataforma *¿Qué hay pa hacer Girardot?* para soportar las demandas del mercado turístico local?

---

## 3 Justificación

La construcción de la solución de software y base de datos relacional para *¿Qué hay pa hacer Girardot?* se fundamenta y justifica en tres pilares estratégicos:

1. **Dimensión Técnica:** La arquitectura adoptada mediante el uso del controlador PDO de PHP y el motor relacional de MySQL garantiza el cumplimiento estricto de las propiedades transaccionales ACID (Atomicidad, Consistencia, Aislamiento y Durabilidad). El diseño en 3FN asegura la normalización del dato, mientras que el uso de sentencias preparadas elimina la vulnerabilidad a ataques de Inyección SQL. Además, el aislamiento lógico de la conexión previene caídas sistémicas mediante el manejo de excepciones de nivel de servidor.
2. **Dimensión Económica y Social:** Proporciona a la comunidad de comerciantes y emprendedores de Girardot una vitrina digital unificada, validada y altamente profesional. Al requerir la aprobación estructurada de los listados, se fomenta la formalización del sector turístico, garantizando al visitante un directorio confiable que estimula el comercio local, fomenta el turismo y cataliza la reactivación económica del municipio.
3. **Dimensión de Seguridad y Control Operacional:** Protege la información crítica del tejido empresarial local aislando las capacidades de los usuarios mediante roles inmutables a nivel de sesión. El sistema audita cada aprobación, rechazo o modificación del estado de un establecimiento comercial, vinculando criptográficamente el ID del administrador ejecutor y la estampa de tiempo exacta de la transacción, mitigando fraudes y acciones malintencionadas.

---

## 4 Objetivos

### 4.1 Objetivo General
Diseñar, estructurar e implementar una solución integral de administración de bases de datos relacional y arquitectura de software web local para la plataforma *¿Qué hay pa hacer Girardot?*, garantizando el procesamiento seguro de transacciones en tiempo real, la automatización del control de estados, la auditoría continua de los registros y una interfaz de usuario optimizada para la consulta turística concurrente.

### 4.2 Objetivos Específicos
* Diseñar y materializar un modelo relacional estrictamente normalizado en Tercera Forma Normal (3FN), creando entidades cohesivas (Usuarios, Roles, Categorías, Comercios, Turismo y Auditoría) con integridad referencial restrictiva mediante llaves primarias (`PK`) y foráneas (`FK`).
* Desarrollar la capa de lógica de negocio y back-end utilizando el motor PHP 8 y abstracción PDO, implementando transacciones explícitas (`beginTransaction`, `commit`, `rollback`) para operaciones sensibles como el cambio de estado de los comercios.
* Construir e implementar un esquema de seguridad jerárquico basado en roles (RBAC: Superadministrador, Proveedor, Turista), protegiendo la autenticación con algoritmos de derivación de claves (`password_hash`) y aislando los entornos administrativos de la vista pública.
* Implementar un módulo de trazabilidad y auditoría de datos automatizada que registre de manera inmutable el ciclo de vida y los cambios de estado (aprobaciones/rechazos) de los comercios operados por la capa administrativa.
* Desarrollar una interfaz de presentación estructurada en HTML5/CSS3 utilizando la metodología de diseño *Glassmorphism*, garantizando una navegación asíncrona intuitiva y una experiencia de usuario (UX) fluida tanto para el panel de administración como para el directorio público.

---

## 5 Alcance y Distribución del Equipo Técnico

Para responder a las altas exigencias arquitectónicas del proyecto, el trabajo se ha distribuido especializando las tareas operativas de los integrantes del equipo:

| Integrante | Rol | Entregables y Responsabilidades Técnicas |
| :--- | :--- | :--- |
| **Fredy M. Urueña** | **Arquitecto de Datos** | • Diseño del Modelo Entidad-Relación y Diccionario de Datos.<br>• Aplicación de reglas de Normalización (1FN, 2FN, 3FN).<br>• Scripting DDL para creación de Tablas (Categorías, Comercios) y restricciones (Constraints/FK). |
| **Sergio D. Rodriguez** | **Desarrollador de Lógica Backend** | • Programación de scripts PHP (PDO) y manejo de excepciones (try/catch).<br>• Desarrollo de los módulos CRUD (Listado y Registro de Comercios).<br>• Renderizado dinámico de la interfaz pública y cruce de datos con HTML. |
| **David S. Forero** | **Ingeniero de Seguridad y Acceso** | • Implementación de validación de Sesiones PHP (`session_start`, `session_destroy`).<br>• Algoritmos de encriptación de contraseñas y evasión de inyección SQL (Prepared Statements).<br>• Lógica del RBAC (Validación lógica de roles en vistas `dashboard.php`). |
| **Sergio A. Parra** | **Analista de Procesos y Auditoría** | • Desarrollo de las transacciones SQL atómicas para cambios de estado.<br>• Diseño e implementación de la tabla de Log/Auditoría y su inserción controlada.<br>• Pruebas de integración del flujo de aprobación y consistencia del estado. |

---

## 6 Arquitectura y Diseño Técnico de la Solución

### 6.1 Modelo Entidad-Relación y Normalización
El modelo de datos fue diseñado para evitar dependencias transitivas y parciales. Las entidades principales conforman el núcleo operativo del sistema:
* **Roles y Usuarios:** El control de jerarquías se maneja con la tabla `Roles`, referenciada por `Usuarios`. La eliminación en cascada está restringida (`ON DELETE RESTRICT`) para evitar la pérdida huérfana de administradores.
* **Categorías:** Actúa como entidad maestra unificadora, permitiendo clasificar dinámicamente tanto instancias de comercio como de actividades de turismo.
* **Comercios y Turismo:** Tablas especializadas que dependen estructuralmente del propietario (`id_usuario_propietario`) y de su tipología (`id_categoria`). Mantienen una cardinalidad 1 a Muchos.
* **Auditoría:** Entidad aislada de solo inserción (Append-only) que rastrea retrospectivamente la llave primaria de los comercios afectados (`registro_id`) y el responsable administrativo.

### 6.2 Lógica de Negocio y Automatización de Transacciones
A nivel de código, la manipulación de estados transicionales (de *Pendiente* a *Aprobado*) se maneja no con simples actualizaciones, sino mediante bloques transaccionales controlados.
```php
$pdo->beginTransaction();
// 1. Actualización del objeto comercial
$stmt = $pdo->prepare("UPDATE Comercios SET estado = ? WHERE id_comercio = ?");
$stmt->execute([$nuevo_estado, $id_comercio]);
// 2. Inserción automatizada del Log de Auditoría
$auditoria = $pdo->prepare("INSERT INTO Auditoria (id_usuario_admin, accion, tabla_afectada, registro_id) VALUES (?, ?, 'Comercios', ?)");
$auditoria->execute([$_SESSION['usuario_id'], $accion, $id_comercio]);
$pdo->commit();
```
Esta arquitectura garantiza que, ante una falla del servidor o desconexión, la base de datos ejecuta un `rollBack()`, manteniendo la coherencia de la información.

### 6.3 Seguridad y Control de Accesos (RBAC)
La plataforma aborda la seguridad en dos frentes:
1. **Protección de Datos en Reposo:** Las credenciales de acceso se cifran unidireccionalmente mediante el estándar `PASSWORD_BCRYPT` integrado en PHP, imposibilitando la ingeniería inversa del hash en caso de vulneración a la base de datos.
2. **Autorización y Aislamiento de Capas:** El panel administrativo (`dashboard.php`, `comercios.php`) se aísla de la capa pública (`index.php`) validando la persistencia de las variables globales de sesión (`$_SESSION['usuario_id']` y `$_SESSION['usuario_rol']`). Las operaciones de modificación bloquean explícitamente cualquier acceso donde el rol no corresponda a la llave jerárquica del Superadministrador (Valor `1`).

### 6.4 Optimización, Interfaz y Métricas
La arquitectura cliente-servidor fue optimizada reduciendo las sobrecargas mediante sentencias `JOIN` altamente eficientes para poblar la cuadrícula de descubrimiento en el directorio público. Esto consolida en una sola petición a MySQL los datos descriptivos del comercio y su categoría asignada, reduciendo la latencia computacional.
A nivel de interfaz gráfica (GUI), se emplearon variables CSS personalizadas, selectores semánticos y el paradigma visual *Glassmorphism* (superficies translúcidas con desenfoque de fondo), renderizando un DOM ligero, sin dependencias de frameworks externos como Bootstrap, que garantiza un despliegue y pintado casi instantáneo en la memoria del navegador.

---

## 7 Conclusiones

La ejecución de la ingeniería de software y la administración estructurada de bases de datos para el ecosistema **¿Qué hay pa hacer Girardot?** demostró ser un rotundo éxito técnico. La implementación estricta del modelo 3FN, sumada a la barrera de seguridad generada por sentencias PDO preparadas, neutralizó eficazmente las vulnerabilidades estructurales comunes en aplicaciones web iniciales.
El uso de transacciones SQL combinadas con la bitácora de auditoría inmutable proporcionó un nivel corporativo de fiabilidad, permitiendo a la administración municipal gestionar la volatilidad comercial de manera segura. Asimismo, la separación arquitectónica entre el motor de base de datos relacional MySQL, la lógica de negocio en PHP y una capa de presentación limpia e interactiva en HTML5/CSS3 demostró que es posible construir soluciones de alta rentabilidad social y excelente rendimiento de respuesta sin depender de arquitecturas en la nube costosas, ofreciendo una operatividad local (intranet/desktop) impecable y duradera.
