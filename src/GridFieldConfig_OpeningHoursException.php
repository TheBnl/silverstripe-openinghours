<?php

namespace Broarm\OpeningHours;

use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

/**
 * Class GridFieldConfig_OpeningHoursException
 *
 * @package Broarm\Silverstripe\OpeningHours
 */
class GridFieldConfig_OpeningHoursException extends GridFieldConfig_RecordEditor
{
    public function __construct()
    {
        parent::__construct();
        $this->removeComponentsByType([new GridFieldDataColumns(), new GridFieldAddNewButton()]);
        $this->addComponent(new GridFieldAddNewInlineButton());
        $this->addComponent(new GridFieldEditableColumns(), new GridFieldEditButton());
    }
}
