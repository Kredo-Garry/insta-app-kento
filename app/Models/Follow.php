<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id')->withTrashed();
    }
        # Follows table                                 Users table
    # follower_id    following_id                   id        name
    #    1                2                         1        John Smith
    #    1                3                         2        Tim Watson
    #    2                4                         3        Jane Doe
    #    3                2                          4        User12345

    # $user = $this->user->findOrFail(2);
    #  foreach($user->followers as $follower){
    #    {{ $follower->following_id }}  ----> id 2 is following id 1, id 2 is following 3
    # }



    public function following()
    {
        // return $this->hasMany(User::class, 'following_id');
        return $this->belongsTo(User::class)->withTrashed();

    }



}
