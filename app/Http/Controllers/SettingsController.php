<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Fetch the first company record (assuming single tenant)
        $company = Company::first();
        return view('settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Company::first();
        
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        if ($company) {
            $company->update($validated);
        } else {
            Company::create($validated);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}