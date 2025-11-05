<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    /**
     * Display a listing of documents (all purchase requests).
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['user', 'vendor'])
            ->latest();

        // Search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('request_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                      $vendorQuery->where('vendor_name', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }
}

