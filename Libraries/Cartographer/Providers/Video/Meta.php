<?php

/**
 * Video Data Provider: Meta Tags | Cartographer\Provider\Video\Meta.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Providers\Video;

use Next\Components\Debug\Exception;    # Exception Class

use Next\Components\Object;             # Object Class

use Next\Validate\Validators\URL;       # URL Validator Class
use Next\Validate\ISO\ISO8601\Period;   # ISO 8601 Periods Validation Class

use Cartographer\Drawing\Drawable;      # Drawable Interface
use Cartographer\Providers\Provider;    # Data Provider Interface
use Cartographer\Drawing\Pens\Pen;      # Drawing Pen Interface

/**
 * Gets data from any valid URL looking out for (some of) Meta Tags
 * for Videos as described by Schema.org VideoObject providing a source
 * for the Cartographer build the Sitemap
 *
 * @package    Cartographer
 *
 * @uses       \DateInterval,
 *             \Next\Components\Debug\Exception, \Next\Components\Object,
 *             \Next\Validate\Validators\URL, \Next\Validate\ISO\ISO8601\Period,
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
class Meta extends Object implements Provider, Drawable {

    /**
     * Meta Tags Provider Default Options
     *
     * @var $array $defaultOptions
     */
    protected $defaultOptions = [

        /**
         * @internal
         *
         * Defines whether or not the Uploading Date is also the
         * Publishing Date. It's useful for sites created with
         * Static Site Generators that can't provide an automatic
         * Publishing Date, requiring it to be manually defined.
         * Defaults to FALSE
         */
        'uploadDateIsPublishingDate' => FALSE
    ];

    /**
     * Meta Tags we currently search from all those listed on
     * VideObject page at Schema.org.
     *
     * @var array $meta
     */
    protected $meta = [
      'name', 'description', 'thumbnailUrl', 'contentUrl',
      'embedUrl', 'duration', 'uploadDate', 'expires', 'datePublished',
      'keywords'
    ];

    /**
     * Valid URLs to retrieve data from
     *
     * @var array $data
     */
    protected $data = [];

    /**
     * Additional Initialization.
     * Rebuilds filtered structure to add (some of) VideoObject
     * Meta Tags and Validates Provider's Integrity
     */
    public function init() {

        foreach( $this -> data as $offset => $url ) {

            $meta = $this -> getMetaTags( $url );

            // No Meta Tags found

            if( count( $meta ) == 0 ) continue;

            // Missing the bare minimum tags

            if( ! array_key_exists( 'name', $meta ) ||
                ! array_key_exists( 'description', $meta ) ||
                ! array_key_exists( 'thumbnailUrl', $meta ) ) {

                continue;
            }

            /**
             * @internal
             *
             * Together or not, at least one of these two tags must exist
             */
            if( ( ! array_key_exists( 'contentUrl', $meta ) && ! array_key_exists( 'embedUrl', $meta ) ) ) {
                continue;
            }

            $this -> data[ $offset ] = [ 'url' => $url, 'meta' => $meta ];
        }

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

        foreach( $this -> data as $data ) {

            // No Meta Tags found

            if( ! array_key_exists( 'meta', $data ) || count( $data['meta'] ) == 0 ) {
                continue;
            }

            $video = new \Cartographer\Drawing\Nodes\Video\Video;

                /**
                 * @internal
                 *
                 * As for Schema.org VideoObject, 'name' is the Video Title
                 */
            $node = new \Cartographer\Drawing\Nodes\Video\Title(
                [ 'data' => $data['meta']['name'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Description

            $node = new \Cartographer\Drawing\Nodes\Video\Description(
                [ 'data' => $data['meta']['description'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Thumbnail

            $node = new \Cartographer\Drawing\Nodes\Video\Thumbnail(
                [ 'data' => $data['meta']['thumbnailUrl'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Content Location URL

            if( array_key_exists( 'contentUrl', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\ContentLocation(

                    [
                      'data' => $data['meta']['contentUrl'],

                      /**
                       * @internal
                       *
                       * Content Location URL (i.e Video File) can't
                       * be the same as the Landing Page
                       */
                      'page' => $data['url'],

                      /**
                       * @internal
                       *
                       * If provided, Content Location URL also can't
                       * be the same as the Video Embeddable Player
                       */
                      'playerLocation' => ( array_key_exists( 'embedUrl', $data['meta'] ) ?
                                                $data['meta']['embedUrl'] : NULL )
                    ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Embeddable Player URL

            if( array_key_exists( 'embedUrl', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\PlayerLocation(

                    [
                      'data' => $data['meta']['embedUrl'],

                      /**
                       * @internal
                       *
                       * Content Location URL (i.e Video File) can't
                       * be the same as the Landing Page
                       */
                      'page' => $data['url']
                    ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Duration

            if( array_key_exists( 'duration', $data['meta'] ) ) {

                $validator = new Period(
                    [
                      'value' => $data['meta']['duration']
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
                        $data['meta']['duration']
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

                // Expiration Date

            if( array_key_exists( 'expires', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\ExpirationDate(
                    [ 'data' => $data['meta']['expires'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Publishing Date

            if( array_key_exists( 'datePublished', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\PublishingDate(
                    [ 'data' => $data['meta']['datePublished'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Publishing Date

            if( array_key_exists( 'datePublished', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\PublishingDate(
                    [ 'data' => $data['meta']['datePublished'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();

            } else {

                if( $this -> options -> uploadDateIsPublishingDate !== FALSE &&
                    array_key_exists( 'uploadDate', $data['meta'] ) ) {

                    $node = new \Cartographer\Drawing\Nodes\Video\PublishingDate(
                        [ 'data' => $data['meta']['uploadDate'] ]
                    );

                    ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                        $this -> _error[] = $node -> getErrorMessage();
                }
            }

                // Video Tags

            if( array_key_exists( 'keywords', $data['meta'] ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Tags(
                    [ 'data' => $data['meta']['keywords'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

            // Add everything under a `<url>` Node

            $node = new \Cartographer\Drawing\Nodes\Common\URL;
            $node -> add( new \Cartographer\Drawing\Nodes\Common\Page( [ 'data' => $data['url'] ] ) )
                  -> add( $video );

            $composite -> add( $node );
        }

        $composite -> draw( $pen );
    }

    // Filterable Interface Method Implementation

    /**
     * Filters provided URLs to valid and well-formed URLs
     */
    public function filter() {

        foreach( $this -> options -> urls as $url ) {

            $validator = new URL( [ 'value' => $url ] );

            // Invalid URLs

            if( ! $validator -> validate() ) continue;

            $this -> data[] = $url;
        }
    }

    // Auxiliary Methods

    /**
     * Check Parameter Options Integrity.
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if, after filtered, data-source is empty
     */
    private function checkIntegrity() {

        if( count( $this -> data ) == 0 ) {

            throw new Exception(
                'No valid Video Sitemap URLs found', Exception::WRONG_USE
            );
        }
    }

    /**
     * Retrieve (some of) Meta Tags for Videos as described by
     * Schema.org VideoObject
     *
     * @param string $url
     *  URL to look out for Meta Tags
     *
     * @return string
     *  JSON string with Response Data
     *
     * @see http://schema.org/VideoObject
     */
    private function getMetaTags( $url ) {

        /**
         * @internal
         *
         * XmlReader is awfully complex for such a simple task and
         * DOMDocumment doesn't recognize HTML5 tags, so let's do the old way
         */
        preg_match_all(
           '/<meta[^>]+itemprop=["\'](?<name>[^>]+)["\'][^>]+content=["\'](?<value>[^>]+)["\']>/i',
           file_get_contents( $url ), $tags );

        /**
         * @internal
         *
         * Apparently there's a little difference between what Google
         * consider a valid implementation of Schema.org VideoObject Tags
         * regarding string capitalization.
         *
         * As described on their page "Schema.org for Videos" [1], tags
         * made of two different and distinguishable words (i.e. 'contentURL')
         * have the first word lowercased and the second uppercased
         *
         * But on Schema.org VideoObject page [2] we see that,
         * as for example of the same tag, the "right" is 'contentUrl',
         * with only the first letter of the second word capitalized
         *
         * Right or wrong, we'll normalize but without doing any magic
         *
         * @see https://developers.google.com/webmasters/videosearch/schema [1]
         * @see http://schema.org/VideoObject [2]
         */
        $names = array_map(

            function( $name ) {

                if( preg_match( '/([a-z]+)([A-Z]{2,})/', $name, $m ) > 0 ) {

                    return sprintf(
                        '%s%s', $m[ 1 ], ucfirst( strtolower( $m[ 2 ] ) )
                    );
                }

                return $name;

            }, $tags['name']
        );

        return ( count( $tags ) > 0 ) ?
            array_combine( $names, $tags['value'] ) : [];
    }
}