<?php

namespace Broarm\OpeningHours;

use DateTime;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBTime;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\ViewableData;

/**
 * Class OpeningHours
 * @package Broarm\Silverstripe\OpeningHours
 *
 * @property OpeningHours|DataObject owner
 * @method HasManyList OpeningHours
 * @method HasManyList OpeningHourExceptions
 */
class OpeningHours extends DataExtension
{
    use Configurable;

    private static $short_day_format = 'ccc';

    private static $long_day_format = 'cccc';

    /**
     * @var \Spatie\OpeningHours\OpeningHours
     */
    protected $_openinghours;

    private static $has_many = array(
        'OpeningHours' => OpeningHour::class,
        'OpeningHourExceptions' => OpeningHourException::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->exists()) {
            $config = GridFieldConfig_OpeningHours::create($this->owner->OpeningHours());
            $openingHours = GridField::create(
                'OpeningHours',
                _t(__CLASS__ . '.OpeningHours', 'Opening hours'),
                $this->owner->OpeningHours(),
                $config
            );
        } else {
            $openingHours = LiteralField::create('Notice',
                "<p class='message notice'>The object must be saved before opening hours can be added</p>");
        }

        $fields->addFieldToTab('Root.OpeningHours', $openingHours);
        $fields->addFieldToTab('Root.OpeningHourExceptions', GridField::create(
            'OpeningHourExceptions',
            _t(__CLASS__ . '.Exceptions', 'Exceptions'),
            $this->owner->OpeningHourExceptions(),
            GridFieldConfig_OpeningHoursException::create()
        ));

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
     * Get a queryable opening hours object
     * @return \Spatie\OpeningHours\OpeningHours
     */
    public function getOpeningHoursQuery()
    {
        if (isset($this->owner->_openinghours)) {
            return $this->owner->_openinghours;
        } else {
            $hours = [];
            array_map(function (OpeningHour $day) use (&$hours) {
                $from = $day->dbObject('From');
                $till = $day->dbObject('Till');
                $hours[$day->Day] = ["{$from->Format('HH:mm')}-{$till->Format('HH:mm')}"];
            }, $this->owner->OpeningHours()->toArray());

            if (($exceptions = $this->owner->OpeningHourExceptions()) && $exceptions->exists()) {
                array_map(function (OpeningHourException $exception) use (&$hours) {
                    $from = $exception->dbObject('From');
                    $till = $exception->dbObject('Till');
                    foreach ($exception->getRange() as $key => $value) {
                        $hours['exceptions'][$value->format('Y-m-d')] = [
                            'hours' => ["{$from->Format('HH:mm')}-{$till->Format('HH:mm')}"],
                            'data'  => $exception->Reason
                        ];
                    }
                }, $exceptions->toArray());
            }

            return $this->owner->_openinghours = \Spatie\OpeningHours\OpeningHours::create($hours);
        }
    }

    /**
     * Get the opening hours
     *
     * @return OpeningHour|DataObject|null
     */
    public function getOpeningHoursToday()
    {
        $now = new DateTime('now');
        $openinghours = $this->owner->getOpeningHoursQuery();
        $range = $openinghours->currentOpenRange($now);
        
        $out = ViewableData::create();
        if ($range) {
            $out->From = DBTime::create()->setValue($range->start());
            $out->Till = DBTime::create()->setValue($range->end());
        }
        $out->Closed = $openinghours->isClosed();
        return $out;
    }

    /**
     * Get a summarized version of the set opening hours
     * todo: check exceptions on summarized list
     *
     * @return ArrayList
     */
    public function getOpeningHoursSummarized()
    {
        $openinghours = $this->owner->getOpeningHoursQuery();
        $consecutiveDays = $openinghours->forWeekConsecutiveDays();
        $shortFormat = self::config()->get('short_day_format');
        $longFormat = self::config()->get('long_day_format');

        $out = ArrayList::create();
        foreach ($consecutiveDays as $consecutiveDay) {
            $dayFormat = count($consecutiveDay['days']) > 1 ? $shortFormat : $longFormat;
            $days = array_map(function ($day) use ($dayFormat) {
                return DBDate::create()->setValue(strtotime($day))->Format($dayFormat);
            }, $consecutiveDay['days']);

            $openingHours = $consecutiveDay['opening_hours'];
            $range = $openingHours->offsetGet(0);
            $out->add(new ArrayData([
                'Days' => implode(', ', $days),
                'From' => DBTime::create()->setValue($range->start()),
                'Till' => DBTime::create()->setValue($range->end()),
                'IsClosed' => ($range->start() === $range->end())
            ]));
        }

        return $out;
    }
}
