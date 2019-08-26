<?php

namespace Broarm\OpeningHours;

use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Class GridFieldConfig_OpeningHours
 *
 * @package Broarm\Silverstripe\OpeningHours
 */
class GridFieldConfig_OpeningHours extends GridFieldConfig
{

    /**
     * GridFieldConfig_OpeningHours constructor.
     *
     * @param HasManyList $object
     * @param string $sortField
     */
    public function __construct(HasManyList $object, $sortField = 'Sort')
    {
        parent::__construct();

        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent(new GridFieldEditableColumns());
        if ($object->count() < 7) {
            $this->addComponent(new GridFieldAddNewInlineButton("toolbar-header-right"));
        }
    }
}
