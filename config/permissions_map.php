<?php
/**
 * Route → Permission Key Map
 *
 * Every authenticated route maps to ONE permission key.
 * The Router loads this file and checks the DB to see whether
 * the current user's role has that permission.
 *
 * Keys  : 'controller/action'  (all lowercase)
 * Values: permission_key string, or NULL to allow any authenticated user
 *
 * Routes NOT listed here are denied to everyone except SuperAdmin.
 */
return [
    // ── Dashboard ────────────────────────────────────────────
    'dashboard/index'          => 'dashboard.view',

    // ── Assets ───────────────────────────────────────────────
    'asset/index'              => 'asset.view',
    'asset/add'                => 'asset.create',
    'asset/edit'               => 'asset.edit',
    'asset/delete'             => 'asset.delete',

    // ── Assignments ──────────────────────────────────────────
    'assignment/index'         => 'assignment.view',
    'assignment/assign'        => 'assignment.create',
    'assignment/edit'          => 'assignment.edit',
    'assignment/delete'        => 'assignment.delete',
    'assignment/markreturn'    => 'assignment.return',
    'assignment/undoreturn'    => 'assignment.return',
    'assignment/returnasset'   => 'assignment.return',

    // ── Maintenance ──────────────────────────────────────────
    'maintenance/index'        => 'maintenance.view',
    'maintenance/add'          => 'maintenance.create',
    'maintenance/updatestatus' => 'maintenance.update',
    'maintenance/delete'       => 'maintenance.delete',

    // ── Employees ────────────────────────────────────────────
    'employee/index'           => 'employee.view',
    'employee/add'             => 'employee.create',
    'employee/edit'            => 'employee.edit',
    'employee/delete'          => 'employee.delete',

    // ── Vendors ──────────────────────────────────────────────
    'vendor/index'             => 'vendor.view',
    'vendor/add'               => 'vendor.create',
    'vendor/edit'              => 'vendor.edit',
    'vendor/delete'            => 'vendor.delete',

    // ── Equipment Requests ───────────────────────────────────
    'request/index'            => 'request.view',
    'request/create'           => 'request.create',
    'request/edit'             => 'request.edit',
    'request/approve'          => 'request.approve',
    'request/reject'           => 'request.approve',
    'request/updatestatus'     => 'request.approve',
    'request/delete'           => 'request.delete',

    // ── Admin panel (Admin role) ──────────────────────────────
    'admin/index'              => 'admin.panel',
    'admin/logs'               => 'admin.panel',
    'admin/users'              => 'admin.users',
    'admin/adduser'            => 'admin.users',
    'admin/edituser'           => 'admin.users',
    'admin/deleteuser'         => 'admin.users',

    // ── SuperAdmin panel ─────────────────────────────────────
    // Handled by Router before this map is consulted (SuperAdmin bypass)
    // Listed here only for completeness – key is unused
    'superadmin/index'         => null,
    'superadmin/permissions'   => null,
    'superadmin/users'         => null,
    'superadmin/adduser'       => null,
    'superadmin/edituser'      => null,
    'superadmin/deleteuser'    => null,

    // ── Auth – available to every authenticated user ──────────
    'auth/changepassword'      => null,
    'auth/logout'              => null,
];
