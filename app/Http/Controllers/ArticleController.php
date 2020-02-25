<?php

namespace App\Http\Controllers;

use App\Article;
use App\Enums\Parts;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $articles = Article::orderBy('position', 'DESC')
            ->paginate(15);


        return view('admin.articles.index', compact('articles'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $parts = Parts::getInstances();

        return view('admin.articles.create', compact("parts"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required',
            'part' => 'required',
            'is_visible' => 'required',
            'position' => 'required',
        ]);

        $article = Article::create([
            'url' => $request->get('url') ?? '',
            'part' => Parts::getInstance(intval($request->get('part')))->value ?? 0,
            'is_visible' => $request->get('is_visible') ?? 0,
            'position' => $request->get('position') ?? 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $article = Article::find($id);

        $parts = Parts::getInstances();

        return view('admin.articles.show', compact('article', 'parts'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = Article::find($id);

        $parts = Parts::getInstances();

        return view('admin.articles.edit', compact('article', 'parts'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'url' => 'required',
            'part' => 'required',
            'is_visible' => 'required',
            'position' => 'required',
        ]);

        $article = Article::find($id);
        $article->url = $request->get("url") ?? '';
        $article->part = Parts::getInstance(intval($request->get('part')))->value ?? 0;
        $article->is_visible = $request->get('is_visible') ?? 0;
        $article->position = $request->get('position') ?? 0;
        $article->save();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $article = Article::find($id);
        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья успешно удалено');
    }
}
