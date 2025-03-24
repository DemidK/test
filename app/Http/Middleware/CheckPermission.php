<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    protected $permissionService;
    
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // If this is a guest permission, allow access
        if ($permission === 'guest') {
            return $next($request);
        }
        
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page');
        }
        
        $user = Auth::user();
        
        // Superusers bypass all permission checks
        if (method_exists($user, 'isSuperuser') && $user->isSuperuser()) {
            return $next($request);
        }
        
        // For non-superusers, check if they have the required permission
        if (!$this->permissionService->userCan($permission)) {
            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }
            
            // For normal requests, redirect with error message
            return redirect()
                ->back()
                ->with('error', 'You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}