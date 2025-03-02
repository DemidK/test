@props(['customer', 'readonly' => false])

<div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
    <div class="md:col-span-1">
        <label for="partner_id" class="block text-gray-700 mb-2">Registracijas numurs</label>
        <input type="number" name="partner_id" id="partner_id" 
            class="w-full px-4 py-2 border rounded-lg"
            value="{{ old('partner_id', $customer->partner_id ?? '') }}"
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div class="md:col-span-1">
        <label for="partner_vat" class="block text-gray-700 mb-2">PVN numurs</label>
        <input type="text" name="partner_vat" id="partner_vat" 
            class="w-full px-4 py-2 border rounded-lg"
            value="{{ old('partner_vat', $customer->partner_vat ?? '') }}"
            {{ $readonly ? 'readonly' : '' }}>
    </div>
    <div class="md:col-span-1">
        <label for="partner_name" class="block text-gray-700 mb-2">Klienta nosaukums</label>
        <input type="text" name="partner_name" id="partner_name" 
            class="w-full px-4 py-2 border rounded-lg" 
            value="{{ old('partner_name', $customer->partner_name ?? '') }}" 
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div class="md:col-span-1">
        <label for="partner_email" class="block text-gray-700 mb-2">Klienta e-pasts</label>
        <input type="email" name="partner_email" id="partner_email" 
            class="w-full px-4 py-2 border rounded-lg" 
            value="{{ old('partner_email', $customer->partner_email ?? '') }}" 
            {{ $readonly ? 'readonly' : '' }}>
    </div>
    <div class="md:col-span-1">
        <label for="partner_address" class="block text-gray-700 mb-2">Juridisko adrese</label>
        <textarea name="partner_address" id="partner_address" 
                class="w-full px-4 py-2 border rounded-lg" 
                rows="3" 
                {{ $readonly ? 'readonly' : '' }}>{{ old('partner_address', $customer->partner_address ?? '') }}</textarea>
    </div>
    <div class="md:col-span-1">
        <label for="partner_post_address" class="block text-gray-700 mb-2">Pasta adrese (if different)</label>
        <textarea name="partner_post_address" id="partner_post_address" 
                class="w-full px-4 py-2 border rounded-lg" 
                rows="3" 
                {{ $readonly ? 'readonly' : '' }}>{{ old('partner_post_address', $customer->partner_post_address ?? '') }}</textarea>
    </div>
</div>