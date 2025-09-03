# Link Preview
A PHP class that consumes an HTTP(S) link and returns an array of preview information. Think of Facebook sharing -
whenever you paste a link, it goes to specified page and fetches some details.

Initially based on [kasp3r/link-preview](https://github.com/kasp3r/link-preview) that seems to be abandoned.

This fork is special in that it doesn't come with any specific HTTP implementation but instead lets you inject a PSR-17 Factory and a PSR-18 Client. Giving you the freedom to use whatever you like.

## Dependencies

* PHP >= 7.0
* Symfony DomCrawler >= 3.0

## Installation via Composer

To install simply run:

```
composer require marcelklehr/link-preview
```

Or add it to `composer.json` manually:

```json
{
    "require": {
        "marcelklehr/link-preview": "~1.2"
    }
}
```

## Direct usage

```php
use Marcelklehr\LinkPreview\Client;

$previewClient = new Client(/*your http implementation here*/);

// Get previews from all available parsers
$previews = $previewClient->getLink('https://www.boogiecall.com/en/Melbourne')->getPreviews();

// Only get preview data from specific parser
$preview = $previewClient->getPreview('general');

// Convert output to array
$preview = $preview->toArray();
```

**Output**

```
array(4) {
  ["basic"]=>array(2){
    ["title"]=>
    string(44) "Events, parties & live concerts in Melbourne"
    ["description"]=>
    string(107) "List of events in Melbourne. Nightlife, best parties and concerts in Melbourne, event listings and reviews."
  }
  ["image"]=>array(2){
    ["large"]=>
    string(94) "https://cdn.boogiecall.com/media/images/872398e3d9598c494a2bed72268bf018_1440575488_7314_s.jpg"
    ["small"]=>
    string(94) "https://cdn.boogiecall.com/media/images/872398e3d9598c494a2bed72268bf018_1440575488_7314_s.jpg"
  }
}
```

### YouTube example

```php
use Marcelklehr\LinkPreview\Client;

$previewClient = new Client(/*your http implementation here*/);

// Only parse YouTube specific information
$preview = $previewClient->getLink('https://www.youtube.com/watch?v=v1uKhwN6FtA')->getPreview('youtube');

var_dump($preview->toArray());
```

**Output**

```
array(2) {
  ["video"]=>array(2) {
    ["embed"]=>
    string(128) "<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/v1uKhwN6FtA" frameborder="0"/>"
    ["id"]=>
    string(11) "v1uKhwN6FtA"
  }
}
```

## License

The MIT License (MIT)
Copyright (c) 2016 Denis Mysenko
Copyright (c) 2018 Marcel Klehr

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
