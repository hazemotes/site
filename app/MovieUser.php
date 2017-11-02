<?php
/**
 * Created by PhpStorm.
 * User: clay
 * Date: 8/8/17
 * Time: 3:53 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class MovieUser extends Model
{
    protected $table = 'movie_users';

    public function movies(){
//        return $this->hasMany('App\Movie', '')
    }
}