<?php

namespace Broarm\OpeningHours;

use DatePeriod;
use DateTime;
use DateInterval;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\TimeField;

/**
 * Class OpeningHourException
 * @package Broarm\OpeningHours
 *
 * @property string DateFrom
 * @property string DateTill
 * @property string From
 * @property string Till
 * @property string Reason
 */
class OpeningHourException extends DataObject
{
    private static $table_name = 'OpeningHourException';

    private static $db = [
        'DateFrom' => 'Date',
        'DateTill' => 'Date',
        'From' => 'Time',
        'Till' => 'Time',
        'Reason' => 'Varchar'
    ];

    private static $summary_fields = [
        'DateFrom',
        'DateTill',
        'From',
        'Till'
    ];

    private static $has_one = [
        'Parent' => DataObject::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', [
                DateField::create('DateFrom'),
                DateField::create('DateTill'),
                TimeField::create('From'),
                TimeField::create('Till'),
                TextField::create('Reason'),
            ]);
        });

        return parent::getCMSFields();
    }

    public function getRange()
    {
        if ($this->DateTill > $this->DateFrom) {
            return new DatePeriod(
                new DateTime($this->DateFrom),
                new DateInterval('P1D'),
                new DateTime($this->DateTill)
            );
        } else {
            return [new DateTime($this->DateFrom)];
        }
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['DateFrom'] = _t(__CLASS__ . '.DateFrom', 'Date from');
        $labels['DateTill'] = _t(__CLASS__ . '.DateTill', 'Date till');
        $labels['From'] = _t(__CLASS__ . '.From', 'Time from');
        $labels['Till'] = _t(__CLASS__ . '.Till', 'Time till');
        $labels['Reason'] = _t(__CLASS__ . '.Reason', 'Reason');
        return $labels;
    }
}