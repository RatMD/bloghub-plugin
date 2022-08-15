<?php declare(strict_types=1);

namespace RatMD\BlogHub\Models;

use Model;
use Illuminate\Support\Str;

/**
 * Meta Model
 */
class Meta extends Model
{
    /**
     * Table associated with this Model
     * 
     * @var string
     */
    public $table = 'ratmd_bloghub_meta';

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
        "name",
        "value",
        "metable_id",
        "metable_type",
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
    public $morphTo = [
        'metable' => []
    ];

    /**
     * JSONable fields
     * 
     * @var string[]
     */
    public $jsonable = [
        'value'
    ];

}
