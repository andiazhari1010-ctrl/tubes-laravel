<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        // Pastikan model User diimport dengan benar
        return User::where('name', 'LIKE', "%{$query}%")
                   ->where('id', '!=', Auth::id())
                   ->get();
    }
}