<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tax Invoice</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin: 20px;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .logo {
            height: 70px;
            display: block;
            margin: 0 auto 10px;
        }

        .title-row {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .company-address-line {
            border-bottom: 2px solid #ddd;
            border-top: 2px solid #ddd;
        }

        .meta-info-table {
            width: 100%;
            margin-bottom: 5px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        .label-red {
            color: #d9534f;
            font-weight: normal;
        }

        /* Customer Section */
        .section-title {
            background: #fff;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            margin: 15px 0 10px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .w-20 {
            width: 15%;
            font-weight: bold;
        }

        .text-red-addr {}

        /* Items Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        .description-cell {
            line-height: 1.6;
        }

        .bank-details {
            font-size: 13px;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
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
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .amount-in-words {
            text-align: right;
            margin-bottom: 15px;
            font-style: italic;
        }
    </style>
</head>
@php
    $sgst_tax = $payment->amount * ($settings['sgst_percentage'] / 100);
    $cgst_tax = $payment->amount * ($settings['cgst_percentage'] / 100);
    $amountBeforeTax = $payment->amount - ($sgst_tax + $cgst_tax);
    $totalFinalAmount = $payment->amount;

    // Amount in words logic (requires NumberFormatter or standard PHP)
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $amountInWords = ucwords($f->format($totalFinalAmount));
@endphp

<body>
    <div class="container">
        <div class="text-center">
            @if(isset($settings['logo']) && !empty($settings['logo']))
                <img src="{{ $settings['logo'] }}" alt="Logo" class="logo">
            @endif
        </div>

        <div class="title-row">Tax Invoice</div>
        <div class="company-address-line">
            <p class="text-center">{{ $settings['company_name'] ?? 'Company Name' }} | {{ $settings['company_address'] ?? 'Company Address' }}</p>
            @php
                // if company_tel1 is not null, then use it, otherwise use company_tel2
                $settings['company_tel'] = $settings['company_tel1'] ?? $settings['company_tel2'] ?? 'xxxxxxxxxxxxxxx';
            @endphp
            <p class="text-center">{{ $settings['company_email'] ?? 'support@example.com' }} | {{ $settings['company_tel'] }}</p>
        </div>

        <table class="meta-info-table">
            <tr>
                <td style="width: 50%;">
                    <strong>Invoice No #:</strong>
                    {{ $payment->invoice_no }}<br>
                    <strong>GST No:</strong> {{ $settings['gst_no'] ?? '' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Invoice Date:</strong> {{ $payment->created_at->format('d M Y') }}<br>
                    <span>Place:</span> <span style="font-weight: bold;">{{ $settings['receipt_place'] ?? 'Baglore, KR' }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Customer Information</div>
        <table class="info-table">
            <tr>
                <td class="w-20">Name:</td>
                <td>{{ $payment->customer->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="w-20">Email:</td>
                <td>{{ $payment->customer->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="w-20">Mobile:</td>
                <td>{{ $payment->customer->mobile ?? '-' }}</td>
            </tr>
            <tr>
                <td class="w-20">Address:</td>
                <td class="text-red-addr">{{ !empty($payment->customer->address) ? $payment->customer->address : '-' }}</td>
            </tr>
            @if(!empty($payment->customer->gst_number))
                <tr>
                    <td class="w-20">GST No.:</td>
                    <td>{{ $payment->customer->gst_number ?? '-' }}</td>
                </tr>
            @endif
            <tr>
                <td class="w-20">Payment Type:</td>
                <td>{{ ucfirst($payment->payment_type) }}</td>
            </tr>
        </table>

        <div style="font-weight: bold; margin-bottom: 10px;">Tax Invoice Details</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Package</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="description-cell">
                        <strong>{{ $payment->package->name }}</strong><br>
                        {{-- This ensures descriptions are on separate lines --}}
                        {!! nl2br(e($payment->package->description)) !!}
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($amountBeforeTax, 2) }}</td>
                    <td class="text-right">{{ number_format($amountBeforeTax, 2) }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="bold text-right">Total Net</td>
                    <td class="bold text-right">{{ number_format($amountBeforeTax, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($settings['have_tax_data'])
            <table class="table">
                <thead>
                    <tr>
                        <th>Tax Code</th>
                        <th>Net</th>
                        <th>Rate</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $settings['sgst_tax_code'] }}</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($settings['sgst_percentage'], 2) }}</td>
                        <td class="text-center">SGST</td>
                        <td class="text-right">{{ number_format($sgst_tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $settings['cgst_tax_code'] }}</td>
                        <td class="text-right">0</td>
                        <td class="text-right">{{ number_format($settings['cgst_percentage'], 2) }}</td>
                        <td class="text-center">CGST</td>
                        <td class="text-right">{{ number_format($cgst_tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="bold text-right">Total Gross (Indian Rupee)</td>
                        <td class="bold text-right">{{ number_format($totalFinalAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div class="amount-in-words">
            <strong>Amount in Words: </strong> {{ $amountInWords }} Only
        </div>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">
                    <h3>Payment Received</h3>
                </span>
            </div>
        </div>

        <div class="bank-details">
            A/C Details: {{ $settings['receipt_ac_details'] ?? 'CONFEDERATION OF REAL ESTATE ASSOCIATES INDIA' }},
            Current Bank A/C: {{ $settings['receipt_current_bank_ac'] ?? 'IDFC FIRST Bank:10133938666' }},
            Bank IFSC: {{ $settings['receipt_bank_ifsc'] ?? 'IDFB0081103' }}
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Receipt generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>

</html>