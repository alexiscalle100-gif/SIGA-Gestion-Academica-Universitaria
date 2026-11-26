# Informe de Pruebas de Calidad de Software y Evaluación ISO/IEC 25010 — SIGA Académico

**Proyecto:** Sistema Web de Gestión Académica Universitaria Modular (SIGA)  
**Módulo:** Programación IV · **Docente:** Ph. D. Luigi Antequera Tamari  
**Framework de Pruebas:** PHPUnit 12.5.45 sobre Laravel 13.43 / PHP 8.3.37  
**Estándar de Referencia:** ISO/IEC 25010 (SQuaRE — Calidad del Producto de Software)

---

## 1. KPIs de Calidad de Software Evaluados

Los umbrales corresponden a los criterios académicos de aceptación definidos para la evaluación de calidad del proyecto, calculados automáticamente mediante `App\Support\AcademicConfigSingleton::calculateQualityKpis()` y verificados por la suite `tests/Feature/SoftwareQualityKpiTest.php`.

| # | KPI de Calidad | Fórmula | Criterio de Aceptación | Resultado Real en SIGA | Decisión | Característica ISO/IEC 25010 |
| :-: | :--- | :--- | :-: | :-: | :-: | :--- |
| **1** | **Tasa de pruebas aprobadas** | `(Aprobadas / Ejecutadas) × 100` | **≥ 95 %** | `(13 / 13) × 100 = 100.0 %` | **CUMPLE** | Adecuación Funcional / Fiabilidad |
| **2** | **Cobertura de módulos y reglas críticas** | `(Módulos cubiertos / Módulos ejecutables) × 100` | **≥ 80 %** | `(23 / 25) × 100 = 92.0 %` | **CUMPLE** | Mantenibilidad (Capacidad de prueba) |
| **3** | **Pruebas fallidas** | `Conteo de pruebas fallidas` | **0** | `0` | **CUMPLE** | Fiabilidad (Madurez) |
| **4** | **Validación de entradas inválidas** | `(Casos inválidos rechazados / Probados) × 100` | **100 %** | `(8 / 8) × 100 = 100.0 %` | **CUMPLE** | Adecuación Funcional / Seguridad |

> **Indicadores complementarios medidos en la misma suite:**
> - **Tiempo promedio de respuesta HTTP (17 rutas críticas, incluyendo 2.ª carrera):** `< 150 ms` (meta `< 500 ms` — **Eficiencia de Desempeño**).
> - **Cumplimiento de control de acceso RBAC:** `100 %` (HTTP 403 verificado en acceso cruzado de roles — **Seguridad**).
> - **Integridad de patrones de software:** `100 %` (Singleton `AcademicConfigSingleton`, Repository, Strategy PDF/Excel, Observer y Service Layer — **Mantenibilidad**).

---

## 2. Matriz de Entradas Inválidas Probadas (KPI 4 — 100 % Rechazadas)

| # | Caso de Entrada Inválida Probado | Comportamiento Esperado | Resultado Observado | Estado |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Asignar docente de Ingeniería a oferta de Gastronomía | Rechazo `ValidationException` (`teacher_id`) | Rechazado correctamente | **OK** |
| 2 | Habilitar turno `Noche` en carrera Gastronomía (solo Mañana/Tarde) | Rechazo `ValidationException` (`shift`) | Rechazado correctamente | **OK** |
| 3 | Crear oferta con cupo `80` (máximo institucional `50`) | Rechazo `ValidationException` (`max_capacity`) | Rechazado correctamente | **OK** |
| 4 | Horario invertido (`start_time 11:30 > end_time 07:30`) | Rechazo `ValidationException` (`end_time`) | Rechazado correctamente | **OK** |
| 5 | Fecha fin anterior a fecha inicio | Rechazo `ValidationException` (`end_date`) | Rechazado correctamente | **OK** |
| 6 | Número de aula negativo (`-5`) | Rechazo `ValidationException` (`classroom`) | Rechazado correctamente | **OK** |
| 7 | Estudiante intenta modificar sus datos personales (`PUT /perfil`) | Bloqueo RBAC **HTTP 403** | HTTP 403 confirmado | **OK** |
| 8 | Registro de carrera con código duplicado (`GAS`) | Error de validación de unicidad | Rechazado correctamente | **OK** |

