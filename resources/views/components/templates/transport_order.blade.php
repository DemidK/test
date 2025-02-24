<style>
    body { font-family: sans-serif; }
    .header { text-align: center; margin-bottom: 20px; }
    .section { margin-bottom: 15px; }
    .section-title { font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; }
    .detail-row { display: flex; margin-bottom: 10px; }
    .label { width: 30%; font-weight: bold; }
    .value { width: 70%; }
    .full-width { width: 100%; }
</style>
<div class="header">
    <h1>Transportation Order #{{ $order->id }}</h1>
</div>
<div class="section">
    <div class="section-title">Carrier Details</div>
    <div class="detail-row">
        <div class="label">Carrier Name:</div>
        <div class="value">{{ $order->carrier_name ?? 'Not Specified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Registration Number:</div>
        <div class="value">{{ $order->reg_number ?? 'Not Provided' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Address:</div>
        <div class="value">{{ $order->address ?? 'No Address' }}</div>
    </div>
</div>
<div class="section">
    <div class="section-title">Vehicle Information</div>
    <div class="detail-row">
        <div class="label">Vehicle Number:</div>
        <div class="value">{{ $order->vehicle_number ?? 'Not Assigned' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Vehicle Type:</div>
        <div class="value">{{ $order->vehicle_type ?? 'Unspecified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Driver:</div>
        <div class="value">{{ $order->driver_name ?? 'Not Assigned' }}</div>
    </div>
</div>
<div class="section">
    <div class="section-title">Transport Details</div>
    <div class="detail-row">
        <div class="label">Loading Address:</div>
        <div class="value">{{ $order->load_address ?? 'Not Specified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Loading Date/Time:</div>
        <div class="value">{{ $loadDateTime }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Unloading Address:</div>
        <div class="value">{{ $order->unload_address ?? 'Not Specified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Unloading Date/Time:</div>
        <div class="value">{{ $unloadDateTime }}</div>
    </div>
</div>
<div class="section">
    <div class="section-title">Cargo Information</div>
    <div class="detail-row">
        <div class="label">Cargo Type:</div>
        <div class="value">{{ $order->cargo_type ?? 'Unspecified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Max Tonnage:</div>
        <div class="value">{{ $order->max_tonnage ?? 'Not Specified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Volume:</div>
        <div class="value">{{ $order->volume_m3 ? $order->volume_m3 . ' m³' : 'Not Specified' }}</div>
    </div>
</div>
<div class="section">
    <div class="section-title">Financial Details</div>
    <div class="detail-row">
        <div class="label">Freight Amount:</div>
        <div class="value">
            {{ $order->freight_amount ? number_format($order->freight_amount, 2) . ' ' . $order->currency : 'Not Specified' }}
        </div>
    </div>
    <div class="detail-row">
        <div class="label">VAT Status:</div>
        <div class="value">{{ $order->vat_status ?? 'Not Specified' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Payment Terms:</div>
        <div class="value">{{ $order->payment_term_days }} days</div>
    </div>
</div>
<div class="section">
    <div class="section-title">Additional Information</div>
    <div class="detail-row">
        <div class="label">Required Documents:</div>
        <div class="value">{{ $order->documents_required ?? 'No specific documents required' }}</div>
    </div>
    <div class="detail-row">
        <div class="label">Special Conditions:</div>
        <div class="value">{{ $order->special_conditions ?? 'No special conditions' }}</div>
    </div>
</div>