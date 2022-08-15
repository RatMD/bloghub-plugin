<?php declare(strict_types=1);

namespace RatMD\BlogHub\Models;

use Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * Table associated with this Model
     * 
     * @var string
     */
    public $table = 'ratmd_bloghub_tags';

    /**
     * Enable Modal Timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Guarded Model attributes
     * 
     * @var array
     */
    protected $guarded = [
        '*'
    ];

    /**
     * Fillable Model attributes
     * 
     * @var array
     */
    protected $fillable = [
        "slug",
        "title",
        "description",
        "promote",
        "color"
    ];

    /**
     * Model Validation Rules
     * 
     * @var array
     */
    public $rules = [
        'slug' => 'required|unique:ratmd_bloghub_tags'
    ];

    /**
     * Mutable Date Attributes
     * 
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * BelongsToMany Relationships
     *
     * @var array
     */
    public $belongsToMany = [
        'posts' => [
            'RainLab\Blog\Models\Post',
            'table' => 'ratmd_bloghub_tags_posts',
            'order' => 'published_at desc'
        ]
    ];

    /**
     * Hook - Before Model is createds
     *
     * @return void
     */
    public function beforeCreate()
    {
        $this->title = empty($this->title)? $this->slug: $this->title;
        $this->slug = Str::slug($this->slug);
    }

}
