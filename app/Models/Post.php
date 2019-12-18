<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Post extends Model
{
    protected $fillable = ['id', 'title', 'description', 'long_description', 'image_url', 'user_id', 'category_id'];

    /**
     * Relationship: Many to One User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Many to One Category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
