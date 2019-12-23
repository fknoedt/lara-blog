<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $fillable = ['id', 'slug', 'name', 'user_id', 'parent_id'];

    /**
     * Custom Attributes
     * @var array
     */
    protected $appends = ['parent_category'];

    /**
     * Custom attribute (N x 1 with category)
     * @return \Illuminate\Support\Collection
     */
    public function getParentCategoryAttribute()
    {
        return $this->getParentId() ? $this->category()->pluck('name')->first() : '';
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
     * Relationship: Many to One Categories (self join)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship: One to Many Posts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
