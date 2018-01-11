# OpeningHours for SilverStripe

Add opening hours to a data object. I suggest adding [silverstripe-australia/addressable](https://github.com/silverstripe-australia/silverstripe-addressable) to add opening hours and address data to a Store site. In the future i would suggest using [bramdeleeuw/silverstripe-schema](https://github.com/TheBnl/silverstripe-schema) to add schema data, like store information, to your site but it's still a work in progress. 

To add the opening hours, simply extens on the object that you want to use them on.
```yaml
YourObject:
  extensions:
    - 'Broarm\OpeningHours\OpeningHours'
```

The object comes packed with some methods you can use to display the opening hours with:
```php
// Returns the opening hours as a summarized list, this means days with similar opening hours are combined e.g "Mon – Tue"
$openingHourHolder->getOpeningHoursSummarized()
 
// Returns todays opening hours
$openingHourHolder->getOpeningHoursToday()
 
// Return the short localized version for the current day (in the loop)
$openingHour->getShortDay();
 
// Return the short localized version for the current day (in the loop)
$openingHour->getFullDay();
 
// Return the concatnated days list as a range, only used when looping over the summarized days loop.
$openingHour->getConcatenatedDays();
 
// Returns true when the From and Till data are equal (shop is closed for that day)
$openingHour->IsClosed();
 
// Returns true when the current time falls between the opening hours
$openingHour->IsOpenNow();
```