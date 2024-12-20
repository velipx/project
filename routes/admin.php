<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\NotificationController;

// Route Paths
const ADMIN_USERS = 'users';
const ADMIN_ROLES = 'roles';
const ADMIN_PERMISSIONS = 'permissions';
const ADMIN_NOTIFICATIONS = 'notifications';
const ADMIN_FRIENDSHIP = 'friendship';

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Users Grouped
    Route::controller(UsersController::class)->prefix(ADMIN_USERS)->name('admin.users')->group(function () {
        Route::get('/', 'index')->name('');
        Route::post('create', 'store')->name('.store');
        Route::get('create', 'showCreateModal')->name('.create');
        Route::get('remove', 'showDeleteConfirmationModal')->name('.remove');
        Route::delete('destroy', 'destroy')->name('.destroy');
        Route::post('restore/{id}', 'restore')->name('.restore');

        // User
        Route::get('{user:username}/avatar', 'showAvatarUploadModal')->name('.upload-avatar-modal');
        Route::post('{user:username}/avatar', 'uploadAvatar')->name('.upload-avatar');
        Route::get('{user:username}/change-password', 'showChangePassword')->name('.change-password');
        Route::post('{user:username}/update-password', 'updatePassword')->name('.update-password');
        Route::get('{user:username}/sessions', 'showSessions')->name('.sessions');
        Route::post('{user:username}/sessions/logout-session', 'logoutSession')->name('.sessions.logout');
        Route::post('{user:username}/sessions/logout-all-other-sessions', 'logoutAllOtherSessions')->name('.sessions.logout-all-other');
        Route::get('{user:username}', 'show')->name('.show');
    });

    // Roles Grouped
    Route::controller(RolesController::class)->prefix(ADMIN_ROLES)->name('admin.roles')->group(function () {
        Route::get('/', 'index')->name('');
        Route::post('create', 'store')->name('.store');
        Route::get('create', 'showCreateModal')->name('.create');
        Route::get('remove', 'showDeleteConfirmationModal')->name('.remove');
        Route::delete('destroy', 'destroy')->name('.destroy');
        Route::post('restore/{id}', 'restore')->name('.restore');
        Route::post('{role}/update-permissions', 'updatePermissions')->name('.update-permissions');
        Route::get('{role}', 'show')->name('.show');
    });

    // Permissions Grouped
    Route::controller(PermissionsController::class)->prefix(ADMIN_PERMISSIONS)->name('admin.permissions')->group(function () {
        Route::get('/', 'index')->name('');
        Route::post('create', 'store')->name('.store');
        Route::get('create', 'showCreateModal')->name('.create');
        Route::get('remove', 'showDeleteConfirmationModal')->name('.remove');
        Route::delete('destroy', 'destroy')->name('.destroy');
        Route::post('/restore/{id}', 'restore')->name('.restore');
    });

    Route::controller(FriendshipController::class)->prefix(ADMIN_FRIENDSHIP)->name('admin.friendship')->group(function () {
        Route::post('request/{receiver}', 'sendRequest')->name('.request');
        Route::post('accept/{friendship}', 'acceptRequest')->name('.accept');
        Route::post('reject/{friendship}', 'rejectRequest')->name('.reject');
    });

    Route::post('/admin/users/{id}/login-as', [UsersController::class, 'loginAsUser'])->name('admin.users.loginAs');
    Route::post('/admin/revert-impersonation', [UsersController::class, 'revertImpersonation'])->name('admin.revertImpersonation');

    Route::middleware(['auth:sanctum', 'verified'])->get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::middleware(['auth:sanctum', 'verified'])->post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});