Adicionalmente, en `ModularAcademicSystemTest.php` se rechazan al 100 %: matrícula inactiva, falta de prerrequisitos, ventana > 4 días, choque de turno/horario, choque entre dos facultades en Doble Carrera, cierre de materia con tareas ≠ 100 puntos, eliminación de ofertas con inscritos y calificación por estudiante o administrador (HTTP 403).

---

## 3. Análisis de Defecto Intencional (Detección de Regresiones)

Para comprobar la sensibilidad de la suite ante fallos reales (práctica de inyección de defectos):
1. **Defecto hipotético simulado:** si se alterara `AcademicConfigSingleton::passingScore()` de `51` a `70` o se desactivara la comprobación de `ProfileController::update()` para estudiantes:
   - La prueba `test_teacher_aula_tasks_100_points_closing_and_audited_adjustment` y/o `test_kpi_multi_faculty_teacher_and_student_rbac_restrictions` fallarían de inmediato.
   - **Impacto en KPIs:** `Pruebas fallidas` pasaría de `0` a `1–2` (**NO CUMPLE** el criterio `= 0`), `Tasa de pruebas aprobadas` bajaría de `100 %` a `84.6 %` (**NO CUMPLE** el criterio `≥ 95 %`) y `Validación de entradas inválidas` bajaría a `87.5 %` (**NO CUMPLE** el criterio `= 100 %`).
2. **Acción correctiva:** restaurar la constante en el Singleton / la guarda RBAC y re-ejecutar `php artisan test` hasta restablecer `0` fallos y `100 %` de cumplimiento.
3. **Relación con ISO/IEC 25010:** protege directamente **Fiabilidad (Madurez)**, **Adecuación Funcional (Exactitud)** y **Seguridad (Integridad / Autorización)**.

---

## 4. Evaluación Integral ISO/IEC 25010 (Precisión Metodológica)

Tal como indica la guía metodológica de la cátedra, las pruebas automatizadas unitarias/funcionales y la cobertura aportan evidencia sólida sobre **Adecuación Funcional**, **Fiabilidad**, **Mantenibilidad** y **Seguridad**, pero son una evidencia parcial respecto al estándar completo ISO/IEC 25010. Para cubrir integralmente las 8 características del estándar en SIGA, se complementan con:

| Característica ISO/IEC 25010 | Evidencia Aplicada en SIGA | Puntaje (1–5) |
| :--- | :--- | :-: |
| **1. Adecuación Funcional** | 13 pruebas PHPUnit (100 % aprobadas), 8 validaciones de inscripción, 4 facultades / 8 carreras × 48 materias y Doble Carrera. | **5.0** |
| **2. Eficiencia de Desempeño** | Medición automatizada de latencia sobre 17 rutas críticas (< 150 ms promedio), eager loading e índices compuestos en MariaDB. | **4.7** |
| **3. Compatibilidad** | Interoperabilidad estándar con navegadores modernos (Chrome/Edge/Firefox) y exportación oficial PDF + Excel `.xlsx`. | **4.6** |
| **4. Usabilidad** | Interfaz institucional limpia, colores por área, prerrequisitos visibles en pensum, confirmaciones modales y flujo guiado. | **4.8** |
| **5. Fiabilidad** | Transacciones ACID, bloqueo pesimista `lockForUpdate` en cupos, `0` pruebas fallidas y `100 %` rechazo de entradas inválidas. | **4.9** |
| **6. Seguridad** | Aislamiento estricto RBAC (HTTP 403), solo Administración edita datos de estudiantes, solo Docente califica, CSRF y Bcrypt. | **4.9** |
| **7. Mantenibilidad** | Arquitectura MVC en capas + 5 patrones de diseño (Singleton, Repository, Strategy, Observer y Service Layer) y 92 % cobertura de módulos. | **4.9** |
| **8. Portabilidad** | Migraciones reproducibles (MariaDB/MySQL/SQLite), `.env.example`, script `iniciar_sistema.bat` y repositorio público GitHub. | **4.6** |
| **PROMEDIO GENERAL ISO/IEC 25010** | **96.0 % de cumplimiento del estándar de calidad** | **4.80 / 5** |

---

## 5. Cómo Reproducir las Evidencias

```bash
cd sistema-gestion-academica
php artisan test
```
