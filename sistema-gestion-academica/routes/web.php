<?php

use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\TuitionController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\Teacher\TeacherOfferingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notificaciones/{notification}/leer', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notificaciones/leer-todas', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reportes/{type}/{format}', [ReportController::class, 'download'])->name('reports.download');

    // ======================= ADMINISTRADOR =======================
    Route::middleware('role:administrador')->prefix('admin')->name('admin.')->group(function () {
        // Facultades
        Route::get('/facultades', [FacultyController::class, 'index'])->name('faculties.index');
        Route::post('/facultades', [FacultyController::class, 'store'])->name('faculties.store');
        Route::put('/facultades/{faculty}', [FacultyController::class, 'update'])->name('faculties.update');
        Route::delete('/facultades/{faculty}', [FacultyController::class, 'destroy'])->name('faculties.destroy');

        // Carreras y gestión de pensum (aprobar/habilitar, deshabilitar, editar, agregar/quitar materias)
        Route::get('/carreras', [CareerController::class, 'index'])->name('careers.index');
        Route::post('/carreras', [CareerController::class, 'store'])->name('careers.store');
        Route::get('/carreras/{career}', [CareerController::class, 'show'])->name('careers.show');
        Route::put('/carreras/{career}', [CareerController::class, 'update'])->name('careers.update');
        Route::patch('/carreras/{career}/estado', [CareerController::class, 'toggleStatus'])->name('careers.toggle');
        Route::delete('/carreras/{career}', [CareerController::class, 'destroy'])->name('careers.destroy');
        Route::post('/carreras/{career}/materias', [CareerController::class, 'addSubject'])->name('careers.subjects.store');
        Route::delete('/carreras/{career}/materias/{curriculumItem}', [CareerController::class, 'removeSubject'])->name('careers.subjects.destroy');

        // Ofertas (docente y aula opcionales al habilitar; editables después)
        Route::get('/ofertas', [OfferingController::class, 'index'])->name('offerings.index');
        Route::post('/ofertas', [OfferingController::class, 'store'])->name('offerings.store');
        Route::get('/ofertas/{offering}/editar', [OfferingController::class, 'edit'])->name('offerings.edit');
        Route::put('/ofertas/{offering}', [OfferingController::class, 'update'])->name('offerings.update');
        Route::patch('/ofertas/{offering}/deshabilitar', [OfferingController::class, 'disable'])->name('offerings.disable');
        Route::delete('/ofertas/{offering}', [OfferingController::class, 'destroy'])->name('offerings.destroy');

        // Matrículas semestrales
        Route::get('/matriculas', [TuitionController::class, 'index'])->name('tuitions.index');
        Route::post('/matriculas/estado', [TuitionController::class, 'toggle'])->name('tuitions.toggle');

        // Usuarios y perfiles (edición por Admin, Doble Carrera para estudiantes y Múltiples Facultades para docentes)
        Route::get('/usuarios', [UserProfileController::class, 'index'])->name('users.index');
        Route::post('/usuarios', [UserProfileController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{user}', [UserProfileController::class, 'show'])->name('users.show');
        Route::put('/usuarios/{user}', [UserProfileController::class, 'update'])->name('users.update');
        Route::patch('/usuarios/{user}/estado', [UserProfileController::class, 'toggleActive'])->name('users.toggle');
        Route::post('/usuarios/{user}/carreras', [UserProfileController::class, 'addCareer'])->name('users.careers.add');
        Route::delete('/usuarios/{user}/carreras/{career}', [UserProfileController::class, 'removeCareer'])->name('users.careers.remove');
        Route::put('/usuarios/{user}/facultades', [UserProfileController::class, 'syncFaculties'])->name('users.faculties.sync');
    });

    // ======================= DOCENTE =======================
    Route::middleware('role:docente')->prefix('docente')->name('teacher.')->group(function () {
        Route::get('/ofertas', [TeacherOfferingController::class, 'index'])->name('offerings.index');
        Route::get('/ofertas/{offering}', [TeacherOfferingController::class, 'show'])->name('offerings.show');
        Route::post('/ofertas/{offering}/tareas', [TeacherOfferingController::class, 'storeTask'])->name('tasks.store');
        Route::put('/tareas/{task}', [TeacherOfferingController::class, 'updateTask'])->name('tasks.update');
        Route::delete('/tareas/{task}', [TeacherOfferingController::class, 'destroyTask'])->name('tasks.destroy');
        Route::post('/tareas/{task}/calificar/{enrollment}', [TeacherOfferingController::class, 'gradeTask'])->name('tasks.grade');
        Route::post('/ofertas/{offering}/cerrar', [TeacherOfferingController::class, 'close'])->name('offerings.close');
        Route::post('/inscripciones/{enrollment}/corregir-nota', [TeacherOfferingController::class, 'adjustGrade'])->name('grades.adjust');
    });

    // ======================= ESTUDIANTE =======================
    Route::middleware('role:estudiante')->prefix('estudiante')->name('student.')->group(function () {
        Route::get('/inscripciones', [StudentPortalController::class, 'enrollments'])->name('enrollments.index');
        Route::post('/inscripciones', [StudentPortalController::class, 'storeEnrollment'])->name('enrollments.store');
        Route::get('/mis-materias/{enrollment}', [StudentPortalController::class, 'showEnrollment'])->name('enrollments.show');
        Route::get('/avance-pensum', [StudentPortalController::class, 'progress'])->name('progress');
    });
});
