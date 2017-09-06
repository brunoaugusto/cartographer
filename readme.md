<p align="center">
  <img src="http://i.imgur.com/UO9ys5s.jpg" height="100" width="355" />
</p>

The Cartographer is PHP tool to generate Sitemaps created with [Next Framework][2]. Although easily expansible to allow the creation of any sort of Sitemap, the initial conception was the creation of [Video Sitemaps][1].

## Requirements

The Cartographer requires a webserver running at least *PHP 5.4*. Currently PHP 7 is **not** supported because it has some new reserved keywords that currently conflicts with Next's pseudo-strong type-hinting.

## Installation

Simply download the package or clone the Repository:

    git clone git@github.com:brunoaugusto/cartographer.git

## Usage

The Cartographer detects patterns in an environment described by a `Cartographer\Providers\Provider` Object and draws the Sitemap using `Cartographer\Drawing\Pens\Pen` and `Cartographer\Drawing\Papers\Paper` Objects, similarly to real-world old cartographic process.

> Being created over the shoulders of Next Framework, all Objects when children of `Next\Components\Object` can be configured by passing an associative array of arguments or a well-formed `Next\Components\Parameter` Object.

### ▸ Data Providers

Data Providers analyze different sources and build the Sitemap Node's Composite, a nested Collection of `Cartographer\Drawing\Nodes\Node`, which will be hierarchically rendered with the `Cartographer\Drawing\Pens\Pen` provided.

Currently there are 3 Data providers available:

