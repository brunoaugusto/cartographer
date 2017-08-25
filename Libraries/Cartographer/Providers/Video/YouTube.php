<?php

/**
 * Video Data Provider: YouTube | Cartographer\Provider\Video\YouTube.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Providers\Video;

use Next\Components\Debug\Exception;    # Exception Class

use Next\Components\Object;             # Object Class
use Next\HTTP\Stream\Reader;            # HTTP Stream Reader Class
use Next\HTTP\Stream\Adapter\Socket;    # HTTP Socket Adapter Class

/**
 * Wilson Score Confidence Interval for a Bernoulli Parameter Class
 */
use Next\Math\Equations\Intervals\Wilson;

use Next\Validate\Validators\URL;       # URL Validator Class
use Next\Validate\ISO\ISO8601\Period;   # ISO 8601 Periods Validation Class

use Cartographer\Drawing\Drawable;      # Drawable Interface
use Cartographer\Providers\Provider;    # Data Provider Interface
use Cartographer\Drawing\Pens\Pen;      # Drawing Pen Interface

/**
 * Gets data from a YouTube URL providing a source for the Cartographer
 * build the Sitemap
 *
 * @package    Cartographer
 *
 * @uses       \DateInterval,
 *             \Next\Components\Object, \Next\Components\Debug\Exception,
 *             \Next\HTTP\Stream\Reader, \Next\HTTP\Stream\Adapter\Socket,
 *             \Next\Math\Equations\Intervals\Wilson,
 *             \Next\Validate\Validators\URL, \Next\Validate\ISO\ISO8601\Period,
 *             \Cartographer\Drawing\Drawable, Cartographer\Providers\Provider,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\Common\URLSet,
 *             \Cartographer\Drawing\Nodes\Common\URL,
 *             \Cartographer\Drawing\Nodes\Common\Page,
 *             \Cartographer\Drawing\Nodes\Video\Video,
 *             \Cartographer\Drawing\Nodes\Video\Title,
 *             \Cartographer\Drawing\Nodes\Video\Description,
 *             \Cartographer\Drawing\Nodes\Video\Thumbnail,
 *             \Cartographer\Drawing\Nodes\Video\PlayerLocation,
 *             \Cartographer\Drawing\Nodes\Video\Duration,
 *             \Cartographer\Drawing\Nodes\Video\Rating,
 *             \Cartographer\Drawing\Nodes\Video\PublishingDate,
 *             \Cartographer\Drawing\Nodes\Video\Tags,
 *             \Cartographer\Drawing\Nodes\Video\Uploader
 */
class YouTube extends Object implements Provider, Drawable {

    /**
     * YouTube URLs RegExp
     * Accepts only full URLs (i.e. inclusing the /watch?v=XXX) for the
     * domains youtube.com and youtu.be, both HTTP and HTTPS
     *
     * @var string
     */
    const YOUTUBE_URL_REGEXP = '(https?://)?(www\.)?(youtube\.com|youtu\.be)/watch\?v=';

    /**
     * YouTube API v3 URL for Videos
     *
     * @var string
     *
     * @see https://developers.google.com/youtube/v3/docs/videos
     */
    const YOUTUBE_API_VIDEOS_URL = 'https://www.googleapis.com/youtube/v3/videos';

    /**
     * YouTube Embeddable Player URL
     *
     * @var string
     */
    const YOUTUBE_EMBED_URL = 'https://www.youtube.com/embed';

    /**
     * YouTube Provider Default Options
     *
     * @var $array $defaultOptions
     */
    protected $defaultOptions = [

        /**
         * @internal
         *
         * Confidence Level for Wilson Score Interval.
         * Defaults to 0.95, meaning 95% of chance that the Videos
         * will have a positive reception
         */
        'confidence' => 0.95
    ];

    /**
     * Valid YouTube URLs to retrieve data from
     *
     * @var array $data
     */
    protected $data = [];

