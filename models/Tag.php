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
        ],
        'posts_count' => [
            'RainLab\Blog\Models\Post',
            'table' => 'ratmd_bloghub_tags_posts',
            'scope' => 'isPublished',
            'count' => true
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

    /**
     * Sets the "url" attribute with a URL to this object.
     * 
     * @param string $pageName
     * @param Controller $controller
     * @param array $params Override request URL parameters
     * @return string
     */
    public function setUrl($pageName, $controller, $params = [])
    {
        $params = array_merge([
            'id'   => $this->id,
            'slug' => $this->slug,
        ], $params);

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    /**
     * Get Posts Count Value
     *
     * @return int
     */
    public function getPostCountAttribute()
    {
        return optional($this->posts_count->first())->count ?? 0;
    }

}
