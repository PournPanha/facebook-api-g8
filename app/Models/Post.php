<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'video',
        'user_id',
        'tags',
    ];
   
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * List all posts or filter based on request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function list(Request $request)
    {
        $query = self::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }
        Log::info($query->toSql(), $query->getBindings());

        return $query->get();
    }

    /**
     * Store or update a post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function store(Request $request, $id = null)
    {
        $data = $request->only('title', 'content','image','video', 'user_id', 'tags');
        $validatedData = [
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'image' => $data['image'] ?? '',
            'video' => $data['video'] ?? '',
            'user_id' => $data['user_id'] ?? null,
            'tags' => $data['tags'] ?? '',
        ];

        return self::updateOrCreate(['id' => $id], $validatedData);
    }
}

