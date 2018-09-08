<?php

namespace Broarm\OpeningHours;

use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;

/**
 * Class OpeningHours
 * @package Broarm\Silverstripe\OpeningHours
 *
 * @property OpeningHours|DataObject owner
 * @method HasManyList OpeningHours
 */
class OpeningHours extends DataExtension
{
    private static $has_many = array(
        'OpeningHours' => OpeningHour::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->exists()) {
            $config = new GridFieldConfig_OpeningHours($this->owner->OpeningHours());
            $openingHours = new GridField('OpeningHours', 'OpeningHours', $this->owner->OpeningHours(), $config);
        } else {
            $openingHours = new LiteralField('Notice',
                "<p class='message notice'>The object must be saved before opening hours can be added</p>");
        }

        $fields->addFieldToTab('Root.OpeningHours', $openingHours);
        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->owner->exists() && !$this->owner->OpeningHours()->exists()) {
            $this->createOpeningHours();
        }
    }

    /**
     * Set up the opening hours for each day of the week
     */
    private function createOpeningHours()
    {
        $days = OpeningHour::singleton()->dbObject('Day')->enumValues();
        foreach ($days as $day) {
            $openingHour = OpeningHour::create();
            $openingHour->Day = $day;
            $this->owner->OpeningHours()->add($openingHour);
        }
    }


    /**
     * Get the opening hours
     *
     * @return OpeningHour|DataObject|null
     */
    public function getOpeningHoursToday()
    {
        return OpeningHour::get_today();
    }


    /**
     * Get a summarized version of the set opening hours
     *
     * @return ArrayList
     */
    public function getOpeningHoursSummarized()
    {
        $hours = $this->owner->OpeningHours()->getIterator();
        $hoursOut = new ArrayList();
        $current = null;
        $prev = null;

        while ($hours->valid()) {
            $current = $hours->current();
            if ($prev && self::same_time($current, $prev)) {
                $hoursOut->last()->addDay($current->getShortDay());
            } else {
                $hoursOut->add($current);
            }
            $prev = $current;
            $hours->next();
        }

        return $hoursOut;
    }


    /**
     * Check if the time entries are the same
     *
     * @param OpeningHour $a
     * @param OpeningHour $b
     * @return bool
     */
    private static function same_time(OpeningHour $a, OpeningHour $b)
    {
        return $a->Till === $b->Till
            && $a->From === $b->From;
    }
}
