<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Post extends Model
{
    protected $fillable = ['id', 'title', 'description', 'long_description', 'image_url', 'user_id', 'category_id', 'created_at', 'updated_at'];

    /**
     * Custom Attributes
     * @var array
     */
    protected $appends = ['author', 'readable_created_date', 'category'];

    /**
     * Custom attribute (N x 1 with users)
     * @return \Illuminate\Support\Collection
     */
    public function getAuthorAttribute()
    {
        return $this->user()->pluck('name')->first();
    }

    /**
     * Custom attribute (N x 1 with category)
     * @return \Illuminate\Support\Collection
     */
    public function getCategoryAttribute()
    {
        return $this->category()->pluck('name')->first();
    }

    /**
     * Date parsed for human readbility
     * @return false|string
     */
    public function getReadableCreatedDateAttribute()
    {
        return date("jS F, Y g:i A", strtotime($this->created_at));
    }

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
