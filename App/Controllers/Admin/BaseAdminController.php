<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

abstract class BaseAdminController extends Controller
{
    protected array $storeViews = [];

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);

        // Enforce admin authentication - redirect if not logged in
        if (!Auth::isAdmin()) {
            Session::flash('error', 'Please log in to access the admin panel.');
            $this->response = Response::redirect('/admin/login');
            return;
        }

        View::setDefaultLayout('admin/layout/app');

        $adminUser = Auth::admin();
        View::share('adminUser', $adminUser);

        $this->storeViews = Database::getInstance()
            ->table('store_views')
            ->orderBy('sort_order', 'ASC')
            ->get();

        View::share('storeViews', $this->storeViews);
        View::share('csrfToken', Session::csrfToken());
        View::share('flashSuccess', Session::getFlash('success'));
        View::share('flashError', Session::getFlash('error'));
    }

    /**
     * Render an admin view with shared admin data merged in.
     */
    protected function adminView(string $viewName, array $data = [], int $status = 200): Response
    {
        $data = array_merge([
            'adminUser'    => Auth::admin(),
            'storeViews'   => $this->storeViews,
            'csrfToken'    => Session::csrfToken(),
            'flashSuccess' => Session::getFlash('success'),
            'flashError'   => Session::getFlash('error'),
        ], $data);

        return $this->view($viewName, $data, $status);
    }

    /**
     * Get the current page number from the query string.
     */
    protected function page(): int
    {
        $page = (int) $this->request->query('page');
        return $page > 0 ? $page : 1;
    }

    /**
     * Verify CSRF token or redirect back with an error.
     */
    protected function verifyCsrf(): bool
    {
        $token = $this->input('_csrf_token', '');
        return Session::verifyCsrf((string) $token);
    }

    /**
     * Log an admin activity.
     */
    protected function logActivity(
        string $action,
        string $entityType,
        int|string $entityId,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $db = Database::getInstance();
        $admin = Auth::admin();

        $db->table('activity_log')->insert([
            'admin_user_id' => $admin['id'] ?? null,
            'action'        => $action,
            'entity_type'   => $entityType,
            'entity_id'     => (int) $entityId,
            'old_data'      => $oldData !== null ? json_encode($oldData) : null,
            'new_data'      => $newData !== null ? json_encode($newData) : null,
            'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
