<?php
/**
 * OpeningHours.php
 *
 * @author Bram de Leeuw
 * Date: 21/12/16
 */
 
 
/**
 * OpeningHours
 *
 * @property OpeningHours|DataObject owner
 *
 * @method HasManyList OpeningHours
 */
class OpeningHours extends DataExtension {

    private static $has_many = array(
        'OpeningHours' => 'OpeningHour'
    );

    public function updateCMSFields(FieldList $fields) {
        if ($this->owner->exists()) {
            $config = new GridFieldConfig_OpeningHours($this->owner->OpeningHours());
            $openingHours = new GridField('OpeningHours', 'OpeningHours', $this->owner->OpeningHours(), $config);
        } else {
            $openingHours = new LiteralField('Notice', "<p class='message notice'>The object must be saved before opening hours can be added</p>");
        }

        $fields->addFieldsToTab('Root.OpeningHours', array($openingHours));
        return $fields;
    }

    public function onAfterWrite()
    {
        if ($this->owner->OpeningHours()->count() === 0) $this->createOpeningHours();
        parent::onAfterWrite();
    }


    /**
     * Set up the opening hours for each day of the week
     */
    private function createOpeningHours() {
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
     * @return ViewableData
     */
    public function getOpeningHoursToday() {
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
    private static function same_time(OpeningHour $a, OpeningHour $b) {
        return $a->Till === $b->Till
            && $a->From === $b->From;
    }
}