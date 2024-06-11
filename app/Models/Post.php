<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'auth_id',
        'tags',
    ];

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

        // Log the SQL query for debugging
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
        $data = $request->only('title', 'content', 'auth_id', 'tags');

        // Validate and sanitize the input data
        $validatedData = [
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'auth_id' => $data['auth_id'] ?? null,
            'tags' => $data['tags'] ?? '',
        ];

        return self::updateOrCreate(['id' => $id], $validatedData);
    }
}

