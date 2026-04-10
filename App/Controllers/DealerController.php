<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class DealerController extends BaseStorefrontController
{
    /**
     * Handle dealer application form submission from the dealer-onboarding page.
     */
    public function apply(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token. Please try again.');
            return $this->redirect('/dealer-onboarding#apply');
        }

        $v = Validator::make([
            'company_name'  => $this->input('company_name', ''),
            'contact_name'  => $this->input('contact_name', ''),
            'email'         => $this->input('email', ''),
            'phone'         => $this->input('phone', ''),
            'website'       => $this->input('website', ''),
            'country'       => $this->input('country', ''),
            'business_type' => $this->input('business_type', ''),
            'vat_number'    => $this->input('vat_number', ''),
            'annual_volume' => $this->input('annual_volume', ''),
            'message'       => $this->input('message', ''),
        ], [
            'company_name'  => 'required|max:191',
            'contact_name'  => 'required|max:191',
            'email'         => 'required|email',
            'phone'         => 'max:50',
            'website'       => 'max:255',
            'country'       => 'max:100',
            'vat_number'    => 'max:100',
            'annual_volume' => 'max:100',
            'message'       => 'max:3000',
        ]);

        if ($v->fails()) {
            $allErrors = array_merge(...array_values($v->errors()));
            Session::flash('error', implode(' ', $allErrors));
            return $this->redirect('/dealer-onboarding#apply');
        }

        $businessType = (string) $this->input('business_type', 'other');
        if (!in_array($businessType, ['retailer', 'webshop', 'workshop', 'distributor', 'other'], true)) {
            $businessType = 'other';
        }

        $db = Database::getInstance();

        $existing = $db->table('dealer_applications')
            ->where('email', strtolower(trim((string) $this->input('email', ''))))
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            Session::flash('error', 'An application for this email address already exists or has been approved.');
            return $this->redirect('/dealer-onboarding#apply');
        }

        $db->table('dealer_applications')->insert([
            'company_name'  => trim((string) $this->input('company_name', '')),
            'contact_name'  => trim((string) $this->input('contact_name', '')),
            'email'         => strtolower(trim((string) $this->input('email', ''))),
            'phone'         => trim((string) $this->input('phone', '')) ?: null,
            'website'       => trim((string) $this->input('website', '')) ?: null,
            'country'       => trim((string) $this->input('country', '')) ?: null,
            'business_type' => $businessType,
            'vat_number'    => trim((string) $this->input('vat_number', '')) ?: null,
            'annual_volume' => trim((string) $this->input('annual_volume', '')) ?: null,
            'message'       => trim((string) $this->input('message', '')) ?: null,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Your dealer application has been submitted! Our B2B team will review it and get back to you within 2–3 business days.');
        return $this->redirect('/dealer-onboarding#apply');
    }
}
