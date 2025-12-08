<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            background: #10b981;
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin: -20px -20px 20px -20px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #10b981;
        }

        .company-info h2 {
            margin: 0 0 5px 0;
            color: #10b981;
            font-size: 18px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background: #f3f4f6;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 13px;
            color: #374151;
            margin-bottom: 10px;
            border-left: 4px solid #10b981;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 6px 10px;
            font-weight: bold;
            color: #666;
            width: 40%;
            font-size: 11px;
        }

        .info-value {
            display: table-cell;
            padding: 6px 10px;
            color: #333;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th {
            background: #f9fafb;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .amount {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 14px;
        }

        .total-row td {
            padding: 15px 10px;
            border-top: 2px solid #10b981;
            border-bottom: 2px solid #10b981;
        }

        .net-pay {
            background: #10b981;
            color: white;
            font-size: 16px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PAYSLIP</h1>
        <p>{{ $payroll->month_year }}</p>
    </div>

    <div class="company-info">
        <h2>{{ config('app.name', 'HRMS Company') }}</h2>
        <p>Human Resource Management System</p>
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Employee Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Employee Name:</div>
                <div class="info-value">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Employee ID:</div>
                <div class="info-value">{{ $payroll->employee->employee_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value">{{ $payroll->employee->department->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Position:</div>
                <div class="info-value">{{ $payroll->employee->designation->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Pay Period:</div>
                <div class="info-value">{{ $payroll->month_year }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">{{ $payroll->created_at->format('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Earnings</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="amount">{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                @if(isset($payroll->allowances) && $payroll->allowances > 0)
                    <tr>
                        <td>Allowances</td>
                        <td class="amount">{{ number_format($payroll->allowances, 2) }}</td>
                    </tr>
                @endif
                @if(isset($payroll->overtime_pay) && $payroll->overtime_pay > 0)
                    <tr>
                        <td>Overtime Pay</td>
                        <td class="amount">{{ number_format($payroll->overtime_pay, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Gross Pay</td>
                    <td class="amount">
                        {{ number_format($payroll->basic_salary + ($payroll->allowances ?? 0) + ($payroll->overtime_pay ?? 0), 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Deductions</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($payroll->tax) && $payroll->tax > 0)
                    <tr>
                        <td>Tax</td>
                        <td class="amount">{{ number_format($payroll->tax, 2) }}</td>
                    </tr>
                @endif
                @if(isset($payroll->sss) && $payroll->sss > 0)
                    <tr>
                        <td>SSS</td>
                        <td class="amount">{{ number_format($payroll->sss, 2) }}</td>
                    </tr>
                @endif
                @if(isset($payroll->philhealth) && $payroll->philhealth > 0)
                    <tr>
                        <td>PhilHealth</td>
                        <td class="amount">{{ number_format($payroll->philhealth, 2) }}</td>
                    </tr>
                @endif
                @if(isset($payroll->pagibig) && $payroll->pagibig > 0)
                    <tr>
                        <td>Pag-IBIG</td>
                        <td class="amount">{{ number_format($payroll->pagibig, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total Deductions</td>
                    <td class="amount">{{ number_format($payroll->total_deductions ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table>
        <tr class="net-pay">
            <td style="font-size: 18px; padding: 20px 10px;">NET PAY</td>
            <td class="amount" style="font-size: 20px; padding: 20px 10px;">₱
                {{ number_format($payroll->net_salary, 2) }}</td>
        </tr>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Employee Signature
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Authorized Signature
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated payslip and does not require a signature.</p>
        <p>For any queries, please contact the HR Department.</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'HRMS Company') }}. All rights reserved.</p>
    </div>
</body>

</html>