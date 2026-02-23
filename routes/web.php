<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BacklinkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/backlinks/{backlink}/check', [BacklinkController::class, 'check'])->name('projects.backlinks.check');
    Route::post('/projects/{project}/backlinks/bulk-check', [BacklinkController::class, 'bulkCheck'])->name('projects.backlinks.bulk-check');
    Route::get('/projects/{project}/backlinks/export', [BacklinkController::class, 'export'])->name('projects.backlinks.export');
    Route::resource('projects.backlinks', BacklinkController::class);
    Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::resource('projects.metrics', MetricController::class)->only(['index', 'store', 'destroy']);
    Route::get('/projects/{project}/report', [ReportController::class, 'index'])->name('projects.report');
    Route::resource('users', UserController::class)->middleware('admin');
    Route::get('/projects/{project}/export/backlinks', [ExportController::class, 'exportBacklinks'])->name('projects.export.backlinks');
    Route::get('/projects/{project}/export/metrics', [ExportController::class, 'exportMetrics'])->name('projects.export.metrics');
    Route::get('/projects/{project}/export/broken-backlinks', [ExportController::class, 'exportBrokenBacklinks'])->name('projects.export.broken-backlinks');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications');
    Route::get('/api/documentation', [ApiDocumentationController::class, 'index'])->name('api.documentation');
    Route::get('/settings/email', [EmailSettingController::class, 'index'])->name('settings.email');
    Route::post('/settings/email', [EmailSettingController::class, 'updateSettings'])->name('settings.email.update');
    Route::post('/settings/email/test', [EmailSettingController::class, 'testEmail'])->name('settings.email.test');
    Route::post('/settings/email/template/{template}', [EmailSettingController::class, 'updateTemplate'])->name('settings.email.template.update');
    Route::post('/settings/schedule', [SettingsController::class, 'updateSchedule'])->name('settings.updateSchedule');
    Route::get('/projects/{project}/backlinks/progress/{progress}', [App\Http\Controllers\BacklinkController::class, 'progress'])->name('projects.backlinks.progress');
});

require __DIR__.'/auth.php';
