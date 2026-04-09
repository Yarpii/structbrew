<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use Throwable;

final class AuthController extends Controller
{
    /**
     * @throws Throwable
     */
    public function login(): Response
    {
        return $this->view('auth.login', [
            'title' => 'Login',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function register(): Response
    {
        return $this->view('auth.register', [
            'title' => 'Create Account',
        ]);
    }
}
