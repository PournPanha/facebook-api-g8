<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'post_id','react'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function likePost(array $data, $id = null)
    {
        $validateData = [
            'user_id' => $data['user_id'] ?? null,
            'post_id' => $data['post_id'] ?? null,
        ];

        // Find existing like record by user_id and post_id
        $like = self::where('user_id', $validateData['user_id'])
                    ->where('post_id', $validateData['post_id'])
                    ->first();

        if ($like) {
            // If like record already exists, return it
            return $like;
        }

        // If no existing like record found, create a new one
        return self::findOrNew($id)->fill($validateData)->save();
    }
}

