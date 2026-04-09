<?php
declare(strict_types=1);
namespace Brew\Controllers;

use Brew\Core\Controller;
use Brew\Core\Request;
use Brew\Core\Response;
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
