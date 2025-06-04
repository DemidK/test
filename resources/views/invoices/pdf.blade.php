<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rēķins Nr. {{ $invoice->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Arial Unicode MS';
            /* Важно: убедитесь, что wkhtmltopdf имеет опцию --enable-local-file-access
               и что этот путь корректен в вашем окружении Laravel. */
            src: url("{{ $fontPath ?? storage_path('fonts/Arial-Unicode-MS.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Arial Unicode MS', Arial, sans-serif;
            line-height: 1.4; /* Немного уменьшил для компактности, как в примере */
            margin: 0;
            padding: 20px;
            color: #000000;
            font-size: 9pt; /* Базовый размер шрифта как в примере */
        }

        .container {
            width: 100%; /* Убрал max-width для полного использования страницы, как в примере */
            margin: 0 auto;
        }

        .invoice-title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt; /* Размер как в примере "FAKTŪRRĒĶINS" */
            margin-bottom: 5px;
        }

        .invoice-date-centered {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 20px;
        }

        .party-details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .party-details-table td {
            vertical-align: top;
            padding: 0 5px 5px 0; /* Уменьшил отступы */
            font-size: 9pt;
        }

        .party-label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        .detail-line {
            margin-bottom: 2px;
        }

        .align-right-data {
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px; /* Уменьшил отступ */
        }

        .items-table th, .items-table td {
            border: 1px solid #000000;
            padding: 4px; /* Уменьшил отступы */
            text-align: left;
            font-size: 8.5pt; /* Мельче для таблицы */
        }

        .items-table th {
            font-weight: bold;
            background-color: #f2f2f2; /* Легкий фон для заголовков, опционально */
        }

        .items-table td.nr { text-align: center; }
        .items-table td.pvn-percent { text-align: center; }
        .items-table td.unit { text-align: center; }
        .items-table td.quantity { text-align: right; }
        .items-table td.price { text-align: right; }
        .items-table td.sum { text-align: right; }

        .summary-table {
            width: 45%; /* Примерно как в образце */
            float: right;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 10px;
        }
        .summary-table td {
            padding: 3px 5px;
        }
        .summary-table td.label {
            text-align: right;
            font-weight: normal;
        }
        .summary-table td.value {
            text-align: right;
            font-weight: bold;
            border-bottom: 1px solid black; /* Линия только под значением */
        }
         .summary-table td.value.no-border {
            border-bottom: none;
        }
        .summary-table tr.total-row td.value {
             font-size: 10pt; /* KOPĀ EUR крупнее */
        }


        .footer-info {
            font-size: 9pt;
            margin-bottom: 20px;
        }
         .footer-info p {
            margin: 2px 0;
        }

        .signature-section {
            margin-top: 30px;
            font-size: 9pt;
        }
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-section td {
            padding-top: 5px;
        }
        .signature-line {
            border-bottom: 1px solid black;
            height: 25px;
            width: 200px; /* Ширина линии подписи */
            display: inline-block; /* Для правильного отображения */
            margin-left: 10px;
        }
        .zv-place {
            display: inline-block;
            margin-left: 50px; /* Отступ для Z.V. */
        }

        /* Для предотвращения разрыва страницы внутри секций, если возможно */
        .party-details-table, .items-table, .summary-section, .signature-section {
            page-break-inside: avoid;
        }

        /* Очистка float */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-title">FAKTŪRRĒĶINS Nr. {{ $invoice->invoice_number }}</div>
        <div class="invoice-date-centered">{{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('Y. \g\a\d\a d. F') }}</div>

        <table class="party-details-table">
            <tr>
                <td style="width: 50%;">
                    <span class="party-label">Nosūtītājs</span>
                    {{-- Эти данные, вероятно, нужно будет передавать в шаблон отдельно или из другой модели --}}
                    <div class="detail-line">{{ $sellerName ?? 'Juventus Auto, SIA' }} | {{ $sellerRegNo ?? '40103956049' }}</div>
                    <div class="detail-line">{{ $sellerAddress ?? 'Lokomotīves iela 52 - 39, Rīga, Latvija' }}</div>
                    <div class="detail-line">Tālr. {{ $sellerPhone ?? '+37129223031' }}</div>
                    <div class="detail-line">Izsniegšanas vieta: {{ $sellerBankName ?? 'SWEDBANK' }}</div>
                    <div class="detail-line">Norēķinu rekvizīti: {{ $sellerBankSwift ?? 'HABALV22' }}</div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <br>
                    <div class="detail-line">PVN: {{ $sellerVatNo ?? 'LV40103956049' }}</div>
                    <br><br><br> <div class="detail-line">konts: {{ $sellerBankAccount ?? 'LV89HABA0551041059970' }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 15px;">
                    <span class="party-label">Saņēmējs</span>
                    <div class="detail-line">{{ $invoice->partner_name }} | {{ $invoice->partner_reg_no ?? $invoice->partner_vat }}</div>
                    <div class="detail-line">{{ $invoice->partner_address }}</div>
                    {{-- Добавьте другие поля покупателя, если они есть и нужны --}}
                    {{-- <div class="detail-line">Tālr. {{ $invoice->partner_phone ?? '' }}</div> --}}
                    <div class="detail-line">Saņemšanas vieta: {{ $invoice->buyer_bank_name ?? 'UniCredit Bank AS' }}</div>
                    <div class="detail-line">Norēķinu rekvizīti: {{ $invoice->buyer_bank_swift ?? 'VBRILV2X' }}</div>
                </td>
                <td style="text-align: right; padding-top: 15px;">
                    <br>
                    <div class="detail-line">PVN: {{ $invoice->partner_vat }}</div>
                     <br><br> <div class="detail-line">konts: {{ $invoice->buyer_bank_account ?? 'LV53VBRI58105684LVLAC' }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%;" class="nr">Nr.</th>
                    <th style="width:40%;">Nosaukums</th>
                    <th style="width:10%;" class="pvn-percent">PVN</th>
                    <th style="width:10%;" class="unit">MV</th>
                    <th style="width:10%;" class="quantity">Daudzums</th>
                    <th style="width:12.5%;" class="price">Cena EUR</th>
                    <th style="width:12.5%;" class="sum">Summa EUR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $itemCounter = 1;
                    // Предполагаем, что $items уже содержит обработанные данные,
                    // включая itemSubtotal, itemVatAmount, itemTotal
                    // Если нет, то расчеты нужно будет добавить здесь или передать уже рассчитанные
                @endphp
                @foreach($items as $item)
                    <tr>
                        <td class="nr">{{ $itemCounter++ }}</td>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td class="pvn-percent">{{ number_format(floatval($item['vat'] ?? 0), 0) }}%</td>
                        <td class="unit">{{ $item['unit'] ?? 'gab.' }}</td> {{-- Добавьте поле 'unit' в ваши данные или используйте заполнитель --}}
                        <td class="quantity">{{ number_format(floatval($item['quantity'] ?? 0), 2) }}</td>
                        <td class="price">{{ number_format(floatval($item['price'] ?? 0), 2) }}</td>
                        <td class="sum">{{ number_format( (floatval($item['quantity'] ?? 0) * floatval($item['price'] ?? 0)) * (1 + (floatval($item['vat'] ?? 0)/100)), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section clearfix">
            <table class="summary-table">
                <tr>
                    <td class="label">SUMMA:</td>
                    <td class="value">{{ number_format($invoice->total_wo_vat ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">PVN {{ $invoice->main_vat_rate ?? 21 }}%:</td> {{-- Предполагаем, что есть основная ставка НДС --}}
                    <td class="value">{{ number_format($invoice->total_vat ?? 0, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">KOPĀ EUR:</td>
                    <td class="value">{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>


        <div class="footer-info">
            {{-- Предполагаем, что количество позиций можно получить из count($items) --}}
            <p>Kopā izsniegts {{ count($items) }} nosaukums</p>
            <p>Apmaksas veids: {{ $invoice->payment_method ?? 'pārskaitījums' }}</p>
            <p>Apmaksāt līdz: {{ isset($invoice->due_date) ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : (\Carbon\Carbon::parse($invoice->invoice_date ?? now())->addDays(10)->format('Y-m-d')) }}</p>
            <p style="font-weight: bold;">Pavisam apmaksai: {{-- Сюда нужно вставить сумму прописью на латышском --}}
                {{ $amountInWords ?? 'Šeit būs summa vārdiem latviski' }}
            </p>
        </div>


        <div class="signature-section">
            <table>
                <tr>
                    <td style="width: 15%;">Izsniedzējs</td>
                    <td style="width: 35%;">{{ $sellerSignatoryName ?? 'Leonīds Karelovs' }}</td>
                    <td style="width: 15%;">Saņēmis</td>
                    <td style="width: 35%;">{{-- Имя получателя, если нужно --}}</td>
                </tr>
                <tr>
                    <td>Datums</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('Y. \g\a\d\a d. F') }}</td>
                    <td>Datums</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Paraksts</td>
                    <td><span class="signature-line"></span><span class="zv-place">Z.V.</span></td>
                    <td>Paraksts</td>
                    <td><span class="signature-line"></span><span class="zv-place">Z.V.</span></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>