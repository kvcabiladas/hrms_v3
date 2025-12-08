<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employee = $user->employee;
        $company = Company::first();

        // Get emergency contacts from employee's JSON field or default empty array
        $emergencyContacts = $employee && $employee->emergency_contacts
            ? json_decode($employee->emergency_contacts, true)
            : [['name' => '', 'relationship' => '', 'phone' => '']];

        return view('settings.index', compact('user', 'employee', 'company', 'emergencyContacts'));
    }

    // 1. Update Company Info (Super Admin Only)
    public function updateCompany(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $company = Company::first();
        if ($company) {
            $company->update($validated);
        } else {
            Company::create($validated);
        }

        return back()->with('success', 'Company settings updated.');
    }

    // 2. Update Personal Profile (All Users)
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employee = $user->employee;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        // Update User Table (Login Info)
        // The red line should be gone now because of the @var comment above
        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        // Update Employee Table (Personal Info)
        if ($employee) {
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    // 3. Change Password (All Users)
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Update Password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully. Secure mode active.');
    }

    // 4. Update Emergency Contacts
    public function updateEmergencyContacts(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }

        $request->validate([
            'contacts' => 'required|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.relationship' => 'required|string|max:100',
            'contacts.*.phone' => 'required|string|max:20',
        ]);

        // Store as JSON in the employee table
        $employee->update([
            'emergency_contacts' => json_encode($request->contacts),
        ]);

        return back()->with('success', 'Emergency contacts updated successfully.');
    }
}