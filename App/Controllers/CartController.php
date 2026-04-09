<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use Throwable;

final class CartController extends Controller
{
    /**
     * @throws Throwable
     */
    public function index(): Response
    {
        return $this->view('cart.index', [
            'title' => 'Shopping Cart',
        ]);
    }
}
