<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

final class AuthController extends Controller
{
    /**
     * Show the admin login page.
     */
    public function showLogin(): Response
    {
        if (Auth::isAdmin()) {
            return $this->redirect('/admin');
        }

        View::setDefaultLayout(null);

        return $this->view('admin/auth/login', [
            'csrfToken'  => Session::csrfToken(),
            'flashError' => Session::getFlash('error'),
        ]);
    }

    /**
     * Handle admin login form submission.
     */
    public function login(): Response
    {
        $token = $this->input('_csrf_token', '');
        if (!Session::verifyCsrf((string) $token)) {
            Session::flash('error', 'Invalid security token. Please try again.');
            return $this->redirect('/admin/login');
        }

        $data = [
            'email'    => (string) $this->input('email', ''),
            'password' => (string) $this->input('password', ''),
        ];

        $validator = Validator::make($data, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Please provide a valid email and password.');
            return $this->redirect('/admin/login');
        }

        if (!Auth::adminAttempt($data['email'], $data['password'])) {
            Session::flash('error', 'Invalid email or password.');
            return $this->redirect('/admin/login');
        }

        Session::flash('success', 'Welcome back!');
        return $this->redirect('/admin');
    }

    /**
     * Handle admin logout.
     */
    public function logout(): Response
    {
        // Verify CSRF token on logout to prevent CSRF logout attacks
        $token = $this->input('_csrf_token', '');
        if (!Session::verifyCsrf((string) $token)) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin');
        }

        Auth::adminLogout();
        Session::flash('success', 'You have been logged out.');
        return $this->redirect('/admin/login');
    }
}
