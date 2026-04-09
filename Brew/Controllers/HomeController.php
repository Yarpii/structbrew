<?php
declare(strict_types=1);

namespace Brew\Controllers;

use Brew\Core\Controller;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Data\Products;
use Throwable;

final class HomeController extends Controller
{
    /**
     * @throws Throwable
     */
    public function index(Request $req): Response
    {
        return $this->view('home.index', [
            'title'       => 'Home',
            'featured'    => Products::featured(4),
            'trending'    => Products::trending(4),
            'newArrivals' => Products::newArrivals(4),
            'onSale'      => Products::onSale(),
            'categories'  => Products::categories(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function about(): Response
    {
        return $this->view('home.about', [
            'title' => 'About Us',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function contact(): Response
    {
        return $this->view('home.contact', [
            'title' => 'Contact',
        ]);
    }

    public function api(Request $req): Response
    {
        return $this->json([
            'status'     => 'ok',
            'framework'  => 'Structbrew',
            'path'       => $req->path(),
            'timestamp'  => time(),
        ]);
    }
}