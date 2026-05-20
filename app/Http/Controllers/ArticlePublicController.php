<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlePublicController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(9);
        return view('articles.index', compact('articles'));
    }
}
