<?php declare(strict_types=1);

namespace RatMD\BlogHub\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Comments extends Controller
{

    /**
     * Implemented Interfaces
     *
     * @var array
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * Form Configuration File
     * 
     * @var string
     */
    public $formConfig = 'config_form.yaml';

    /**
     * List Configuration File
     * 
     * @var string
     */
    public $listConfig = 'config_list.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('RainLab.Blog', 'blog', 'ratmd_bloghub_comments');
    }
    
}
