<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Payment Receipt</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }

        .logo {
            height: 80px;
            max-width: 200px;
            margin: 0 auto 10px;
            display: block;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-number {
            font-size: 16px;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section h3 {
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-row {
            margin-bottom: 5px;
            overflow: hidden;
        }

        .info-row .label {
            font-weight: bold;
            float: left;
            width: 40%;
        }

        .info-row .value {
            float: left;
            width: 60%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f8f8f8;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-row {
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(isset($settings['logo']) && !empty($settings['logo']))
                <img src="{{ $settings['logo'] }}" alt="Company Logo" class="logo">
            @endif
            <div class="receipt-title">Tax Invoice</div>
            <div class="receipt-number">Invoice No #: {{ $payment->invoice_no }}</div>
        </div>

        <div class="info-section">
            <h3>Customer Information</h3>
            <div class="info-row clearfix">
                <span class="label">Name:</span>
                <span class="value">{{ $payment->customer->name }}</span>
            </div>
            <div class="info-row clearfix">
                <span class="label">Email:</span>
                <span class="value">{{ $payment->customer->email }}</span>
            </div>
            <div class="info-row clearfix">
                <span class="label">Mobile:</span>
                <span class="value">{{ $payment->customer->mobile }}</span>
            </div>
            <div class="info-row clearfix">
                <span class="label">Payment Type:</span>
                <span class="value">{{ ucfirst($payment->payment_type) }}</span>
            </div>
        </div>

        @if($settings['have_tax_data'])
            <div class="info-section">
                <h3>Tax Invoice</h3>
                <div class="info-row clearfix">
                    <span class="label">Invoice Date:</span>
                    <span class="value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="info-row clearfix">
                    <span class="label">GST No:</span>
                    <span class="value">{{ $settings['gst_no'] }}</span>
                </div>
                {{-- @if($payment->payment_type == 'online payment')
                <div class="info-row clearfix">
                    <span class="label">Payment Gateway:</span>
                    <span class="value">{{ ucfirst($payment->payment_gateway) }}</span>
                </div>
                @endif --}}
            </div>
        @endif

        {{-- Package Details --}}
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center;">Item</th>
                    <th style="text-align: center;">Package</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: center;">Price</th>
                    <th style="text-align: center;">Net</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $payment->package->name}}<br>
                        {{ $payment->package->description }}
                    </td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">Total Net</td>
                    <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($settings['have_tax_data'])
            {{-- Tax Details --}}
            <table class="table">
                <thead>
                    <tr>
                        <th style="text-align: center;">Tax Code</th>
                        <th style="text-align: center;">Net</th>
                        <th style="text-align: center;">Rate</th>
                        <th style="text-align: center;">Description</th>
                        <th style="text-align: center;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sgst_tax = $payment->amount * ($settings['sgst_percentage'] / 100);
                        $cgst_tax = $payment->amount * ($settings['cgst_percentage'] / 100);
                        $totalFinalAmount = $payment->amount + $sgst_tax + $cgst_tax;
                    @endphp
                    <tr>
                        {{-- SGST --}}
                        <td>{{ $settings['sgst_tax_code']}}</td>
                        <td style="text-align: right;">0</td>
                        <td style="text-align: right;">{{ number_format($settings['sgst_percentage'], 2) }}</td>
                        <td style="text-align: right;">SGST</td>
                        <td style="text-align: right;">{{ number_format($payment->amount * ($settings['sgst_percentage'] / 100), 2) }}</td>
                    </tr>
                    <tr>
                        {{-- CGST --}}
                        <td>{{ $settings['cgst_tax_code']}}</td>
                        <td style="text-align: right;">0</td>
                        <td style="text-align: right;">{{ number_format($settings['cgst_percentage'], 2) }}</td>
                        <td style="text-align: right;">CGST</td>
                        <td style="text-align: right;">{{ number_format($payment->amount * ($settings['cgst_percentage'] / 100), 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;">Total Gross (Indian Rupee)</td>
                        <td style="text-align: right;">{{ number_format($totalFinalAmount, 2) }}</td>
                    </tr>

                </tbody>
            </table>
        @endif

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">
                    <h3>Payment Received</h3>
                </span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>{{ $settings['company_name'] ?? 'Company Name' }} | {{ $settings['company_address'] ?? 'Company Address' }}</p>
            @php
                // if company_tel1 is not null, then use it, otherwise use company_tel2
                $settings['company_tel'] = $settings['company_tel1'] ?? $settings['company_tel2'] ?? 'xxxxxxxxxxxxxxx';
            @endphp
            <p>{{ $settings['company_email'] ?? 'support@example.com' }} | {{ $settings['company_tel'] }}</p>
            <p>Receipt generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>

</html>