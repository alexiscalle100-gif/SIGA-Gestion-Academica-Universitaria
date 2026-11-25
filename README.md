# SIGA Académico — Sistema Web de Gestión Académica Universitaria Modular

Plataforma web de **Gestión Académica Universitaria** desarrollada en **Laravel 13 + PHP 8.3 + Tailwind CSS 4 + Alpine.js + MariaDB (XAMPP)** bajo la **Metodología Ágil XP (Extreme Programming)**, diseñada para carreras de grado de 4 años en formato modular (**1 materia por mes**, 8 niveles de 6 meses y **48 materias por carrera**).

> **Proyecto Formativo — Programación IV**  
> Universidad Privada Domingo Savio (UPDS) · Docente: Ph. D. Luigi Antequera Tamari

---

## Características Principales

### 1. Estructura Académica Modular (4 Facultades · 8 Carreras · 384 Ítems de Pensum)
- **Jerarquía**: `Facultad → Carreras`, `Facultad → Docentes`, `Facultad → Materias`.
- **4 Facultades y 8 Carreras completas** (48 materias por carrera, 8 niveles × 6 meses, prerrequisitos por materia y por nivel completo aprobado, y 4 cupos de electivas):
  - **Facultad de Ingeniería (`ING`)**: Ingeniería de Sistemas (`SIS`), Ingeniería de Telecomunicaciones (`TEL`), Ingeniería Industrial (`IND`).
  - **Facultad de Ciencias Empresariales (`EMP`)**: Administración de Empresas (`ADM`), Contaduría Pública (`CPA`).
  - **Facultad de Ciencias Jurídicas y Sociales (`JUR`)**: Derecho (`DER`), Psicología (`PSI`).
  - **Facultad de Gastronomía (`GAS`)**: Gastronomía (`GAS`).
- **Materias comunes en niveles 1 a 3**: Las carreras de una misma facultad comparten materias del tronco común, por lo que compañeros de distintas carreras cursan juntos y el docente visualiza la **carrera y facultad de cada alumno inscrito**, junto con un resumen de **Estudiantes por Carrera**.
- **Materias transversales por facultad**: Duplicadas con código propio según la facultad dueña (`MAT-101` en Ingeniería, `MAT-E101` en Empresariales, `MAT-G101` en Gastronomía).

### 2. Semestres Automáticos, Matrículas y Doble Carrera Multi-Facultad
- **Semestres fijos automáticos**: 2 por año (`Enero–Junio` y `Julio–Diciembre`), generados automáticamente por `SemesterService`.
- **Matrícula semestral manual**: Habilitada por el Administrador con cupo máximo de **6 materias por carrera/semestre** (no acumulable).
- **Doble Carrera (incluso en distintas facultades)**:
  - El Administrador puede asignar o retirar una segunda carrera al estudiante y gestionar matrículas independientes por carrera.
  - El Estudiante puede alternar entre sus carreras desde el **Dashboard**, **Inscripción y Cursos** y **Avance e Historial**.
  - **Control automático de choques entre carreras y facultades**: bloquea tanto **choques de horario** (mismo turno o franja solapada en el mismo mes) como **choques de materia equivalente** en otra facultad, permitiendo cursar ambas carreras en turnos distintos.

### 3. Ofertas Académicas con Horarios por Turno
- **Horarios explícitos por turno**:
  - **Mañana**: `07:30 - 11:30 · Lunes a Viernes`
  - **Tarde**: `14:00 - 18:00 · Lunes a Viernes`
  - **Noche**: `18:45 - 22:15 · Lunes a Viernes`
- **Dos pestañas administrativas**:
  1. **Habilitación de materias**: botones de facultades, catálogo filtrado por colores de área, configuración de oferta con confirmación previa y listado de docentes mostrando choques en tiempo real.
  2. **Ofertas habilitadas**: edición controlada (solo docente, aula y cupo ≤ 50; fechas, turno y horario bloqueados), deshabilitación (anula inscripciones y libera el cupo de 6) y eliminación permitida únicamente sin inscritos.

### 4. Inscripción por Intentos, Actividades Presentadas y Calificación Exclusiva Docente
- **8 validaciones automáticas** al inscribirse (matrícula activa, pertenencia al pensum, prerrequisitos aprobados, no aprobada/en curso, ventana de hasta 4 días desde el inicio, sin choque horario/materia en ninguna carrera, cupo < 6 y plazas disponibles) con confirmación obligatoria y sin baja voluntaria.
- **Presentación de actividades**: El estudiante puede marcar sus actividades como **Presentada** (con comentario opcional de entrega), notificando al docente, pero **solo el docente dueño de la materia puede anotar o modificar calificaciones** (acceso bloqueado con HTTP 403 para estudiantes y administrador).
- **Cierre de materia y auditoría**: Las tareas deben sumar exactamente **100 puntos**; se diferencia estrictamente **Sin calificar (`NULL`)** de **`0`**; nota mínima de aprobación **51/100** (enteros); tras el cierre solo se admiten correcciones auditadas (`grade_adjustments`).

### 5. Reportes Oficiales y Arquitectura
- Exportación de reportes académicos en **PDF (DomPDF)** y **Excel `.xlsx` (PhpSpreadsheet)**.
- **Arquitectura MVC en capas + 4 Patrones de Diseño**:
  - **Repository Pattern** (`OfferingRepositoryInterface` / `OfferingRepository`)
  - **Strategy Pattern** (`ReportExportContext` + `PdfReportStrategy` + `ExcelReportStrategy`)
  - **Observer Pattern** (`EnrollmentObserver` → notificaciones y bitácora de auditoría)
  - **Service Layer** (`SemesterService`, `CurriculumProgressService`, `OfferingService`, `EnrollmentService`, `TaskGradingService`)

