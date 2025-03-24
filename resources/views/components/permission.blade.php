@props(['permission' => null, 'resource' => null, 'action' => null])

@php
$canAccess = false;

// If user is not authenticated, they have no permissions
if (!auth()->check()) {
    $canAccess = false;
} else {
    $user = auth()->user();
    
    // Superusers bypass all permission checks
    if (method_exists($user, 'isSuperuser') && $user->isSuperuser()) {
        $canAccess = true;
    } else {
        // For normal users, check specific permissions
        $permissionService = app(\App\Services\PermissionService::class);
        
        if ($permission) {
            $canAccess = $permissionService->userCan($permission);
        } elseif ($resource && $action) {
            $canAccess = $permissionService->can($action, $resource);
        }
    }
}
@endphp

@if($canAccess)
    {{ $slot }}
@endif