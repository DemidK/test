<!-- components/invoice-actions.blade.php -->
@props(['invoice', 'variant' => 'icon'])

@php
$actions = [
    [
        'id' => 'preview',
        'route' => route('invoices.previewPdf', $invoice->id),
        'label' => 'Priekšskatīt PDF',
        'target' => '_blank',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        'buttonColor' => 'blue',
        'iconColor' => 'green'
    ],
    [
        'id' => 'download',
        'route' => route('invoices.exportPdf', $invoice->id),
        'label' => 'Lejupielādēt PDF',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'buttonColor' => 'green',
        'iconColor' => 'purple'
    ],
    [
        'id' => 'edit',
        'route' => route('invoices.edit', $invoice->id),
        'label' => 'Edit',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        'buttonColor' => 'yellow',
        'iconColor' => 'yellow'
    ]
];
@endphp

@if($variant === 'button')
    <div class="flex justify-end gap-4 mb-6">
        @foreach($actions as $action)
            <a href="{{ $action['route'] }}" 
               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif
               class="bg-{{ $action['buttonColor'] }}-600 text-white px-4 py-2 rounded-lg hover:bg-{{ $action['buttonColor'] }}-700 inline-flex items-center gap-2 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $action['icon'] !!}
                </svg>
                {{ $action['label'] }}
            </a>
        @endforeach
    </div>
@else
    <div class="flex gap-2 items-center">
        @foreach($actions as $action)
            <a href="{{ $action['route'] }}" 
               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif
               class="text-{{ $action['iconColor'] }}-600 hover:text-{{ $action['iconColor'] }}-900 transition-colors duration-200"
               title="{{ $action['label'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $action['icon'] !!}
                </svg>
            </a>
        @endforeach

        <form action="{{ route('invoices.destroy', $invoice) }}" 
              method="POST" 
              class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="text-red-600 hover:text-red-800 transition-colors duration-200"
                    onclick="return confirm('Are you sure you want to delete this invoice?')"
                    title="Delete">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </form>
    </div>
@endif