<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Translator;
use App\Data\Products;
use Throwable;

final class HomeController extends Controller
{
    /**
     * @throws Throwable
     */
    public function index(Request $req): Response
    {
        Translator::page('shop');

        $db = Database::getInstance();

        // Garage: brands + vehicle options (needed for both guests and logged-in)
        $brands = $db->table('brands')->where('is_active', 1)->orderBy('name', 'ASC')->get();
        $brandById = [];
        foreach ($brands as $brand) {
            $brandById[(int) $brand['id']] = (string) $brand['name'];
        }

        $vehicles = $db->table('vehicles')->where('is_active', 1)->orderBy('model', 'ASC')->get();
        $garageVehicleOptions = [];
        foreach ($vehicles as $vehicle) {
            $brandName = $brandById[(int) ($vehicle['brand_id'] ?? 0)] ?? 'Unknown';
            $garageVehicleOptions[] = [
                'id'       => (int) $vehicle['id'],
                'brand_id' => (int) ($vehicle['brand_id'] ?? 0),
                'brand'    => $brandName,
                'model'    => (string) ($vehicle['model'] ?? ''),
                'label'    => trim($brandName . ' ' . (string) ($vehicle['model'] ?? '')),
            ];
        }

        // Garage vehicles for logged-in users
        $garageVehicles = [];
        if (Auth::isLoggedIn()) {
            $customerId = (int) Auth::customerId();
            try {
                $rows = $db->table('customer_vehicles')
                    ->where('customer_id', $customerId)
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('id', 'ASC')
                    ->get();
                foreach ($rows as $row) {
                    $meta = null;
                    foreach ($garageVehicleOptions as $opt) {
                        if ((int) $opt['id'] === (int) ($row['vehicle_id'] ?? 0)) {
                            $meta = $opt;
                            break;
                        }
                    }
                    if ($meta === null) continue;
                    $garageVehicles[] = [
                        'id'           => (int) ($row['id'] ?? 0),
                        'vehicle_id'   => (int) ($row['vehicle_id'] ?? 0),
                        'vehicle_type' => (string) ($row['vehicle_type'] ?? 'scooter'),
                        'is_default'   => (int) ($row['is_default'] ?? 0) === 1,
                        'label'        => $meta['label'],
                        'brand'        => $meta['brand'],
                        'model'        => $meta['model'],
                    ];
                }
            } catch (\Throwable) {}
        }

        return $this->view('home.index', [
            'title'                => 'Home',
            'featured'             => Products::featured(6),
            'trending'             => Products::trending(6),
            'newArrivals'          => Products::newArrivals(6),
            'onSale'               => Products::onSale(),
            'engineParts'          => Products::byCategory('engine-components', 6),
            'exhaustParts'         => Products::byCategory('exhaust-systems', 6),
            'brakesParts'          => Products::byCategory('braking-systems', 6),
            'performanceParts'     => Products::byCategory('performance-tuning', 6),
            'wheelsParts'          => Products::byCategory('wheels-tires-hubs', 6),
            'categories'           => Products::categories(),
            'ads'                  => $this->activeAdsByPlacements(['home_find_setup', 'home_deals', 'home_newsletter']),
            'garageVehicleOptions' => $garageVehicleOptions,
            'garageVehicles'       => $garageVehicles,
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
            'departments' => $this->supportDepartments(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function support(): Response
    {
        $supportAds = $this->activeAdsByPlacements(['support_center']);

        if (!Auth::isLoggedIn()) {
            return $this->view('support.guest', [
                'title' => 'Support Center',
                'departments' => $this->supportDepartments(),
                'ads' => $supportAds,
            ]);
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();

        $recentTickets = $db->table('tickets')
            ->select('tickets.*', 'ticket_departments.name AS department_name')
            ->leftJoin('ticket_departments', 'tickets.department_id', '=', 'ticket_departments.id')
            ->where('tickets.customer_id', $customerId)
            ->orderBy('tickets.last_activity_at', 'DESC')
            ->limit(5)
            ->get();

        $openTickets = $db->table('tickets')
            ->where('customer_id', $customerId)
            ->whereRaw("status NOT IN ('resolved','closed')")
            ->count();

        return $this->view('support.member', [
            'title' => 'Support Center',
            'recentTickets' => $recentTickets,
            'openTickets' => $openTickets,
            'ads' => $supportAds,
        ]);
    }

    public function adClick(int $id): Response
    {
        $db = Database::getInstance();
        $ad = $db->table('marketing_ads')
            ->where('id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$ad) {
            return $this->redirect('/');
        }

        $targetUrl = $this->sanitizeRedirectUrl((string) ($ad['cta_url'] ?? '/'));

        $utm = [
            'utm_source' => (string) ($ad['utm_source'] ?? ''),
            'utm_medium' => (string) ($ad['utm_medium'] ?? ''),
            'utm_campaign' => (string) ($ad['utm_campaign'] ?? ''),
            'utm_term' => (string) ($ad['utm_term'] ?? ''),
            'utm_content' => (string) ($ad['utm_content'] ?? ''),
        ];
        $utm = array_filter($utm, static fn (string $value): bool => trim($value) !== '');

        if (!empty($utm)) {
            $targetUrl = $this->appendQueryParams($targetUrl, $utm);
        }

        try {
            $db->table('marketing_ads')->where('id', $id)->update([
                'clicks_count' => (int) ($ad['clicks_count'] ?? 0) + 1,
                'last_clicked_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->table('marketing_ad_clicks')->insert([
                'ad_id' => $id,
                'target_url' => substr($targetUrl, 0, 255),
                'referrer' => substr((string) $this->header('referer', ''), 0, 255),
                'user_agent' => substr((string) $this->header('user-agent', ''), 0, 255),
                'ip_address' => substr((string) ($this->request->ip() ?? ''), 0, 45),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable) {
            // best-effort tracking only
        }

        return $this->redirect($targetUrl);
    }

    private function supportDepartments(): array
    {
        $departments = [];

        try {
            $db = Database::getInstance();

            $departments = $db->table('ticket_departments')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get();

            $mailboxes = $db->table('ticket_mailboxes')
                ->where('is_active', 1)
                ->orderBy('id')
                ->get();

            $mailboxByDepartment = [];
            foreach ($mailboxes as $mailbox) {
                $departmentId = (int) ($mailbox['department_id'] ?? 0);
                if ($departmentId > 0 && !isset($mailboxByDepartment[$departmentId])) {
                    $mailboxByDepartment[$departmentId] = $mailbox;
                }
            }

            foreach ($departments as &$department) {
                $departmentId = (int) ($department['id'] ?? 0);
                $department['contact_email'] = $mailboxByDepartment[$departmentId]['email'] ?? null;
            }
            unset($department);
        } catch (Throwable) {
            $departments = [];
        }

        return $departments;
    }

    private function activeAdsByPlacements(array $placements): array
    {
        if (empty($placements)) {
            return [];
        }

        try {
            $now = date('Y-m-d H:i:s');
            $rows = Database::getInstance()->table('marketing_ads')
                ->where('is_active', 1)
                ->whereRaw('(starts_at IS NULL OR starts_at <= :now0)', [':now0' => $now])
                ->whereRaw('(ends_at IS NULL OR ends_at >= :now1)', [':now1' => $now])
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'DESC')
                ->get();

            $allowed = array_fill_keys($placements, true);
            $mapped = [];
            foreach ($rows as $row) {
                $placement = (string) ($row['placement'] ?? '');
                if ($placement === '' || !isset($allowed[$placement]) || isset($mapped[$placement])) {
                    continue;
                }
                $mapped[$placement] = $row;
            }

            return $mapped;
        } catch (Throwable) {
            return [];
        }
    }

    private function sanitizeRedirectUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '/';
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return '/';
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (in_array($scheme, ['http', 'https'], true)) {
            return $url;
        }

        return '/';
    }

    private function appendQueryParams(string $url, array $params): string
    {
        if (empty($params)) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }

        $query = [];
        if (!empty($parts['query'])) {
            parse_str((string) $parts['query'], $query);
        }

        $query = array_merge($query, $params);
        $queryString = http_build_query($query);

        $rebuilt = '';
        if (!empty($parts['scheme']) && !empty($parts['host'])) {
            $rebuilt .= $parts['scheme'] . '://' . $parts['host'];
            if (!empty($parts['port'])) {
                $rebuilt .= ':' . $parts['port'];
            }
        }

        $rebuilt .= $parts['path'] ?? '/';
        if ($queryString !== '') {
            $rebuilt .= '?' . $queryString;
        }
        if (!empty($parts['fragment'])) {
            $rebuilt .= '#' . $parts['fragment'];
        }

        return $rebuilt;
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