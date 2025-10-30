<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmrController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\MasterMedicineController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueMonitorController;
use App\Http\Controllers\QueueTicketController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Integration\BpjsController;
use App\Http\Controllers\Integration\IntegrationPageController;
use App\Http\Controllers\Integration\SatuSehatController;
use App\Http\Controllers\SearchSuggestionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/search/suggestions', SearchSuggestionController::class)
        ->middleware('throttle:60,1')
        ->name('search.suggestions');

    // API endpoint untuk autocomplete search
    Route::get('/api/patients/search', [PatientController::class, 'searchApi'])
        ->middleware('permission:patient.view')
        ->name('api.patients.search');

    // Patient routes with individual permission checks
    Route::get('patients', [PatientController::class, 'index'])
        ->middleware('permission:patient.view')
        ->name('patients.index');
    Route::get('patients/create', [PatientController::class, 'create'])
        ->middleware('permission:patient.create')
        ->name('patients.create');
    Route::post('patients', [PatientController::class, 'store'])
        ->middleware('permission:patient.create')
        ->name('patients.store');
    Route::get('patients/{patient}', [PatientController::class, 'show'])
        ->middleware('permission:patient.view')
        ->name('patients.show');
    Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])
        ->middleware('permission:patient.update')
        ->name('patients.edit');
    Route::put('patients/{patient}', [PatientController::class, 'update'])
        ->middleware('permission:patient.update')
        ->name('patients.update');
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('permission:patient.delete')
        ->name('patients.destroy');

    Route::get('registrations', [RegistrationController::class, 'index'])
        ->middleware('permission:patient.create|queue.create')
        ->name('registrations.index');

    Route::get('integration', [IntegrationPageController::class, 'index'])
        ->middleware('permission:bpjs.verify|satusehat.sync')
        ->name('integration.index');
    
    Route::get('integration/bpjs-vclaim', [IntegrationPageController::class, 'bpjsVclaim'])
        ->middleware('permission:bpjs.verify')
        ->name('integration.bpjs-vclaim');

    Route::post('registrations/queue', [RegistrationController::class, 'storeExistingQueue'])
        ->middleware('permission:queue.create')
        ->name('registrations.queue.store');

    Route::get('registrations/sep/{patient}', [RegistrationController::class, 'printSep'])
        ->middleware('permission:patient.view')
        ->name('registrations.sep.print');

    Route::middleware('permission:user.manage')->group(function () {
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    Route::resource('visits', VisitController::class)
        ->only(['index', 'create', 'store', 'show', 'update'])
        ->middleware([
            'index' => 'permission:visit.view',
            'show' => 'permission:visit.view',
            'create' => 'permission:visit.create',
            'store' => 'permission:visit.create',
            'update' => 'permission:visit.update',
        ]);

    Route::get('visits/{visit}/emr', [EmrController::class, 'show'])
        ->middleware('permission:emr.create')
        ->name('emr.show');
    Route::post('visits/{visit}/emr', [EmrController::class, 'store'])
        ->middleware('permission:emr.create')
        ->name('emr.store');

    // Lab Work Queue routes (untuk petugas lab)
    Route::get('lab', [LabController::class, 'index'])
        ->middleware('permission:lab.view')
        ->name('lab.index');
    Route::get('lab/{labOrder}/input-result', [LabController::class, 'inputResult'])
        ->middleware('permission:lab.result')
        ->name('lab.input-result');
    Route::post('lab/{labOrder}/store-result', [LabController::class, 'storeResult'])
        ->middleware('permission:lab.result')
        ->name('lab.store-result');
    Route::get('lab/{labOrder}/show', [LabController::class, 'show'])
        ->middleware('permission:lab.view')
        ->name('lab.show');
    Route::get('lab/{labOrder}/print', [LabController::class, 'print'])
        ->middleware('permission:lab.view')
        ->name('lab.print');

    // Pharmacy (Apotek) routes
    Route::get('pharmacy', [PharmacyController::class, 'index'])
        ->name('pharmacy.index');
    Route::get('pharmacy/{prescription}/process', [PharmacyController::class, 'process'])
        ->name('pharmacy.process');
    Route::post('pharmacy/{prescription}/dispense', [PharmacyController::class, 'dispense'])
        ->name('pharmacy.dispense');
    Route::post('pharmacy/{prescription}/cancel', [PharmacyController::class, 'cancel'])
        ->name('pharmacy.cancel');

    // Master Medicines (Manajemen Stok Obat) routes
    Route::get('medicines', [MasterMedicineController::class, 'index'])
        ->name('medicines.index');
    Route::get('medicines/create', [MasterMedicineController::class, 'create'])
        ->name('medicines.create');
    Route::post('medicines', [MasterMedicineController::class, 'store'])
        ->name('medicines.store');
    Route::get('medicines/{medicine}/edit', [MasterMedicineController::class, 'edit'])
        ->name('medicines.edit');
    Route::put('medicines/{medicine}', [MasterMedicineController::class, 'update'])
        ->name('medicines.update');
    Route::delete('medicines/{medicine}', [MasterMedicineController::class, 'destroy'])
        ->name('medicines.destroy');

    Route::get('lab-orders', [LabOrderController::class, 'index'])
        ->middleware('permission:lab.view')
        ->name('lab-orders.index');
    Route::get('lab-orders/create', [LabOrderController::class, 'create'])
        ->middleware('permission:lab.create')
        ->name('lab-orders.create');
    Route::post('lab-orders', [LabOrderController::class, 'store'])
        ->middleware('permission:lab.create')
        ->name('lab-orders.store');
    Route::get('lab-orders/{labOrder}/edit', [LabOrderController::class, 'edit'])
        ->middleware('permission:lab.result|lab.update')
        ->name('lab-orders.edit');
    Route::put('lab-orders/{labOrder}', [LabOrderController::class, 'update'])
        ->middleware('permission:lab.result|lab.update')
        ->name('lab-orders.update');

    Route::get('queues', [QueueTicketController::class, 'index'])
        ->middleware('permission:queue.view')
        ->name('queues.index');
    Route::post('queues', [QueueTicketController::class, 'store'])
        ->middleware('permission:queue.create')
        ->name('queues.store');
    Route::put('queues/{queue}', [QueueTicketController::class, 'update'])
        ->middleware('permission:queue.update')
        ->name('queues.update');

    // Mulai layani pasien dari antrian (khusus dokter)
    Route::post('queues/{queue}/serve', [QueueTicketController::class, 'serve'])
        ->middleware('permission:visit.create')
        ->name('queues.serve');

    Route::resource('medicines', MedicineController::class)
        ->only(['index', 'create', 'store', 'edit', 'update'])
        ->middleware([
            'index' => 'permission:medicine.view',
            'create' => 'permission:medicine.create',
            'store' => 'permission:medicine.create',
            'edit' => 'permission:medicine.update',
            'update' => 'permission:medicine.update',
        ]);

    Route::resource('referrals', ReferralController::class)
        ->only(['index', 'create', 'store', 'show', 'update'])
        ->middleware([
            'index' => 'permission:referral.view',
            'create' => 'permission:referral.create',
            'store' => 'permission:referral.create',
            'show' => 'permission:referral.view',
            'update' => 'permission:referral.update',
        ]);

    Route::get('reports/visits', [ReportController::class, 'visitsMonthly'])
        ->middleware('permission:report.view')
        ->name('reports.visits');

    // BPJS VClaim Routes
    Route::post('bpjs/cek-peserta', [BpjsController::class, 'cekPesertaByNik'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.cek-peserta');
    
    Route::post('bpjs/cek-peserta-kartu', [BpjsController::class, 'cekPesertaByKartu'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.cek-peserta-kartu');
    
    Route::post('bpjs/sep/create', [BpjsController::class, 'createSep'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.sep.create');
    
    Route::put('bpjs/sep/update', [BpjsController::class, 'updateSep'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.sep.update');
    
    Route::delete('bpjs/sep/delete', [BpjsController::class, 'deleteSep'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.sep.delete');
    
    Route::post('bpjs/rujukan/cek', [BpjsController::class, 'cekRujukan'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.rujukan.cek');
    
    Route::get('bpjs/referensi/diagnosa', [BpjsController::class, 'getDiagnoses'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.referensi.diagnosa');
    
    Route::get('bpjs/referensi/poli', [BpjsController::class, 'getPolyclinics'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.referensi.poli');
    
    Route::get('bpjs/monitoring/sep', [BpjsController::class, 'monitorSep'])
        ->middleware(['permission:bpjs.verify', 'throttle:bpjs'])
        ->name('bpjs.monitoring.sep');

    Route::post('satusehat/patient/{patient}/sync', [SatuSehatController::class, 'syncPatient'])
        ->middleware('permission:satusehat.sync')
        ->name('satusehat.sync-patient');
    Route::post('satusehat/visit/{visit}/sync', [SatuSehatController::class, 'syncEncounter'])
        ->middleware('permission:satusehat.sync')
        ->name('satusehat.sync-encounter');

    Route::get('queue-monitor', [QueueMonitorController::class, 'index'])
        ->middleware('permission:queue.manage')
        ->name('queue.monitor');

    Route::get('audit/logs', [AuditLogController::class, 'index'])
        ->middleware('ensure.permission:audit.view')
        ->name('audit.logs');

    Route::get('audit/logs/{log}', [AuditLogController::class, 'show'])
        ->middleware('ensure.permission:audit.view')
        ->name('audit.logs.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
