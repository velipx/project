<?php


use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});

// Admin > Roles
Breadcrumbs::for('admin.roles', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Roles', route('admin.roles'));
});

// Admin > Roles > [Role]
Breadcrumbs::for('admin.roles.show', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('admin.roles');
    $trail->push($role->name, route('admin.roles.show', $role));
});

// Admin > Roles > Create
Breadcrumbs::for('admin.roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles');
    $trail->push('Create', route('admin.roles.create'));
});

// Admin > Roles > Remove
Breadcrumbs::for('admin.roles.remove', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles');
    $trail->push('Remove', route('admin.roles.remove'));
});


// Admin > Users
Breadcrumbs::for('admin.users', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Users', route('admin.users'));
});

// Admin > Users > [User]
Breadcrumbs::for('admin.users.show', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.users');
    $trail->push($user, route('admin.users.show', $user));
});

// Admin > Users > [User] > Sessions
Breadcrumbs::for('admin.users.sessions', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.users.show', $user->username);
    $trail->push('Sessions', route('admin.users.sessions', $user->username));
});

// Admin > Users > Create
Breadcrumbs::for('admin.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users');
    $trail->push('Create', route('admin.users.create'));
});

// Admin > Users > Remove
Breadcrumbs::for('admin.users.remove', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users');
    $trail->push('Remove', route('admin.users.remove'));
});
