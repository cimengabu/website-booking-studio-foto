<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = Portfolio::with('room');

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $portfolios = $query->latest()->paginate(12);
        $categories = ['wisuda', 'prewedding', 'produk', 'keluarga', 'self-photo'];

        return view('customer.portfolio.index', compact('portfolios', 'categories', 'category'));
    }
}
