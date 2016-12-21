# OpeningHours for Silverstripe

Add opening hours to a data object. I suggest adding [silverstripe-australia/addressable](https://github.com/silverstripe-australia/silverstripe-addressable) to add opening hours and address data to a Store site. In the future i would suggest using [bramdeleeuw/silverstripe-schema](https://github.com/TheBnl/silverstripe-schema) to add schema data, like store information, to your site but it's still a work in progress. 

To add the opening hours, simply extens on the object that you want to use them on.
```yaml
YourObject:
  extensions:
    - OpeningHours
```

The object comes packed with some methods you can use to display the opening hours with:
```php
// Object methods
 
// Return the short localized version for the current day (in the loop)
$openingHour->getShortDay()
 
// Return the short localized version for the current day (in the loop)
$openingHour->getFullDay()
 
// Static methods
 
// Returns if the given day is open or not, nice to display a 'store is open' message.
OpeningHour::is_open(OpeningHour $day)
```

## License

Copyright (c) 2016, Bram de Leeuw
All rights reserved.

All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
 * The name of Bram de Leeuw may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.