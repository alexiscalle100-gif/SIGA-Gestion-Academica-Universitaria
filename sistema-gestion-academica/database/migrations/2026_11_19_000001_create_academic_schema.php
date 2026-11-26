<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name', 160);
            $table->json('shifts'); // ['manana', 'tarde', 'noche']
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false); // Inicia inhabilitada hasta aprobación administrativa
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('estudiante')->index()->after('password');
            $table->string('code', 30)->nullable()->unique()->after('role');
            $table->string('document_number', 30)->nullable()->after('code');
            $table->string('phone', 30)->nullable()->after('document_number');
            $table->foreignId('faculty_id')->nullable()->after('phone')->constrained('faculties')->nullOnDelete();
            $table->foreignId('career_id')->nullable()->after('faculty_id')->constrained('careers')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('career_id');
        });

        // Soporte para Doble Carrera (incluso en distintas facultades)
        Schema::create('student_careers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('career_id')->constrained('careers')->restrictOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();

            $table->unique(['student_id', 'career_id'], 'student_careers_unique');
        });

        // Soporte para Docentes asignados a múltiples facultades
        Schema::create('teacher_faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['teacher_id', 'faculty_id'], 'teacher_faculties_unique');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->restrictOnDelete();
            $table->string('code', 25)->unique();
            $table->string('area_prefix', 10)->index();
            $table->string('name', 160);
            $table->unsignedTinyInteger('credits')->default(4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained('careers')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->unsignedTinyInteger('level'); // 1..8
            $table->unsignedTinyInteger('month_in_level'); // 1..6
            $table->unsignedTinyInteger('order_in_curriculum'); // 1..48
            $table->boolean('is_elective')->default(false);
            $table->unsignedTinyInteger('elective_number')->nullable(); // 1..4
            $table->unsignedTinyInteger('requires_full_level')->nullable(); // ej. requiere nivel N completo aprobado
            $table->timestamps();

            $table->unique(['career_id', 'subject_id'], 'curriculum_career_subject_unique');
            $table->index(['career_id', 'level', 'month_in_level']);
        });

        Schema::create('curriculum_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->cascadeOnDelete();
            $table->foreignId('prerequisite_subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['curriculum_item_id', 'prerequisite_subject_id'], 'curr_prereq_unique');
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('period'); // 1 (Ene-Jun) o 2 (Jul-Dic)
            $table->string('code', 15)->unique(); // ej. 2026-I, 2026-II
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->unique(['year', 'period']);
        });

        Schema::create('tuitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('career_id')->constrained('careers')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->boolean('is_active')->default(false);
            $table->unsignedTinyInteger('max_subjects')->default(6);
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'career_id', 'semester_id'], 'tuitions_student_career_semester_unique');
        });

        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->unsignedTinyInteger('month'); // 1..12
            $table->date('start_date');
            $table->date('end_date');
            $table->string('shift', 15); // manana, tarde, noche
            $table->string('start_time', 5)->default('07:30'); // Hora inicio turno
            $table->string('end_time', 5)->default('11:30');   // Hora fin turno
            $table->string('days_label', 40)->default('Lunes a Viernes');
            $table->unsignedTinyInteger('max_capacity')->default(50);
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('classroom')->nullable(); // Opcional al habilitar; asignable luego
            $table->boolean('is_disabled')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['semester_id', 'month', 'shift']);
            $table->index(['teacher_id', 'semester_id', 'month', 'shift']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('career_id')->nullable()->constrained('careers')->nullOnDelete();
            $table->foreignId('offering_id')->constrained('offerings')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->unsignedTinyInteger('final_score')->nullable(); // Notas enteras 0..100
            $table->string('status', 20)->default('en_curso'); // en_curso, aprobada, reprobada, anulada
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'offering_id'], 'enrollments_student_offering_unique');
            $table->index(['student_id', 'subject_id', 'attempt_number']);
            $table->index(['student_id', 'semester_id', 'status']);
        });

        Schema::create('offering_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offering_id')->constrained('offerings')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('max_points'); // 1..100
            $table->date('due_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('task_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offering_task_id')->constrained('offering_tasks')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable(); // Actividad presentada por el estudiante
            $table->string('submission_note', 255)->nullable();
            $table->unsignedTinyInteger('score')->nullable(); // NULL = Sin calificar, 0 = Calificada con 0
            $table->text('comment')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['offering_task_id', 'enrollment_id'], 'task_grades_unique');
        });

        Schema::create('grade_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('adjusted_by')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('previous_score');
            $table->unsignedTinyInteger('new_score');
            $table->string('previous_status', 20);
            $table->string('new_status', 20);
            $table->string('reason', 255);
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('message');
            $table->string('type', 30)->default('info');
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);
            $table->string('description', 255);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('grade_adjustments');
        Schema::dropIfExists('task_grades');
        Schema::dropIfExists('offering_tasks');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('offerings');
        Schema::dropIfExists('tuitions');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('curriculum_prerequisites');
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('teacher_faculties');
        Schema::dropIfExists('student_careers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('career_id');
            $table->dropConstrainedForeignId('faculty_id');
            $table->dropColumn(['role', 'code', 'document_number', 'phone', 'is_active']);
        });

        Schema::dropIfExists('careers');
        Schema::dropIfExists('faculties');
    }
};
