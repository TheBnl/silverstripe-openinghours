<?php

namespace Broarm\OpeningHours;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBTime;

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
        'From' => 'DBTime',
        'Till' => 'DBTime',
        'Sort' => 'Int'
    ];

    private static $default_sort = 'Sort ASC';

    private static $has_one = [
        'Parent' => 'DataObject'
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

    protected $concatenatedDays;

    public function getCMSFields()
    {
        $fields = FieldList::create(new TabSet('Root', new Tab('Main')));
        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('Day', _t('OpeningHours.DAY', 'Day'), $this->Day),
            TimeField::create('From', _t('OpeningHours.FROM', 'From')),
            TimeField::create('Till', _t('OpeningHours.TILL', 'Till'))
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
     * Return the sorting value by the chosen day pf the week
     *
     * @return false|string
     */
    private function sortVal()
    {
        $day = $this->Day;
        return date('N', strtotime($day));
    }


    /**
     * Return the short localized day
     *
     * @return string
     */
    public function getShortDay()
    {
        $day = $this->Day;
        return ucfirst(strftime('%a', strtotime($day)));
    }


    /**
     * Return the full localized day
     *
     * @return string
     */
    public function getFullDay()
    {
        $day = $this->Day;
        return ucfirst(strftime('%A', strtotime($day)));
    }


    /**
     * Return the days store as a concatenated day range or as the short day
     *
     * @return string
     */
    public function getConcatenatedDays()
    {
        if (isset($this->concatenatedDays)) {
            return self::concat_days(explode(', ', $this->concatenatedDays));
        } else {
            return $this->getShortDay();
        }
    }


    /**
     * Add a day to the concat days list
     *
     * @param $day
     */
    public function addDay($day)
    {
        if (!isset($this->concatenatedDays)) {
            $this->concatenatedDays = $this->getShortDay();
        }
        $this->concatenatedDays .= ", $day";
    }


    /**
     * Concat the days to a range
     *
     * @param array $days
     *
     * @return null|string
     */
    private static function concat_days(array $days = [])
    {
        if (count($days) > self::config()->get('days_as_range')) {
            $last = end($days);
            $rangeDelimiter = _t('OpeningHours.RANGE_DELIMITER', '–');
            return "{$days[0]} $rangeDelimiter {$last}";
        } else {
            return implode(', ', $days);
        }
    }


    /**
     * Check if the opening hours fall between the given threshold
     *
     * @return bool
     */
    public function IsOpenNow()
    {
        if (!$this->IsClosed()) {
            $from = $this->From;
            $till = self::after_midnight($this->Till);
            $now = self::after_midnight(date('G:i:s', time()));
            return (bool)($now < $till) && ($now > $from);
        }

        return false;
    }


    /**
     * Returns if the shop is open on the current day
     *
     * @return bool
     */
    public function IsClosed()
    {
        return (bool)($this->From === $this->Till);
    }


    /**
     * Get the opening hours for the current day of the week
     *
     * @return OpeningHour|DataObject|null
     */
    public static function get_today()
    {
        if ($today = self::get()->find('Day', date('l', time()))) {
            return $today;
        } else {
            return null;
        }
    }


    /**
     * Make after midnight calculations possible by adding the after midnight hours to a full day
     *
     * @param $time
     *
     * @return mixed
     */
    private static function after_midnight($time)
    {
        return $time < self::config()->get('midnight_threshold') ? ($time + 24) : $time;
    }

    public function canView($member = null)
    {
        if ($parent = $this->Parent()) {
            return $parent->canView($member);
        }

        return parent::canView($member);
    }

    public function canEdit($member = null)
    {
        if ($parent = $this->Parent()) {
            return $parent->canEdit($member);
        }

        return parent::canEdit($member);
    }

    public function canDelete($member = null)
    {
        if ($parent = $this->Parent()) {
            return $parent->canDelete($member);
        }

        return parent::canDelete($member);
    }

    public function canCreate($member = null, $context = array())
    {
        if ($parent = $this->Parent()) {
            return $parent->canCreate($member, $context);
        }

        return parent::canCreate($member, $context);
    }
}
