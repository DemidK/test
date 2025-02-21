@props(['customer', 'readonly' => false])

<div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
    <div class="md:col-span-1">
        <label for="customer_id" class="block text-gray-700 mb-2">Klienta ID</label>
        <input type="number" name="customer_id" id="customer_id" 
            class="w-full px-4 py-2 border rounded-lg"
            value="{{ old('customer_id', $customer->customer_id ?? '') }}"
            {{ $readonly ? 'readonly' : '' }}>
    </div>
    <div class="md:col-span-1">
        <label for="customer_vat" class="block text-gray-700 mb-2">Customer VAT Number</label>
        <input type="text" name="customer_vat" id="customer_vat" 
            class="w-full px-4 py-2 border rounded-lg"
            value="{{ old('customer_vat', $customer->customer_vat ?? '') }}"
            {{ $readonly ? 'readonly' : '' }}>
    </div>
    <div class="md:col-span-1">
        <label for="customer_name" class="block text-gray-700 mb-2">Klienta vārds / nosaukums</label>
        <input type="text" name="customer_name" id="customer_name" 
            class="w-full px-4 py-2 border rounded-lg" 
            value="{{ old('customer_name', $customer->customer_name ?? '') }}" 
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div class="md:col-span-1">
        <label for="customer_email" class="block text-gray-700 mb-2">Klienta e-pasts</label>
        <input type="email" name="customer_email" id="customer_email" 
            class="w-full px-4 py-2 border rounded-lg" 
            value="{{ old('customer_email', $customer->customer_email ?? '') }}" 
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div class="md:col-span-1">
        <label for="customer_address" class="block text-gray-700 mb-2">Juridisko adrese</label>
        <textarea name="customer_address" id="customer_address" 
                class="w-full px-4 py-2 border rounded-lg" 
                rows="3" 
                {{ $readonly ? 'readonly' : 'required' }}>{{ old('customer_address', $customer->customer_address ?? '') }}</textarea>
    </div>
    <div class="md:col-span-1">
        <label for="customer_post_address" class="block text-gray-700 mb-2">Pasta adrese (if different)</label>
        <textarea name="customer_post_address" id="customer_post_address" 
                class="w-full px-4 py-2 border rounded-lg" 
                rows="3" 
                {{ $readonly ? 'readonly' : '' }}>{{ old('customer_post_address', $customer->customer_post_address ?? '') }}</textarea>
    </div>
</div>