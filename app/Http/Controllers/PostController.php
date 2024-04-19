<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Post;     //posts table
use App\Models\Category; //categories table


class PostController extends Controller
{
    #Define properties and constructor
    private $post;
    private $category;

    # Constructor
    public function __construct(Post $post, Category $category){
        $this->post = $post;
        $this->category = $category;
    }

    public function create(){
        $all_categories = $this->category->all(); //retrieve all the categories
        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    public function store(Request $request){
        $request->validate([
            'category' => 'required|array|between:1,3',
            'description'=> 'required|min:1|max:1000',
            'image'=> 'required|mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        $this->post->user_id = Auth::user()->id;
        $this->post->image = 'data:image/' . $request->image->extension(). ';base64,'.
        base64_encode(file_get_contents($request->image));
        $this->post->description     =$request->description;
        $this->post->save();

        foreach ($request->category as $category_id){
             $category_post[] = ['category_id' => $category_id];
        }
        $this->post->categoryPost()->createMany($category_post);

        return redirect()->route('index');
    }

        # This will open show post page
    # The $id ---> is the specific id of the post that we want to see
    # We will received that ID coming the route later on
    public function show($id){
        $post = $this->post->findOrFail($id);
        #Same as: select * from posts where id = $id;

        return view('users.posts.show')->with('post', $post);
    }

    public function edit($id){
        $post = $this->post->findOrFail($id);
        #SAME AS: SELECT * FROM  posts WHERE id = $id;

        # If the AUTH user is not the owner of the post, redirect them to the homepage
        if (Auth::user()->id != $post->user->id) {
            return redirect()->route('index'); //hompepage
        }

        # get all the categories from the categories table
        $all_categories =  $this->category->all();

        # Get all the category IDs of this post, and save it in an array
        $selected_categories = [];
        foreach ($post->categoryPost as $category_post) {
            $selected_categories[] = $category_post->category_id;
        }

        return view('users.posts.edit')
            ->with('post', $post)
            ->with('all_categories', $all_categories)
            ->with('selected_categories', $selected_categories);
    }

    public function update(Request $request, $id){
        $request->validate(
            [
                'category'=> 'required|array|between:1,3',
                'description'=> 'required|min:1|max:1000',
                'imge'=> 'mimes:jpeg,jpg,png,gif|max:1048'
            ]);

        $post = $this->post->findOrFail($id);
        $post->description = $request->description;

        if ($request->image){
            $post->image = 'data:image/' .  $request->image->extension(). ';base64,'.
            base64_encode(file_get_contents($request->image));
        }
        $post->save();

        $post->categoryPost()->delete();
        # Ues the relationship Post::categoryPost() to select the records...

        #4 Save
        foreach ($request->category as $category_id) {
            $category_post[]=['category_id' =>$category_id];
        }
        $post->categoryPost()->createMany($category_post);

        return redirect()->route('post.show',$id);
    }

    public function destroy($id){
        $post = $this->post->findOrFail($id);
        $post->forceDelete();

        return redirect()->route('index');
    }
    public function followers($id)
    {
        $user = $this->user->findorFail($id);
        return view('users.profile.followers')->with('user', $user);

    }

}




