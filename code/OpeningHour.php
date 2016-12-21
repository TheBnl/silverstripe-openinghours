<?php
/**
 * OpeningHour.php
 *
 * @author Bram de Leeuw
 * Date: 23/11/16
 */


/**
 * OpeningHour
 *
 * @property string Title
 * @property string Day
 * @property Time From
 * @property Time Till
 *
 * @method DataObject Parent
 */
class OpeningHour extends DataObject {

    const MIDNIGHT_THRESHOLD = 5;

    private static $db = array(
        'Title' => 'Varchar(9)',
        'Day' => 'Enum("Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday", "Monday")',
        'From' => 'Time',
        'Till' => 'Time',
        'Sort' => 'Int'
    );

    private static $default_sort = 'Sort ASC';

    private static $has_one = array(
        'Parent' => 'DataObject'
    );

    private static $has_many = array();
    private static $many_many = array();

    private static $defaults = array(
        'From' => '09:00:00',
        'Till' => '22:00:00'
    );

    private static $belongs_many_many = array();
    private static $searchable_fields = array();

    private static $summary_fields = array(
        'getFullDay' => 'Day',
        'From' => 'From',
        'Till' => 'Till'
    );

    private static $translate = array();

    public function getCMSFields() {
        $fields = new FieldList(new TabSet('Root', $mainTab = new Tab('Main')));

        $day = $this->Day;
        $dayField = new ReadonlyField('Day', 'Day', $day);
        $from = new TimeField('From', 'From');
        $till = new TimeField('Till', 'Till');

        $fields->addFieldsToTab('Root.Main', array(
            $dayField,
            $from,
            $till
        ));

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }


    /**
     * Set the title and sorting value to the day of the week
     */
    protected function onBeforeWrite()
    {
        $this->setField('Title', $this->getField('Day'));
        $this->setField('Sort', $this->sortVal());
        parent::onBeforeWrite();
    }


    /**
     * Return the sorting value by the chosen day pf the week
     *
     * @return false|string
     */
    private function sortVal() {
        $day = $this->getField('Day');
        return date('N', strtotime($day));
    }


    /**
     * Return the short localized day
     *
     * @return string
     */
    public function getShortDay() {
        $day = $this->getField('Day');
        return strftime('%a', strtotime($day));
    }


    /**
     * Return the full localized day
     *
     * @return string
     */
    public function getFullDay() {
        $day = $this->getField('Day');
        return strftime('%A', strtotime($day));
    }


    /**
     * Get the opening hours for the current day of the week
     *
     * @return OpeningHour|DataObject|null
     */
    public static function get_today() {
        if ($today = self::get()->find('Day', date('l', time()))) {
            return $today;
        } else {
            return null;
        }
    }


    /**
     * Check if the opening hours fall between the given threshold
     *
     * @param OpeningHour $day
     * @return bool
     */
    public static function is_open(OpeningHour $day) {
        $from = $day->getField('From');
        $till = self::after_midnight($day->getField('Till'));
        $now = self::after_midnight(date('G:i:s', time()));
        return ($now < $till) && ($now > $from);
    }


    /**
     * Make after midnight calculations possible by adding the after midnight hours to a full day
     *
     * @param $time
     * @return mixed
     */
    private static function after_midnight($time) {
        return $time < self::MIDNIGHT_THRESHOLD ? ($time + 24) : $time;
    }


    public function canView($member = null) {
        if (!$this->Parent()) return false;
        return $this->Parent()->canView($member);
    }

    public function canEdit($member = null) {
        if (!$this->Parent()) return false;
        return $this->Parent()->canEdit($member);
    }

    public function canDelete($member = null) {
        if (!$this->Parent()) return false;
        return $this->Parent()->canDelete($member);
    }

    public function canCreate($member = null) {
        if (!$this->Parent()) return false;
        return $this->Parent()->canCreate($member);
    }
}