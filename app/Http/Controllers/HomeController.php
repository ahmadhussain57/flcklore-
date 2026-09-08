<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // لاحقًا: آخر محتوى، آخر منتجات، الأكثر تعليقاتًا، الأكثر إعجابًا
        return view('home', [
            'latestContents' => collect(),
            'latestProducts' => collect(),
            'mostCommented' => collect(),
            'mostLiked' => collect(),
        ]);
    }
}