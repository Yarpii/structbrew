<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

abstract class BaseStorefrontController extends Controller
{
    protected mixed $flashSuccess;
    protected mixed $flashError;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);

        $this->flashSuccess = Session::getFlash('success');
        $this->flashError = Session::getFlash('error');

        View::setDefaultLayout('layout/app');
        View::share('currentCustomer', Auth::customer());
        View::share('isLoggedIn', Auth::isLoggedIn());
        View::share('csrfToken', Session::csrfToken());
        View::share('flashSuccess', $this->flashSuccess);
        View::share('flashError', $this->flashError);
    }

    protected function storefrontView(string $viewName, array $data = [], int $status = 200): Response
    {
        $data = array_merge([
            'currentCustomer' => Auth::customer(),
            'isLoggedIn' => Auth::isLoggedIn(),
            'csrfToken' => Session::csrfToken(),
            'flashSuccess' => $this->flashSuccess,
            'flashError' => $this->flashError,
        ], $data);

        return $this->view($viewName, $data, $status);
    }

    protected function verifyCsrf(): bool
    {
        return Session::verifyCsrf((string) $this->input('_csrf_token', ''));
    }

    protected function redirectIfGuest(string $redirect = '/login'): ?Response
    {
        if (!Auth::isLoggedIn()) {
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect($redirect);
        }

        return null;
    }
}