#### [Cartographer\Providers\Provider\Standard][13]

 Data is retrieved from valid Sitemap Tags in a PHP array.<br />
 The Standard Provider requires a Parameter Option `data` with the data-source to be analyzed.

 Below an example of a valid Standard Provider manually configured with some tags for the first episode of the Animation Chaotic provided by [Chaotic Online Channel][3] on YouTube.

  ```php
  $standardProvider = new Cartographer\Providers\Video\Standard(
      [ 'data' =>
        [
          [
            'title' => 'Welcome to Chaotic (Part 1)',
            'description' => 'In the first episode of the series, the protagonist, Tom Majors, receives his access code for Chaotic, a place where, according to his best friend, Kaz, you can turn into the Creatures and play for real.

  Even skeptical of friend’s fantasies, Tom decides to take a chance and uses his code, and thus he finds out the wonderful world of Chaotic (or not).',
            'page' => 'http://chaoticonline.tk/videos/season-1/welcome-to-chaotic-part-1/',
            'playerLocation' => 'https://www.youtube.com/embed/3Bsj4BLE-eE?showinfo=0',
            'thumbnail'   => 'http://i.imgur.com/CSImexP.png',
            'duration' => 1170,
            'publishingDate' => '2016-08-18T08:56:00-03:00',
            'tags' => [ 'chaotic', 'tv-show', '2006', 'animation', 'adventure', 'lets-get-chaotic' ],
            'category' => 'Animation',
            'requiresSubscription' => 'no',
            'platform' => [ 'deny' => 'tv' ],
            'uploader' => [ 'name' => 'Chaotic Online', 'webpage' => 'http://www.youtube.com/c/ChaoticOnline' ]
          ]
        ]
      ]
  );
  ````

  And here's a full list of all recognized Video Tags:

   - `title` *(string)*<br />
    The Video Title
   - `description` *(string)*<br />
    A Description for the Video with a maximum of 2036 characters<br />
    If the string provided here exceeds this limit, it will be smartly truncated

      > This very specific length is due the fact HTML entities must be properly converted -OR- wrapped into a [CDATA Block][4], which has a total length of 12 characters :wink:

   - `page` *(string)*<br />
   The also called *Landing Page* where the User will be redirected when clicking the link on Search Results
   - `thumbnail` *(string)*<br />
   A Thumbnail with dimensions of at least 160x90 and at most 1920x1080 pixels
   - `contentLocation` *(string)*<br />
   An absolute URL pointing to the video file. E.g: http://www.site.com/path/to/video.mp4<br />
   This entry can coexist with the `playerLocation` (see below) and if it does they can't point to the same URL

     > This URL **must not** be the same of the `page` value mentioned above

   - `playerLocation` (string)<br />
   An absolute URL pointing to the playback page or where an embeddable Player is located

     > This URL **must not** be the same of the `page` value mentioned above

   - `duration` *(integer)*<br />
   The Video Duration in seconds<br />
   Must not be negative (obviously) nor exceed the limit of 28800 seconds (8h)
   - `expirationDate` *(string)*<br />
   A full [W3C DateTime][5] compliant date/time from which the Video won't be available anymore
   - `rating` *(float)*<br />
   A value varying from 0.0 and up to 5.0 representing the Video Rating
   - `views` *(integer)*<br />
   The number of views the Video has
   - `price` *(array)*<br />
   An associative array describing the price to view/download the Video.<br />
   Accepted values for its inner Parameter Options are:
     - `price` (string)<br />
     The price itself (required)
     - `currency` (string)<br />
     An [ISO 4217][6] compliant with the Country Code of described Price (required)
     - `type` (string)<br />
       - Defined as `own` means the User would be buying-off the Video
       - Defined as `rent` means the User is just "renting" the Video for watching
     - `resolution` *(string)*<br />
       - Defined as `HD` means the Video is in High Definition
       - Defined as `SD` means the Video is in Standard Definition
   - `publishingDate` *(string)*<br />
   A full [W3C DateTime][5] compliant date/time describing when the Video has been published
   - `familyFriendly` *(string)*<br />
   Defined as `no` informs the webcrawlers the Video is **not** Family Friendly<br />
   At example of Google, this means that for this Video to appear in the search results the *SafeSearch* filter must be turned off

     > By default all Videos are considered Family Friendly and thus it's not needed (or possible) to set this as `yes`

   - `tags` *(array|string)*<br />
   A list of tags describing the Video.<br />
   It can be a an array of tags or a comma-separated string
   - `live` *(string)*<br />
   Specifies if the Video is a Live Stream. Accepted values are `yes` or `no`
   - `category` *(string)*<br />
   A Category for the Video with a maximum of 256 characters<br />
   If the string provided here exceeds this limit, it will be smartly truncated
   - `gallery` *(array)*<br />
   Describes a Gallery for the Video<br />
   If defined must contain an `url` with a valid URL of the Gallery and, optionally, a `title` entry with a Title for it
   - `countryRestriction` *(array)*<br />
   An associative array with its key describing the *Relationship Rule* (`alow` or `deny`) and an array or a comma-separated list of [ISO 3166][7] Country Codes as value.<br />

     > Only the first *Relationship* defined will be taken into account!

   - `requiresSubscription` *(string)*<br />
   Describes if the Video requires an either paid or free subscription to be played/downloaded.<br />
   Accepted values are `yes` and `no`
   - `uploader` *(array)*<br />
   Describes the person/company who uploaded the Video<br />
   If defined must contain an entry `name` with the name of the Uploader and, optionally, a `webpage` with a valid URL where to get more informations about the Uploader
   - `platform` *(array)*<br />
   An associative array with its key describing the *Relationship Rule* (`alow` or `deny`) and an array or a comma-separated list of platforms in which the video will be allowed/denied to be played<br />
   Valid Platforms are: `mobile`, `tv` and `web`

 > In order to be considered as valid an entry **must** have at least a `Title`, a `Description`, a `Thumbnail` and a `Content Location` -OR a `Player Location`

#### [Cartographer\Providers\Provider\YouTube][14]

 Data is retrieved by consuming the YouTube Video API<br />
 The YouTube Provider requires a Parameter Option `urls` with YouTube URLs for the API to work with and a `key` with a [Google Developers Credential][8]. E.g:

  ````php
  $youtubeProvider = new Cartographer\Providers\Video\YouTube(
      [
        'key' => 'XXX',
        'urls' =>
          [
            'https://www.youtube.com/watch?v=vKIfaS37ufE', // Season 2, Episode #1
            'http://vimeo.com/132146410',
            'https://www.youtube.com/watch?v=IHdbWeoJfl8'  // Season 3, Episode #1
          ]
      ]
  );
  ````

Additionally, the YouTube Provider also accepts a third Parameter Option called `APIExtraComponents` through which more [YouTube API Parts][9] can be consumed bringing more data for a slightly more complete Sitemap

The most basic informations are available by requesting the `snippet` Part, which is the bare minimum so cannot be removed, but at expense of Quota Costs, the following Parts can be requested as well:

   - The `contentDetails` Part currently provides the
     **Video Duration** and has an additional Quota Cost of **2**

   - The `statistics` Part currently provides **Views Counter** and **Video Rating** and has an additional Quota Cost of **2**

   If the `statistics` Part is provided, because YouTube doesn't provide a straight to the point rating (0 to 5.0) we'll compute one based on the Total Number of Votes, Likes and Dislikes using [Wilson Score Confidence Interval for a Bernoulli Parameter][10]<br />

   This algorithm accepts a Parameter Option `confidence` which, for the purpose of a Video, means the statistical probability of the Video being liked<br />
   By default, a float value of `0.95` is set, meaning a chance of 95% of the Video being liked<br />
   This value is also configurable through YouTube Provider and **must** be a positive float value lower than 1 (i.e. 100%)

#### [Cartographer\Providers\Provider\Meta][15]

 Data is retrieved from *some* of [VideoObject][3] Meta Tags from any valid URL defined under the required Parameter Option `urls`<br />
 Currently the following Meta Tags are recognized:

   - `name` *(string)*<br />
   The Video Title
   - `description` *(string)*<br />
   A Description for the Video with a maximum of 2036 characters.<br />
   If the string provided here exceeds this limit, it will be smartly truncated

      > This very specific length is due the fact HTML entities must be properly converted -OR- wrapped into a [CDATA Block][4], which has a total length of 12 characters :wink:

   - `thumbnailUrl` *(string)*<br />
   A Thumbnail with dimensions of at least 160x90 and at most 1920x1080 pixels
   - `contentUrl` *(string)*<br />
   An absolute URL pointing to the video file. E.g: http://www.site.com/path/to/video.mp4<br />
   This entry can coexist with the `playerLocation` (see below) and if it does they can't point to the same URL

     > This URL **must not** be the same of the `page` value mentioned above

   This entry can coexist with the `embedUrl` (see below) and if it does they can't point to the same URL
   - `embedUrl` *(string)*<br />
   An absolute URL pointing to the playback page or where an embeddable Player is located

     > This URL **must not** be the same of the `page` value mentioned above

   - `duration` *(string)*<br />
   The Video Duration in seconds<br />
   Must not be negative (obviously) nor exceed the limit of 28800 seconds (8h)
   - `uploadDate` *(string)*<br />
   A full [W3C DateTime][5] compliant date/time in which the Video has been uploaded
   - `expires` *(string)*<br />
   A full [W3C DateTime][5] compliant date/time in which the Video won't be available anymore
   - `datePublished` *(string)*<br />
   A full [W3C DateTime][5] compliant date/time describing when the Video has been published
   - `keywords` *(string)*<br />
   A list of tags describing the Video.<br />
   It can be a an array of tags or a comma-separated string

Depending on the platform the resulting sitemap will be used to publish the Video, sometimes the **Publishing Date** may not be available.<br />

Because of this the Meta Provider accepts an additional Parameter Option named `uploadDateIsPublishingDate` that allows the Upload Date, if available, to be used as Publishing Date

### ▸ Drawing Pens

#### [Cartographer\Drawing\Pens\XML][16]

 There's currently only Drawing Pen available that, very suggestively, provides a way for the Sitemap Nodes' Composite to be rendered as a XML structure.

### ▸ Drawing Papers

 And, last but not least, two `Cartographer\Drawing\Papers\Paper` Objects:

#### [Cartographer\Drawing\Papers\Response][17]

The Response Paper Object outputs the content directly to the Browser with the proper HTTP Header Fields (if possible)

#### [Cartographer\Drawing\Papers\File][18]

The File Paper Object writes down the content to a file<br />
It requires a Parameter Option `destination` with a valid directory where the file will be created and accepts an optional Parameter `filename` if the filename needs to be changed

## Example

````php

use Next\Components\Debug\Exception;

try {

    $metaProvider = new Cartographer\Providers\Video\Meta(
      [
        'uploadDateIsPublishingDate' => TRUE,
        'urls' =>
        [
          'http://chaoticonline.tk/videos/season-1/welcome-to-chaotic-part-1/'
        ],
      ]
    )

    $cartographer = new Cartographer\Cartographer(
        [
          'provider' => $metaProvider,
          'pen'      => new Cartographer\Drawing\Pens\XML,
          'paper'    => new Cartographer\Drawing\Papers\Response
        ]
    );

    if( $cartographer -> publish() ) {
        echo 'Sitemap generated successfully';
    }

} catch( Exception $e ) {

    echo 'Oh snap! ', $e -> getMessage();
}
````

## License

The Cartographer is distributed under the [GNU Affero General Public License 3.0][11].<br />

If you don't know what this means, [here you go][12]

[1]: https://developers.google.com/webmasters/videosearch/sitemaps
[2]: https://github.com/nextframework/next
[3]: https://www.youtube.com/c/ChaoticOnline
[4]: http://wikipedia.org/wiki/CDATA
[5]: http://www.w3.org/TR/NOTE-datetime
[6]: https://en.wikipedia.org/wiki/ISO_4217#Active_codes
[7]: https://pt.wikipedia.org/wiki/ISO_3166-1
[8]: https://console.developers.google.com/apis/dashboard
[9]: https://developers.google.com/youtube/v3/docs/videos/list#part
[10]: evanmiller.org/how-not-to-sort-by-average-rating.html
[11]: http://www.gnu.org/licenses/agpl-3.0.txt
[12]: https://tldrlegal.com/license/gnu-affero-general-public-license-v3-(agpl-3.0)
[13]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Providers/Video/Standard.php
[14]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Providers/Video/YouTube.php
[15]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Providers/Video/Meta.php
[16]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Drawing/Pens/XML.php
[17]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Drawing/Papers/Response.php
[18]: https://github.com/brunoaugusto/cartographer/tree/master/Libraries/Cartographer/Drawing/Papers/File.php