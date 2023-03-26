<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Redirect;
use RainLab\Blog\Components\Posts;
use RainLab\Blog\Models\Post;

class PostsByDate extends Posts
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
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.date.label',
            'description'   => 'ratmd.bloghub::lang.components.date.comment'
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
            'title'         => 'ratmd.bloghub::lang.components.date.filter',
            'description'   => 'ratmd.bloghub::lang.components.date.filter_comment',
            'type'          => 'string',
            'default'       => '{{ :date }}',
            'group'         => 'ratmd.bloghub::lang.components.bloghub_group',
        ];
        return $properties;
    }

    /**
     * Get BlogHub Layout Configuration
     *
     * @return array
     */
    protected function getBlogHubConfig()
    {
        $config = $this->controller->getLayout()->getComponent('bloghubBase');
        if ($config) {
            $config = $config->getProperties();
        }

        return array_merge([
            'archiveAuthor' => 'blog/author',
            'archiveDate' => 'blog/date',
            'archiveTag' => 'blog/tag',
            'authorUseSlugOnly' =>'0',
            'date404OnInvalid' => '1',
            'date404OnEmpty' => '1',
            'tagAllowMultiple' => '0',
        ], $config ?? []);
    }

    /**
     * Run Component
     * 
     * @return mixed
     */
    public function onRun()
    {
        $this->prepareVars();

        [$date, $type] = $this->loadDate();
        if (empty($date)) {
            if ($this->getBlogHubConfig()['date404OnInvalid'] === '1') {
                $this->setStatusCode(404);
                return $this->controller->run('404');
            }
        }

        // Set Page Properties
        $this->date = $this->page['date'] = $date;
        $this->dateType = $this->page['dateType'] = $type;
        if (!empty($date)) {
            $this->dateFormat = $this->page['dateFormat'] = $this->formatDate($this->date);
            $this->posts = $this->page['posts'] = $this->listPosts();
        } else {
            $this->dateFormat = $this->page['dateFormat'] = null;
            $this->posts = $this->page['posts'] = [];
        }

        // Return 404 on empty date archives
        if ($this->posts->count() === 0) {
            if ($this->getBlogHubConfig()['date404OnEmpty'] === '1') {
                $this->setStatusCode(404);
                return $this->controller->run('404');
            }
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
        } else if (isset($date['week'])) {
            $datetime = new \DateTime();
            $datetime->setISODate($date['year'], $date['week']);

            $start_date = $datetime->format('Y-m-d') . ' 00:00:00';
            $datetime->modify('+6 days');
            $end_date = $datetime->format('Y-m-d') . ' 23:59:59';
        } else {
            $start_date = $date['year'] . '-01-01 00:00:00';
            $end_date = $date['year'] . '-12-31 23:59:59';
        }

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !parent::checkEditor();

        $posts = Post::with(['categories', 'featured_images', 'ratmd_bloghub_tags'])
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
        if (empty($dateFilter = $this->property('dateFilter'))) {
            return [null, null];
        }

        // Get Date
        if (strpos($dateFilter, '_') === 4) {
            $year = substr($dateFilter, 0, 4);
            $week = substr($dateFilter, 5);
        } else if(strlen($dateFilter) >= 4) {
            $year = substr($dateFilter, 0, 4);
            $month = strlen($dateFilter) >= 7 ? substr($dateFilter, 5, 2) : null;
            $day = strlen($dateFilter) == 10 ? substr($dateFilter, 8, 2) : null;
        } else {
            return [null, null];
        }
        $date = [];
        $type = null;

        // Validate Year
        if (is_numeric($year) && ($year = intval($year)) && $year >= 1970 && $year <= intval(date('Y'))) {
            $date['year'] = $year;
            $type = 'year';
        } else {
            return [null, null];
        }

        // Validate Week
        if (!empty($week)) {
            if (is_numeric($week) && ($week = intval($week)) && $week <= intval(date('W', strtotime('December 28th')))) {
                $date['week'] = $week;
                return [$date, 'week'];
            }
        }

        // Validate Month
        if (!empty($month)) {
            if (is_numeric($month) && ($month = intval($month)) && $month >= 1 && $month <= 12) {
                $date['month'] = $month;
                $type = 'month';
            } else {
                return [null, null];
            }
        }

        // Validate Day
        if (!empty($day)) {
            if (is_numeric($day) && ($day = intval($day)) && $day >= 1 && $day <= intval(date('t', strtotime("$year-$month-01")))) {
                $date['day'] = $day;
                $type = 'day';
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
        } else if (isset($date['week'])) {
            $datetime = new \DateTime();
            $datetime->setISODate($date['year'], $date['week']);
            return $datetime->format('\WW, Y');
        } else {
            return $date['year'];
        }
    }

}
