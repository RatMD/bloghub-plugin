<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Illuminate\Contracts\Database\Query\Builder;
use RainLab\Blog\Components\Posts;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Tag;

class Dates extends Posts
{

    /**
     * The post list filtered by date
     *
     * @var array
     */
    public $date = [];

    /**
     * Component Details
     *
     * @return void
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.dates_title',
            'description'   => 'ratmd.bloghub::lang.components.dates_description'
        ];
    }

    /**
     * Component Properties
     *
     * @return void
     */
    public function defineProperties()
    {
        $properties = parent::defineProperties();
        $properties['dateFilter'] = [
            'title'       => 'ratmd.bloghub::lang.components.date_filter_title',
            'description' => 'ratmd.bloghub::lang.components.date_filter_description',
            'type'        => 'string',
            'default'     => '',
        ];
        return $properties;
    }

    /**
     * Prepare Variables
     *
     * @return void
     */
    protected function prepareVars()
    {
        $this->date = $this->page['date'] = $this->loadDate();
    }

    /**
     * List Posts
     *
     * @return mixed
     */
    protected function listPosts()
    {
        if (empty($this->date)) {
            return [];
        }
        $date = $this->date;
        $category = $this->category ? $this->category->id : null;
        $categorySlug = $this->category ? $this->category->slug : null;

        // Start and End Date
        if (isset($date['day'])) {
            $start_date = $date['year'] . '-' . substr('0' . $date['month'], -2) . '-' . substr('0' . $date['day'], -2) . ' 00:00:00';
            $end_date = $date['year'] . '-' . substr('0' . $date['month'], -2) . '-' . substr('0' . $date['day'], -2) . ' 23:59:59';
        } else if (isset($date['month'])) {
            $last_day = date('t', strtotime("{$date['year']}-{$date['month']}-01"));
            $start_date = $date['year'] . '-' . substr('0' . $date['month'], -2) . '-01 00:00:00';
            $end_date = $date['year'] . '-' . substr('0' . $date['month'], -2) . '-' . $last_day. ' 23:59:59';
        } else {
            $start_date = $date['year'] . '-01-01 00:00:00';
            $end_date = $date['year'] . '-12-31 23:59:59';
        }

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !parent::checkEditor();

        $posts = Post::with(['categories', 'featured_images', 'bloghub_tags'])
            ->whereBetween('published_at', [$start_date, $end_date])
            ->listFrontEnd([
                'page'             => $this->property('pageNumber'),
                'sort'             => $this->property('sortOrder'),
                'perPage'          => $this->property('postsPerPage'),
                'search'           => trim(input('search') ?? ''),
                'category'         => $category,
                'published'        => $isPublished,
                'exceptPost'       => is_array($this->property('exceptPost'))
                    ? $this->property('exceptPost')
                    : preg_split('/,\s*/', $this->property('exceptPost'), -1, PREG_SPLIT_NO_EMPTY),
                'exceptCategories' => is_array($this->property('exceptCategories'))
                    ? $this->property('exceptCategories')
                    : preg_split('/,\s*/', $this->property('exceptCategories'), -1, PREG_SPLIT_NO_EMPTY),
            ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) use ($categorySlug) {
            $post->setUrl($this->postPage, $this->controller, ['category' => $categorySlug]);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }
    
    /**
     * Load Date
     *
     * @return array
     */
    protected function loadDate()
    {
        $date = [];

        // Explode Date string
        $data = explode('-', $this->property('dateFilter'));
        $year = $data[0];
        $month = $data[1] ?? null;
        $day = $data[2] ?? null;

        // Validate Year
        if (!is_numeric($year)) {
            return [];
        }
        
        $year = intval($year);
        if ($year > 1970) {
            $date['year'] = $year;
        } else {
            return [];
        }

        // Validate Month
        if (is_numeric($month)) {
            $month = intval($month);
            if ($month >= 1 && $month <= 12) {
                $date['month'] = $month;
            }
        }

        // Validate Day
        if (array_key_exists('month', $date) && is_numeric($day)) {
            $day = intval($day);

            if ($day >= 1 && $day <= intval(date('t', strtotime("$year-$month-01")))) {
                $date['day'] = $day;
            }
        }

        return $date;
    }

}
