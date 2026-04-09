<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class VehicleController extends BaseAdminController
{
    /**
     * List all vehicles with brand info.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;

        $search  = (string) $this->request->query('search');
        $brandId = $this->request->query('brand_id');

        $query = $db->table('vehicles')
            ->select('vehicles.*', 'brands.name as brand_name')
            ->leftJoin('brands', 'vehicles.brand_id', '=', 'brands.id')
            ->orderBy('brands.name', 'ASC')
            ->orderBy('vehicles.model', 'ASC');

        if ($search !== '') {
            $query->whereRaw(
                "(vehicles.model LIKE :search_0 OR brands.name LIKE :search_1)",
                [':search_0' => "%{$search}%", ':search_1' => "%{$search}%"]
            );
        }

        if ($brandId) {
            $query->where('vehicles.brand_id', (int) $brandId);
        }

        $vehicles = $query->paginate($perPage, $page);

        // Add product count per vehicle
        foreach ($vehicles['data'] as &$vehicle) {
            $vehicle['product_count'] = $db->table('product_vehicles')
                ->where('vehicle_id', $vehicle['id'])
                ->count();
        }
        unset($vehicle);

        $brands = $db->table('brands')->orderBy('name', 'ASC')->get();

        return $this->adminView('admin/vehicles/index', [
            'title'    => 'Vehicles',
            'vehicles' => $vehicles,
            'brands'   => $brands,
            'search'   => $search,
            'brandId'  => $brandId,
        ]);
    }

    /**
     * Show vehicle creation form.
     */
    public function create(): Response
    {
        $db = Database::getInstance();
        $brands = $db->table('brands')->orderBy('name', 'ASC')->get();

        return $this->adminView('admin/vehicles/create', [
            'title'  => 'Create Vehicle',
            'brands' => $brands,
        ]);
    }

    /**
     * Store a new vehicle.
     */
    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/vehicles/create');
        }

        $data = [
            'brand_id'  => (string) $this->input('brand_id', ''),
            'model'     => (string) $this->input('model', ''),
            'year_from' => (string) $this->input('year_from', ''),
            'year_to'   => $this->input('year_to'),
            'engine_cc' => (string) $this->input('engine_cc', ''),
            'slug'      => (string) $this->input('slug', ''),
            'is_active' => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'brand_id'  => 'required|integer',
            'model'     => 'required|max:255',
            'year_from' => 'required|integer',
            'engine_cc' => 'required|integer',
            'slug'      => 'required|slug|unique:vehicles,slug',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/vehicles/create');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $vehicleId = $db->table('vehicles')->insert([
            'brand_id'   => (int) $data['brand_id'],
            'model'      => $data['model'],
            'year_from'  => (int) $data['year_from'],
            'year_to'    => $data['year_to'] ? (int) $data['year_to'] : null,
            'engine_cc'  => (int) $data['engine_cc'],
            'slug'       => $data['slug'],
            'is_active'  => (int) $data['is_active'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->logActivity('create', 'vehicle', $vehicleId, null, $data);
        Session::flash('success', 'Vehicle created successfully.');
        return $this->redirect('/admin/vehicles');
    }

    /**
     * Show vehicle edit form.
     */
    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $vehicle = $db->table('vehicles')->where('id', $id)->first();
        if (!$vehicle) {
            Session::flash('error', 'Vehicle not found.');
            return $this->redirect('/admin/vehicles');
        }

        $brands = $db->table('brands')->orderBy('name', 'ASC')->get();

        return $this->adminView('admin/vehicles/edit', [
            'title'   => 'Edit Vehicle',
            'vehicle' => $vehicle,
            'brands'  => $brands,
        ]);
    }

    /**
     * Update an existing vehicle.
     */
    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/vehicles/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $vehicle = $db->table('vehicles')->where('id', $id)->first();
        if (!$vehicle) {
            Session::flash('error', 'Vehicle not found.');
            return $this->redirect('/admin/vehicles');
        }

        $data = [
            'brand_id'  => (string) $this->input('brand_id', ''),
            'model'     => (string) $this->input('model', ''),
            'year_from' => (string) $this->input('year_from', ''),
            'year_to'   => $this->input('year_to'),
            'engine_cc' => (string) $this->input('engine_cc', ''),
            'slug'      => (string) $this->input('slug', ''),
            'is_active' => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'brand_id'  => 'required|integer',
            'model'     => 'required|max:255',
            'year_from' => 'required|integer',
            'engine_cc' => 'required|integer',
            'slug'      => 'required|slug|unique:vehicles,slug,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/vehicles/' . $id . '/edit');
        }

        $db->table('vehicles')->where('id', $id)->update([
            'brand_id'   => (int) $data['brand_id'],
            'model'      => $data['model'],
            'year_from'  => (int) $data['year_from'],
            'year_to'    => $data['year_to'] ? (int) $data['year_to'] : null,
            'engine_cc'  => (int) $data['engine_cc'],
            'slug'       => $data['slug'],
            'is_active'  => (int) $data['is_active'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'vehicle', $id, $vehicle, $data);
        Session::flash('success', 'Vehicle updated successfully.');
        return $this->redirect('/admin/vehicles/' . $id . '/edit');
    }

    /**
     * Delete a vehicle.
     */
    public function delete(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/vehicles');
        }

        $db = Database::getInstance();

        $vehicle = $db->table('vehicles')->where('id', $id)->first();
        if (!$vehicle) {
            Session::flash('error', 'Vehicle not found.');
            return $this->redirect('/admin/vehicles');
        }

        $db->beginTransaction();

        try {
            $db->table('product_vehicles')->where('vehicle_id', $id)->delete();
            $db->table('vehicles')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'vehicle', $id, $vehicle);
            Session::flash('success', 'Vehicle deleted successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }

        return $this->redirect('/admin/vehicles');
    }
}