    /**
     * YouTube API Request Parts.
     *
     * Most of the essential informations are available by requesting
     * the 'snippet' part. That's mandatory and can't be removed.
     *
     * The 'snippet' Part has a Quota Cost of '3', one for the Request
     * itself and '2' for the Request Part
     *
     * However, more informations, for a slightly more complete Sitemap,
     * can be included by providing additional Request Parts at
     * expense of Quota Costs
     *
     * - The 'contentDetails' Part currently provides the
     *   'Video Duration' and has an additional Quota Cost of '2'
     *
     * - The 'statistics' Part currently provides 'Views Counter'
     *   and 'Video Rating' and has an additional Quota Cost of '2'
     *
     * @var array $parts
     *
     * @see https://developers.google.com/youtube/v3/docs/videos/list#part
     */
    protected $parts = [ 'snippet' ];

    /**
     * Additional Initialization.
     * Validates Provider's Integrity and merges additional
     * API Request Parts for more informations to build the Sitemap Nodes
     */
    public function init() {

        // Adding more API Request Parts

        if( isset( $this -> options -> APIExtraComponents ) ) {

            $extra = $this -> options -> APIExtraComponents;

            $this -> parts = array_merge(

                $this -> parts,

                ( is_array( $extra ) ? $extra : preg_split( '/,\s+/', $extra ) )
            );
        }

        $this -> parts = implode( ',', array_map( 'trim', array_unique( $this -> parts ) ) );

        $this -> checkIntegrity();
    }

    // Drawable Interface Method Implementation

