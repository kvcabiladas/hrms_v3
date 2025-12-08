@extends('layouts.hrms')

@section('title', 'Payslip Details')

@section('content')
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('personal.payroll') }}"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Payroll
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Payslip Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-t-xl p-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">PAYSLIP</h1>
                    <p class="text-green-100">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                    <p class="text-green-100 text-sm">{{ $payroll->employee->designation->name ?? 'N/A' }} •
                        {{ $payroll->employee->department->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-green-100 text-sm">Payment Date</p>
                    <p class="text-2xl font-bold">{{ $payroll->payment_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Payslip Body -->
        <div class="bg-white rounded-b-xl shadow-lg border border-gray-100 p-8">
            <!-- Period Covered -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Period Covered</h3>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $payroll->period_start->format('F d, Y') }} - {{ $payroll->period_end->format('F d, Y') }}
                </p>
            </div>

            <!-- Earnings Section -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Earnings
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Basic Salary</span>
                        <span class="font-bold text-gray-800">₱{{ number_format($payroll->basic_salary, 2) }}</span>
                    </div>
                    @if($payroll->allowances > 0)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Allowances</span>
                            <span class="font-bold text-gray-800">₱{{ number_format($payroll->allowances ?? 0, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center py-2 border-t border-gray-200 pt-3">
                        <span class="font-semibold text-gray-700">Gross Pay</span>
                        <span
                            class="font-bold text-lg text-gray-800">₱{{ number_format($payroll->basic_salary + ($payroll->allowances ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Deductions Section -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Deductions
                </h3>
                <div class="space-y-3">
                    @if($payroll->total_deductions > 0)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Total Deductions</span>
                            <span class="font-bold text-red-600">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm italic">No deductions</p>
                    @endif
                </div>
            </div>

            <!-- Net Pay -->
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold text-green-700 uppercase tracking-wider mb-1">Net Pay</p>
                        <p class="text-xs text-green-600">Amount to be received</p>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-bold text-green-700">₱{{ number_format($payroll->net_salary, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex gap-4">
                <a href="{{ route('personal.payslip.download', $payroll->id) }}"
                    class="flex-1 px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium text-center transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Payslip
                </a>
                <button onclick="window.print()"
                    class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-center transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Payslip
                </button>
            </div>

            <!-- Footer Note -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">This is a computer-generated payslip and does not require a signature.</p>
                <p class="text-xs text-gray-500 mt-1">For any queries, please contact the HR department.</p>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .max-w-4xl,
            .max-w-4xl * {
                visibility: visible;
            }

            .max-w-4xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            button,
            a[href*="download"] {
                display: none !important;
            }
        }
    </style>
@endsection