---

## Stack Tecnológico

| Tecnología | Versión | Uso |
| :--- | :--- | :--- |
| **Laravel** | 13.43.0 | Backend MVC, RBAC, Eloquent ORM, Migraciones |
| **PHP** | 8.3.37 (TS VS16 x64) | Motor de ejecución backend |
| **Blade + Tailwind CSS** | Tailwind v4.0 | Vistas server-side responsivas |
| **Alpine.js** | 3.x | Modales de confirmación, pestañas y filtros |
| **Vite / Node.js** | Vite 8.3.8 / Node 24.15 | Compilación de assets frontend |
| **MariaDB (XAMPP)** | 10.4.32 | Base de datos relacional (`gestion_academica`) |
| **Composer** | 2.11.2 | Gestión de paquetes PHP |
| **DomPDF & PhpSpreadsheet** | 3.1.2 / 5.11.0 | Reportes PDF y Excel `.xlsx` |
| **PHPUnit** | 12.5.45 | Pruebas automatizadas (8 pruebas / 44 aserciones) |

---

## Estructura del Repositorio

```text
PROYECTO_FINAL/
├── sistema-gestion-academica/                 # Aplicación web Laravel 13
│   ├── app/
│   │   ├── Http/Controllers/                  # Controladores por rol (Admin, Teacher, Student)
│   │   ├── Models/                            # Modelos Eloquent (15 tablas normalizadas)
│   │   ├── Observers/                         # EnrollmentObserver (Patrón Observer)
│   │   ├── Repositories/                      # OfferingRepository (Patrón Repository)
│   │   └── Services/                          # Servicios de negocio + Reportes (Patrón Strategy)
│   ├── database/
│   │   ├── migrations/                        # Esquema relacional normalizado 3FN
│   │   └── seeders/                           # 4 facultades, 8 carreras × 48 materias y datos demo
│   ├── resources/views/                       # Vistas Blade por rol
│   └── tests/Feature/                         # Suite PHPUnit (ModularAcademicSystemTest)
├── Proyecto_Formativo_Gestion_Academica.docx  # Documentación oficial completa (Formato UPDS)
├── iniciar_sistema.bat                        # Lanzador rápido para Windows + XAMPP
└── README.md
```

---

## Instalación y Ejecución Local

### Requisitos previos
- **PHP >= 8.3** con extensiones `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `gd`, `zip`, `fileinfo`, `intl`
- **Composer >= 2.x**
- **Node.js >= 20** y **NPM**
- **XAMPP (MariaDB / MySQL)**

### Pasos de instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/alexiscalle100-gif/SIGA-Gestion-Academica-Universitaria.git
cd SIGA-Gestion-Academica-Universitaria/sistema-gestion-academica

# 2. Instalar dependencias backend y frontend
composer install
npm install

# 3. Configurar archivo de entorno
cp .env.example .env
php artisan key:generate
```

Edita `.env` con las credenciales de tu base de datos MariaDB/MySQL (en XAMPP normalmente puerto `3306` o `3307`):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=gestion_academica
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Crear la base de datos `gestion_academica`, migrar y poblar datos iniciales
php artisan migrate:fresh --seed

# 5. Compilar assets frontend
npm run build

# 6. Ejecutar servidor local
php artisan serve
```

Abre en tu navegador: **http://127.0.0.1:8000**

> En Windows con XAMPP instalado en `D:\xampp`, también puedes hacer doble clic en `iniciar_sistema.bat`.

---

## Credenciales de Prueba

| Rol | Correo Electrónico | Contraseña | Escenario Demostrativo |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@upds.edu.bo` | `Admin123*` | 4 facultades, 8 carreras (48 mat. c/u), ofertas con horarios, matrículas y Doble Carrera |
| **Docente (Ingeniería)** | `docente@upds.edu.bo` | `Docente123*` | `SIS-102` (actividad marcada como *Presentada* lista para calificar) y `MAT-102` (alumnos de TEL e IND) |
| **Estudiante (Doble Carrera)** | `estudiante@upds.edu.bo` | `Estudiante123*` | Cursa `SIS` (ING) + `ADM` (EMP): prueba choque horario Mañana (`ADM-E101` vs `SIS-102`) y Noche libre (`ECO-E101`) |
| **Estudiante (Sin Matrícula)** | `mateo.gutierrez@upds.edu.bo` | `Estudiante123*` | Muestra el aviso de Caja/Contabilidad manteniendo visible su historial |
| **Estudiante (Gastronomía)** | `sofia.zambrana@upds.edu.bo` | `Estudiante123*` | Pensum de 48 materias de Gastronomía con turnos Mañana y Tarde |

---

## Pruebas Automatizadas (PHPUnit)

```bash
cd sistema-gestion-academica
php artisan test
```

Resultado esperado: **8 pruebas aprobadas, 44 aserciones verificadas** (validando facultades/carreras, reglas de ofertas y aulas, 8 validaciones de inscripción, mensajes priorizados, aislamiento RBAC, cierre de materia con 100 pts, presentación de actividades con nota exclusiva docente y Doble Carrera multi-facultad).
