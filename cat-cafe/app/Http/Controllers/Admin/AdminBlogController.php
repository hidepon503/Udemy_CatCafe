<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Models\Blog;
use App\Http\Requests\Admin\UpdateBlogRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Cat;
use Illuminate\Support\Facades\Auth;

class AdminBlogController extends Controller
{
    // ブログ一覧画面   
    public function index()
    {
        $blogs = Blog::latest('updated_at')->simplePaginate(10);
        return view('admin.blogs.index',['blogs' => $blogs]);
    }

    // ブログ投稿画面
    public function create()
    {
        
        return view('admin.blogs.create');
    }

    // ブログ投稿処理
    public function store(StoreBlogRequest $request)
    {
        $saveImagePath = $request->file('image')->store('blogs','public');
        $blog =new Blog($request->validated());
        $blog->image = $saveImagePath;
        $blog->save();
        return to_route('admin.blogs.index')->with('success', 'ブログを投稿しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // ブログ編集画面
    public function edit(Blog $blog)
    {
        $user = Auth::user();
        $categories = Category::all();
        $cats = Cat::all();
        return view('admin.blogs.edit',['blog' => $blog, 'categories' => $categories, 'cats' => $cats,'user' => $user]);
    }

    // ブログ更新処理
    public function update(UpdateBlogRequest $request, string $id)
    {
        $blog = Blog::findOrFail($id);
        $updateData = $request->validated();
        
        // 画像を変更する場合
        if($request->has('image')){
            // Storageー＞publicディレクトリ内の古い画像を削除する
            Storage::disk ('public')->delete($blog->image);
            // 変更した画像を保存し、そのパスを$updatedDataに追加する
            $updateData['image'] = $request->file('image')->store('blogs','public');
        }
        $blog->category()->associate($updateData['category_id']);
        $blog->cats()->sync($updateData['cats']??[]);
        $blog->update($updateData);

        return redirect()->route('admin.blogs.index')->with('success', 'ブログを更新しました');
    }

    // 指定したIDの削除処理
    public function destroy(string $id)
    {
        // 指定したIDのブログ記事を取得する
        $blog = Blog::FindOrFail($id);
        // 指定したIDのブログ記事を削除する
        $blog->delete();
        // 削除したIDの画像も削除する
        Storage::disk('public')->delete($blog->image);

        // ブログ一覧画面にリダイレクトし、「ブログを削除しました」と完了メッセージを表示させる
        return to_route('admin.blogs.index')->with('success', 'ブログを削除しました');
    }
}