    /**
     * Draws the Sitemap Nodes using given Drawing Pen based
     * on Data-source retrieved
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     *
     * @return \Cartographer\Drawing\Nodes\Node
     *  Sitemap Nodes Nodes
     */
    public function draw( Pen $pen ) {

        $composite = new \Cartographer\Drawing\Nodes\Common\URLSet;

        foreach( $this -> data as $url ) {

            parse_str( parse_url( $url, PHP_URL_QUERY ), $components );

            $meta = json_decode( $this -> getVideoData( $components['v'] ), TRUE );

            if( count( $meta ) == 0 ) continue;

            $snippet = $meta['items'][ 0 ]['snippet'];

            $video = new \Cartographer\Drawing\Nodes\Video\Video;

                // Video Title

            $node = new \Cartographer\Drawing\Nodes\Video\Title(
                [ 'data' => $snippet['title'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Description

            $node = new \Cartographer\Drawing\Nodes\Video\Description(
                [ 'data' => $snippet['description'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Thumbnail

            $node = new \Cartographer\Drawing\Nodes\Video\Thumbnail(
                [ 'data' => $snippet['thumbnails']['maxres']['url'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Embeddable Player URL

            $node = new \Cartographer\Drawing\Nodes\Video\PlayerLocation(

                [
                  'data' => sprintf( '%s/%s', self::YOUTUBE_EMBED_URL, $components['v'] ),

                  /**
                   * @internal
                   *
                   * Content Location URL (i.e Video File) can't
                   * be the same as the Landing Page
                   */
                  'page' => $url
                ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                /**
                 * Video Duration
                 *
                 * @internal
                 *
                 * YouTube API Provides Video Duration as ISO 8601 Period.
                 * It's an API consuming but we'll validate anyway.
                 *
                 * Also, this information is only present if 'contentDetails'
                 * is requested
                 */
            if( array_key_exists( 'contentDetails', $meta['items'][ 0 ] ) ) {

                $validator = new Period(
                    [
                      'value' => $meta['items'][ 0 ]['contentDetails']['duration']
                    ]
                );

                if( $validator -> validate() !== FALSE ) {

                    /**
                     * @internal
                     *
                     * DateInterval doesn't provide a native way of
                     * getting it formatted to the number of seconds
                     * so let's do the old-fashion way
                     */
                    $interval  = new \DateInterval(
                        $meta['items'][ 0 ]['contentDetails']['duration']
                    );

                    $seconds = array_sum(
                        [ $interval -> h * 3600, $interval -> i * 60, $interval -> s ]
                    );

                    $node = new \Cartographer\Drawing\Nodes\Video\Duration(
                        [ 'data' => $seconds ]
                    );

                    ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                        $this -> _error[] = $node -> getErrorMessage();
                }
            }

                // Rating

            /**
             * @internal
             *
             * Rather than simply make an average rating by dividing
             * the Number of Likes by the Total Number of Votes we'll
             * use of the Lower Bound of Wilson Score Confidence Interval
             * and get more realistic values
             */
            if( array_key_exists( 'statistics', $meta['items'][ 0 ] ) ) {

                $interval = new Wilson(

                    [
                        'positive' => $meta['items'][ 0 ]['statistics']['likeCount'],
                        'negative' => $meta['items'][ 0 ]['statistics']['dislikeCount'],
                        'confidence' => $this -> options -> confidence
                    ]
                );

                $lowerBound = number_format( $interval -> getLowerBound(), 3 );

                /**
                 * @internal
                 *
                 * After computing the Lower Bound we need to convert
                 * it in a more human-readable format: a 5-stars
                 */
                $rating = round( ( 5 * ( $lowerBound * 100 ) ) / 100 * 2 ) / 2;

                $node = new \Cartographer\Drawing\Nodes\Video\Rating(
                    [ 'data' => $rating ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Publishing Date

            $node = new \Cartographer\Drawing\Nodes\Video\PublishingDate(
                [ 'data' => $snippet['publishedAt'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Tags

            $node = new \Cartographer\Drawing\Nodes\Video\Tags(
                [ 'data' => $snippet['tags'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Uploader Name

            $node = new \Cartographer\Drawing\Nodes\Video\Uploader(
                [ 'data' => $snippet['channelTitle'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

            // Add everything under a `<url>` Node

            $node = new \Cartographer\Drawing\Nodes\Common\URL;
            $node -> add( new \Cartographer\Drawing\Nodes\Common\Page( [ 'data' => $url ] ) )
                  -> add( $video );

            $composite -> add( $node );
        }

        $composite -> draw( $pen );
    }

    // Filterable Interface Method Implementation

    /**
     * Filters provided URLs to valid and well-formed YouTube URLs
     */
    public function filter() {

        foreach( $this -> options -> urls as $url ) {

            $validator = new URL( [ 'value' => $url ] );

            // Invalid URLs

            if( ! $validator -> validate() ) continue;

            // Invalid YouTube URLs

            if( preg_match( sprintf( '#%s#', self::YOUTUBE_URL_REGEXP ), $url ) == 0 ) continue;

            $this -> data[] = $url;
        }
    }

    // Auxiliary Methods

    /**
     * Check Parameter Options Integrity.
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if defined Confidence Level used for Wilson Interval
     *  is set as zero or equal/greater to 1.
     *  This is a simply re-throw since this validation is also
     *  done by \Next\Math\Equations\Interval\AbstractInterval
     */
    private function checkIntegrity() {

        if( $this -> options -> confidence < 0 || $this -> options -> confidence >= 1 ) {

            throw new Exception(

                'Confidence Level for Wilson Score Interval Equation requires a positive float lower than 1.0 (i.e. 100%) representing the level of statistical confidence', Exception::WRONG_USE
            );
        }
    }

    /**
     * Retrieve Video Informations by consuming the YouTube API
     *
     * @param string $videoID
     *  YouTube Video ID (i.e. the expression after '?v=')
     *
     * @return string
     *  JSON string with Response Data
     */
    private function getVideoData( $videoID ) {

        $reader = new Reader(

            new Socket(

                sprintf(

                    '%s?id=%s&part=%s&key=%s',

                    self::YOUTUBE_API_VIDEOS_URL, $videoID,

                    $this -> parts, $this -> options -> key
                ),

                Socket::READ
            )
        );

        return $reader -> readAll();
    }
}