<?php

namespace Broarm\OpeningHours;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBTime;
use SilverStripe\ORM\FieldType\DBDate;

/**
 * Class OpeningHour
 *
 * @package Broarm\Silverstripe\OpeningHours
 *
 * @property string Title
 * @property string Day
 * @property DBTime From
 * @property DBTime Till
 *
 * @method DataObject Parent
 */
class OpeningHour extends DataObject
{
    private static $midnight_threshold = 5;

    private static $days_as_range = 2;

    private static $table_name = 'OpeningHour';

    private static $db = [
        'Title' => 'Varchar(9)',
        'Day' => 'Enum("Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday", "Monday")',
        'From' => 'Time',
        'Till' => 'Time',
        'Sort' => 'Int'
    ];

    private static $default_sort = 'Sort ASC';

    private static $has_one = [
        'Parent' => DataObject::class
    ];

    private static $defaults = [
        'From' => '09:00:00',
        'Till' => '22:00:00'
    ];

    private static $summary_fields = [
        'getFullDay' => 'Day',
        'From' => 'From',
        'Till' => 'Till'
    ];

    public function getCMSFields()
    {
        $fields = FieldList::create(new TabSet('Root', new Tab('Main')));
        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('Day', _t(__CLASS__ . '.Day', 'Day'), $this->Day),
            TimeField::create('From', _t(__CLASS__ . '.From', 'From')),
            TimeField::create('Till', _t(__CLASS__ . '.Till', 'Till'))
        ]);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Set the title and sorting value to the day of the week
     */
    protected function onBeforeWrite()
    {
        $this->setField('Title', $this->Day);
        $this->setField('Sort', $this->sortVal());
        parent::onBeforeWrite();
    }

    /**
     * Return the full localized day
     *
     * @return string
     */
    public function getFullDay()
    {
        $dayTime = strtotime($this->Day);
        return DBDate::create()->setValue($dayTime)->Format('cccc');
    }

    /**
     * Return the sorting value by the chosen day pf the week
     *
     * @return false|string
     */
    private function sortVal()
    {
        $day = $this->Day;
        return date('N', strtotime($day));
    }
}
