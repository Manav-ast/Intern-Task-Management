<?php

use Illuminate\Support\Facades\Auth;

/**
 * Get the authenticated admin user.
 *
 * @return \App\Models\Admin|null
 */
function admin()
{
    return auth('admin')->user();
}

/**
 * Get the authenticated admin ID.
 *
 * @return int|null
 */
function admin_id()
{
    return auth('admin')->id();
}

/**
 * Check if an admin is authenticated.
 *
 * @return bool
 */
function is_admin()
{
    return auth('admin')->check();
}

/**
 * Get the admin authentication guard.
 *
 * @return \Illuminate\Contracts\Auth\StatefulGuard
 */
function admin_guard()
{
    return Auth::guard('admin');
}

/**
 * Get the authenticated intern user.
 *
 * @return \App\Models\Intern|null
 */
function intern()
{
    return auth('intern')->user();
}

/**
 * Get the authenticated intern ID.
 *
 * @return int|null
 */
function intern_id()
{
    return auth('intern')->id();
}

/**
 * Check if an intern is authenticated.
 *
 * @return bool
 */
function is_intern()
{
    return auth('intern')->check();
}

/**
 * Get the intern authentication guard.
 *
 * @return \Illuminate\Contracts\Auth\StatefulGuard
 */
function intern_guard()
{
    return Auth::guard('intern');
}
