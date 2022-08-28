<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use RainLab\Blog\Components\Posts;
use RainLab\Blog\Models\Post;
use Redirect;

class Dates extends Posts
{

    /**
     * Post Date Archive array
     *
     * @var array
     */
    public $date = [];

    /**
     * Post Date Archive Type 
     *
     * @var ?string
     */
    public $dateType = null;

    /**
     * Formatted Date Archive String
     *
     * @var string
     */
    public $dateFormat = '';

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
     * Run Component
     * 
     * @return mixed
     */
    public function onRun()
    {
        [$date, $type] = $this->loadDate();
        if (empty($date)) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        // Set Page Variables
        $this->date = $this->page['date'] = $date;
        $this->dateType = $this->page['dateType'] = $type;
        $this->dateFormat = $this->page['dateFormat'] = $this->formatDate($this->date);
        $this->posts = $this->page['posts'] = $this->listPosts();

        // Return 404 on empty date archives
        if ($this->posts->count() === 0) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        // Set Latest Page Number
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1) {
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
            }
        }
    }

    /**
     * List Posts
     *
     * @return mixed
     */
    protected function listPosts()
    {
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
     * Load & Validate Date
     *
     * @return array
     */
    protected function loadDate()
    {
        $date = [];
        $type = null;

        // Explode Date string
        $data = explode('-', $this->property('dateFilter'));
        if (count($data) > 3) {
            return [null, null];
        }

        // Year Archive
        if (count($data) >= 1) {
            $type = 'year';

            if (is_numeric($data[0]) && ($year = intval($data[0])) && $year >= 1970 && $year <= intval(date('Y'))) {
                $date['year'] = $year;
            } else {
                return [null, null];
            }
        }
        
        // Month Archive
        if (count($data) >= 2) {
            $type = 'month';

            if (is_numeric($data[1]) && ($month = intval($data[1])) && $month >= 1 && $month <= 12) {
                $date['month'] = $month;
            } else {
                return [null, null];
            }
        }

        // Day Archive
        if (count($data) == 3) {
            $type = 'day';
            
            if (is_numeric($data[2]) && ($day = intval($data[2])) && $day >= 1 && $day <= intval(date('t', strtotime("$year-$month-01")))) {
                $date['day'] = $day;
            } else {
                return [null, null];
            }
        }

        // Return Result
        return [$date, $type];
    }

    /**
     * Format Date
     *
     * @param array $date
     * @return void
     */
    protected function formatDate(array $date)
    {
        if (isset($date['day'])) {
            return date('F, d. Y', strtotime("{$date['year']}-{$date['month']}-{$date['day']} 00:00:00"));
        } else if (isset($date['month'])) {
            return date('F, Y', strtotime("{$date['year']}-{$date['month']}-01 00:00:00"));
        } else {
            return $date['year'];
        }
    }

}
