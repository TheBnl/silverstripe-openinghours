<?php
/**
 * GridFieldConfig_OpeningHours.php
 *
 * @author Bram de Leeuw
 * Date: 21/12/16
 */

/**
 * Class GridFieldConfig_OpeningHours
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
        if ($object->count() < 7)  $this->addComponent(new GridFieldAddNewInlineButton("toolbar-header-right"));
    }
}