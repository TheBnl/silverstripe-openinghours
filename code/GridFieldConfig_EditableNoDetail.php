<?php

namespace Broarm\Silverstripe\OpeningHours;

use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\HasManyList;

/**
 * Class GridFieldConfig_OpeningHours
 *
 * @package Broarm\Silverstripe\OpeningHours
 */
class GridFieldConfig_OpeningHours extends GridFieldConfig
{

    /**
     * GridFieldConfig_EditableNoDetail constructor.
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
