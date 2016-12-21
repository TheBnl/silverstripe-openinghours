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

    private function createOpeningHours() {
        $days = OpeningHour::singleton()->dbObject('Day')->enumValues();
        foreach ($days as $day) {
            $openingHour = OpeningHour::create();
            $openingHour->Day = $day;
            $this->owner->OpeningHours()->add($openingHour);
        }
    }
}