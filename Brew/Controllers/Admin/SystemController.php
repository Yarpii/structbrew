<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Auth;
use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class SystemController extends BaseAdminController
{
    // ─── Admin Users ─────────────────────────────────────────

    /**
     * List all admin users.
     */
    public function users(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;

        $users = $db->table('admin_users')
            ->select('admin_users.*', 'admin_roles.name as role_name')
            ->leftJoin('admin_roles', 'admin_users.role_id', '=', 'admin_roles.id')
            ->orderBy('admin_users.created_at', 'DESC')
            ->paginate($perPage, $page);

        return $this->adminView('admin/system/users', [
            'title' => 'Admin Users',
            'users' => $users,
        ]);
    }

    /**
     * Show admin user creation form.
     */
    public function createUser(): Response
    {
        $db = Database::getInstance();

        $roles = $db->table('admin_roles')
            ->orderBy('name', 'ASC')
            ->get();

        return $this->adminView('admin/system/create-user', [
            'title' => 'Create Admin User',
            'roles' => $roles,
        ]);
    }

    /**
     * Store a new admin user.
     */
    public function storeUser(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/system/users/create');
        }

        $data = [
            'first_name'   => (string) $this->input('first_name', ''),
            'last_name'    => (string) $this->input('last_name', ''),
            'email'        => (string) $this->input('email', ''),
            'password'     => (string) $this->input('password', ''),
            'role_id'      => (string) $this->input('role_id', ''),
            'is_active'    => $this->input('is_active', '0'),
            'is_superadmin' => $this->input('is_superadmin', '0'),
        ];

        $validator = Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => 'required|email|unique:admin_users,email',
            'password'   => 'required|min:8',
            'role_id'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/system/users/create');
        }

        // Only superadmins can create other superadmins
        $currentAdmin = Auth::admin();
        $isSuperadmin = (int) $data['is_superadmin'];
        if ($isSuperadmin && !($currentAdmin['is_superadmin'] ?? false)) {
            $isSuperadmin = 0;
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $userId = $db->table('admin_users')->insert([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'password_hash' => Auth::hashPassword($data['password']),
            'role_id'       => (int) $data['role_id'],
            'is_active'     => (int) $data['is_active'],
            'is_superadmin' => $isSuperadmin,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $this->logActivity('create', 'admin_user', $userId, null, [
            'email'     => $data['email'],
            'role_id'   => $data['role_id'],
        ]);

        Session::flash('success', 'Admin user created successfully.');
        return $this->redirect('/admin/system/users');
    }

    /**
     * Show admin user edit form.
     */
    public function editUser(int $id): Response
    {
        $db = Database::getInstance();

        $user = $db->table('admin_users')->where('id', $id)->first();
        if (!$user) {
            Session::flash('error', 'Admin user not found.');
            return $this->redirect('/admin/system/users');
        }

        $roles = $db->table('admin_roles')
            ->orderBy('name', 'ASC')
            ->get();

        return $this->adminView('admin/system/edit-user', [
            'title'     => 'Edit Admin User',
            'adminUserRecord' => $user,
            'roles'     => $roles,
        ]);
    }

    /**
     * Update an existing admin user.
     */
    public function updateUser(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/system/users/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $user = $db->table('admin_users')->where('id', $id)->first();
        if (!$user) {
            Session::flash('error', 'Admin user not found.');
            return $this->redirect('/admin/system/users');
        }

        $data = [
            'first_name'    => (string) $this->input('first_name', ''),
            'last_name'     => (string) $this->input('last_name', ''),
            'email'         => (string) $this->input('email', ''),
            'role_id'       => (string) $this->input('role_id', ''),
            'is_active'     => $this->input('is_active', '0'),
            'is_superadmin' => $this->input('is_superadmin', '0'),
        ];

        $validator = Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => 'required|email|unique:admin_users,email,' . $id,
            'role_id'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/system/users/' . $id . '/edit');
        }

        $currentAdmin = Auth::admin();
        $isSuperadmin = (int) $data['is_superadmin'];
        if ($isSuperadmin && !($currentAdmin['is_superadmin'] ?? false)) {
            $isSuperadmin = (int) ($user['is_superadmin'] ?? 0);
        }

        $updateData = [
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'role_id'       => (int) $data['role_id'],
            'is_active'     => (int) $data['is_active'],
            'is_superadmin' => $isSuperadmin,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Update password only if provided
        $newPassword = (string) $this->input('password', '');
        if ($newPassword !== '') {
            $passwordValidator = Validator::make(['password' => $newPassword], [
                'password' => 'min:8',
            ]);
            if ($passwordValidator->fails()) {
                Session::flash('error', 'Password must be at least 8 characters.');
                return $this->redirect('/admin/system/users/' . $id . '/edit');
            }
            $updateData['password_hash'] = Auth::hashPassword($newPassword);
        }

        $db->table('admin_users')->where('id', $id)->update($updateData);

        $this->logActivity('update', 'admin_user', $id, [
            'email'   => $user['email'],
            'role_id' => $user['role_id'],
        ], [
            'email'   => $data['email'],
            'role_id' => $data['role_id'],
        ]);

        Session::flash('success', 'Admin user updated successfully.');
        return $this->redirect('/admin/system/users/' . $id . '/edit');
    }

    /**
     * Delete an admin user.
     */
    public function deleteUser(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/system/users');
        }

        $db = Database::getInstance();

        $user = $db->table('admin_users')->where('id', $id)->first();
        if (!$user) {
            Session::flash('error', 'Admin user not found.');
            return $this->redirect('/admin/system/users');
        }

        // Prevent self-deletion
        $currentAdmin = Auth::admin();
        if ($currentAdmin && (int) $currentAdmin['id'] === $id) {
            Session::flash('error', 'You cannot delete your own account.');
            return $this->redirect('/admin/system/users');
        }

        // Prevent deleting the last superadmin
        if ((int) ($user['is_superadmin'] ?? 0) === 1) {
            $superadminCount = $db->table('admin_users')
                ->where('is_superadmin', 1)
                ->where('is_active', 1)
                ->count();
            if ($superadminCount <= 1) {
                Session::flash('error', 'Cannot delete the last active superadmin.');
                return $this->redirect('/admin/system/users');
            }
        }

        $db->table('admin_users')->where('id', $id)->delete();

        $this->logActivity('delete', 'admin_user', $id, [
            'email' => $user['email'],
        ]);

        Session::flash('success', 'Admin user deleted successfully.');
        return $this->redirect('/admin/system/users');
    }

    // ─── Activity Log ────────────────────────────────────────

    /**
     * Show activity log with pagination.
     */
    public function activity(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 30;

        $entityType = $this->request->query('entity_type');
        $adminUserId = $this->request->query('admin_user_id');

        $query = $db->table('activity_log')
            ->select(
                'activity_log.*',
                'admin_users.first_name as admin_first_name',
                'admin_users.last_name as admin_last_name',
                'admin_users.email as admin_email'
            )
            ->leftJoin('admin_users', 'activity_log.admin_user_id', '=', 'admin_users.id')
            ->orderBy('activity_log.created_at', 'DESC');

        if ($entityType) {
            $query->where('activity_log.entity_type', $entityType);
        }

        if ($adminUserId) {
            $query->where('activity_log.admin_user_id', (int) $adminUserId);
        }

        $logs = $query->paginate($perPage, $page);

        // Get distinct entity types for filter
        $entityTypes = $db->table('activity_log')
            ->select('entity_type')
            ->groupBy('entity_type')
            ->get();
        $entityTypeList = array_column($entityTypes, 'entity_type');

        // Get admin users for filter
        $adminUsers = $db->table('admin_users')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name', 'ASC')
            ->get();

        return $this->adminView('admin/system/activity', [
            'title'          => 'Activity Log',
            'logs'           => $logs,
            'entityTypeList' => $entityTypeList,
            'adminUsers'     => $adminUsers,
            'entityType'     => $entityType,
            'adminUserId'    => $adminUserId,
        ]);
    }

    // ─── Admin Roles ─────────────────────────────────────────

    /**
     * List all admin roles.
     */
    public function roles(): Response
    {
        $db = Database::getInstance();

        $roles = $db->table('admin_roles')
            ->orderBy('name', 'ASC')
            ->get();

        // Add user count per role
        foreach ($roles as &$role) {
            $role['user_count'] = $db->table('admin_users')
                ->where('role_id', $role['id'])
                ->count();

            // Decode permissions for display
            $role['permissions_list'] = json_decode($role['permissions'] ?? '[]', true) ?: [];
        }
        unset($role);

        return $this->adminView('admin/system/roles', [
            'title' => 'Admin Roles',
            'roles' => $roles,
        ]);
    }
}
