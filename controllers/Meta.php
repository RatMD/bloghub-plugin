<?php declare(strict_types=1);

namespace RatMD\BlogHub\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Meta extends Controller
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
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('RatMD.BlogHub', 'bloghub', 'meta');
    }

}
