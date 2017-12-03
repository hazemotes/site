<?php
/**
 * Created by PhpStorm.
 * User: clay
 * Date: 8/8/17
 * Time: 3:37 PM
 */

namespace App\Http\Controllers;

use App\Movie;
use App\MovieUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;


class MovieController extends Controller
{

    //API key: 63b69e61fd51dccdc80fd5a4c3480c43

    public function index()
    {
        $users = MovieUser::inRandomOrder()->get();

        foreach ($users as $key => $user){
            $movies = Movie::where('movie_user_id', $user->id)->get()->count();

            if($movies < 1){
                $users->forget($key);
            }
        }

        $data['randos'] = $users;

        return view('projects.movies', $data);
    }

    public function register(Request $request)
    {

        $name = $request->input('name');

        $duplicates = MovieUser::where('name', $name)->get()->count();

        if ($duplicates > 0) {
            return response()->json(['status' => 'error', 'message' => 'A user with that name already exists']);
        } else {

            $key = $this->generateBarcodeNumber();

            $user = new MovieUser();
            $user->name = $name;
            $user->key = $key;
            $user->save();
            return response()->json(['status' => 'success', 'key' => $key]);

        }

    }

    public function login(Request $request){

        $name = $request->input('name');
        $key = $request->input('key');

        $user = MovieUser::where('name', $name)->where('key', $key)->first();



        if($user){
            return response()->json(['status' => 'success', 'id' => $user->id]);
        } else {
            return 'error';
        }
    }

    public function save(Request $request){

        $key = $request->input('key');
        $movies = $request->input('movies');
        $comments = $request->input('comments');
        $titles = $request->input('titles');

        $user = MovieUser::where('key', $key)->first();

        // delete old movies
        Movie::where('movie_user_id', $user->id)->delete();

        foreach ($movies as $movie_id){

            $m = new Movie();

            $m->api_id = $movie_id;
            $m->movie_user_id = $user->id;
            $m->comment = isset($comments[$movie_id]) ? $comments[$movie_id] : '';
            $m->title = isset($titles[$movie_id]) ? $titles[$movie_id] : '';

            $m->save();
        }

        return 'success';
    }

    public function getList(Request $request){

        $id = $request->id;

        $movies = Movie::where('movie_user_id', $id)->get();

        return $movies->toJson();

    }

    function generateBarcodeNumber()
    {
        $number = dechex(mt_rand(10000, 99999)); // better than rand()

        // call the same function if the barcode exists already
        if ($this->barcodeNumberExists($number)) {
            return $this->generateBarcodeNumber();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    function barcodeNumberExists($number)
    {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return MovieUser::where('key', $number)->exists();
    }
}