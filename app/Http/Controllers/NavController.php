<?php

namespace App\Http\Controllers;

use App\Models\NavLink;
use Illuminate\Http\Request;

class NavController extends Controller
{
    /**
     * Update the navigation order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        // Validate the request
        $request->validate([
            'order' => 'required|array',
        ]);
    
        // Get the order data
        $order = $request->input('order');
        
        // Update the position of each link in the database
        foreach ($order as $linkId => $position) {
            NavLink::where('id', $linkId)->update(['position' => $position]);
        }
    
        // Return a success response
        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }
}