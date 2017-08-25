<?php

/**
 * Standard Data-source Provider Class | Cartographer\Providers\Video\Standard.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Providers\Video;

use Next\Components\Debug\Exception;    # Exception Class
use Next\Components\Object;             # Object Class

use Cartographer\Drawing\Drawable;      # Drawable Interface
use Cartographer\Drawing\Pens\Pen;      # Drawing Pen Interface
use Cartographer\Providers\Provider;    # Data-source Provider Interface

/**
 * Defines a Standard Data-source Provider, parsing an input PHP array
 * and building the Sitemap Nodes' Composite to be drawn
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Debug\Exception, Next\Components\Object,
 *             \Cartographer\Drawing\Drawable,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Providers\Provider,
 *             \Cartographer\Drawing\Nodes\Common\URLSet,
 *             \Cartographer\Drawing\Nodes\Video\Video,
 *             \Cartographer\Drawing\Nodes\Video\Title,
 *             \Cartographer\Drawing\Nodes\Video\Description,
 *             \Cartographer\Drawing\Nodes\Video\Thumbnail,
 *             \Cartographer\Drawing\Nodes\Video\ContentLocation,
 *             \Cartographer\Drawing\Nodes\Video\PlayerLocation,
 *             \Cartographer\Drawing\Nodes\Video\Duration,
 *             \Cartographer\Drawing\Nodes\Video\ExpirationDate,
 *             \Cartographer\Drawing\Nodes\Video\Rating,
 *             \Cartographer\Drawing\Nodes\Video\Views,
 *             \Cartographer\Drawing\Nodes\Video\PublishingDate,
 *             \Cartographer\Drawing\Nodes\Video\FamilyFriendliness,
 *             \Cartographer\Drawing\Nodes\Video\Tags,
 *             \Cartographer\Drawing\Nodes\Video\Category,
 *             \Cartographer\Drawing\Nodes\Video\CountryRestriction,
 *             \Cartographer\Drawing\Nodes\Video\Gallery,
 *             \Cartographer\Drawing\Nodes\Video\Price,
 *             \Cartographer\Drawing\Nodes\Video\SubscriptionRequirement,
 *             \Cartographer\Drawing\Nodes\Video\Uploader,
 *             \Cartographer\Drawing\Nodes\Video\Platform,
 *             \Cartographer\Drawing\Nodes\Video\Live,
 *             \Cartographer\Drawing\Nodes\Common\URL,
 *             \Cartographer\Drawing\Nodes\Common\Page
 */
class Standard extends Object implements Provider, Drawable {

    /**
     * Valid and filtered Data-source with at least the Required Fields
     *
     * @var array $data
     */
    protected $data = [];

    /**
     * Additional Initialization.
     * Triggers Data filtering and validates Provider's Integrity
     */
    public function init() {
        $this -> checkIntegrity();
    }

    /**
     * Draws the Sitemap Nodes using given Drawing Pen based
     * on filtered Data-source
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $composite = new \Cartographer\Drawing\Nodes\Common\URLSet;

        foreach( $this -> data as $source ) {

            $video = new \Cartographer\Drawing\Nodes\Video\Video;

                // Video Title

            $node = new \Cartographer\Drawing\Nodes\Video\Title(
                [ 'data' => $source['title'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Description

            $node = new \Cartographer\Drawing\Nodes\Video\Description(
                [ 'data' => $source['description'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Thumbnail

            $node = new \Cartographer\Drawing\Nodes\Video\Thumbnail(
                [ 'data' => $source['thumbnail'] ]
            );

            ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                $this -> _error[] = $node -> getErrorMessage();

                // Video Content Location URL

            if( array_key_exists( 'contentLocation', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\ContentLocation(

                    [
                      'data' => $source['contentLocation'],

                      /**
                       * @internal
                       *
                       * Content Location URL (i.e Video File) can't
                       * be the same as the Landing Page
                       */
                      'page' => $source['page'],

                      /**
                       * @internal
                       *
                       * If provided, Content Location URL also can't
                       * be the same as the Video Embeddable Player
                       */
                      'playerLocation' => ( array_key_exists( 'playerLocation', $source ) ?
                                                $source['playerLocation'] : NULL )
                    ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Embeddable Player URL

            if( array_key_exists( 'playerLocation', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\PlayerLocation(

                    [
                      'data' => $source['playerLocation'],

                      /**
                       * @internal
                       *
                       * Embeddable Player Location URL can't be the
                       * same as the Landing Page
                       */
                      'page' => $source['page']
                    ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Duration

            if( array_key_exists( 'duration', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Duration(
                    [ 'data' => $source['duration'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Expiration Date

            if( array_key_exists( 'expirationDate', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\ExpirationDate(
                    [ 'data' => $source['expirationDate'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Rating

            if( array_key_exists( 'rating', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Rating(
                    [ 'data' => $source['rating'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Number of Views

            if( array_key_exists( 'views', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Views(
                    [ 'data' => $source['views'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Publishing Date

            if( array_key_exists( 'publishingDate', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\PublishingDate(
                    [ 'data' => $source['publishingDate'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Family Friendly Flag

            if( array_key_exists( 'familyFriendly', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\FamilyFriendliness(
                    [ 'data' => $source['familyFriendly'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Tags

            if( array_key_exists( 'tags', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Tags(
                    [ 'data' => $source['tags'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Category

            if( array_key_exists( 'category', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Category(
                    [ 'data' => $source['category'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Country Restriction

            if( array_key_exists( 'countryRestriction', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\CountryRestriction(
                    [ 'data' => $source['countryRestriction'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Gallery

            if( array_key_exists( 'gallery', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Gallery(
                    [ 'data' => $source['gallery'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Video Price(s)

            if( array_key_exists( 'price', $source ) ) {

                foreach( $source['price'] as $price ) {

                    $node = new \Cartographer\Drawing\Nodes\Video\Price(
                        [ 'data' => $price ]
                    );

                    ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                        $this -> _error[] = $node -> getErrorMessage();
                }
            }

                // Subscription Requirement

            if( array_key_exists( 'requiresSubscription', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\SubscriptionRequirement(
                    [ 'data' => $source['requiresSubscription'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Uploader Name

            if( array_key_exists( 'uploader', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Uploader(
                    [ 'data' => $source['uploader'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Playback Platforms

            if( array_key_exists( 'platform', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Platform(
                    [ 'data' => $source['platform'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

                // Live Stream Flag

            if( array_key_exists( 'live', $source ) ) {

                $node = new \Cartographer\Drawing\Nodes\Video\Live(
                    [ 'data' => $source['live'] ]
                );

                ( $node -> validate() != FALSE ) ? $video -> add( $node ) :
                    $this -> _error[] = $node -> getErrorMessage();
            }

            // Add everything under a `<url>` Node

            $url = new \Cartographer\Drawing\Nodes\Common\URL;
            $url -> add( new \Cartographer\Drawing\Nodes\Common\Page( [ 'data' => $source['page'] ] ) )
                 -> add( $video );

            $composite -> add( $url );
        }

        $composite -> draw( $pen );
    }

    // Filterable Interface Method Implementation

    /**
     * Filters the Object
     */
    public function filter() {

        $valid = [
                   'title', 'description', 'page', 'thumbnail',
                   'contentLocation', 'playerLocation', 'duration',
                   'expirationDate', 'rating', 'views', 'price',
                   'publishingDate', 'familyFriendly', 'tags', 'live',
                   'category', 'gallery', 'countryRestriction',
                   'requiresSubscription', 'uploader', 'platform'
                 ];

        foreach( $this -> options -> data as $source ) {

            // Required Fields

            if( ! array_key_exists( 'title',       $source ) ||
                ! array_key_exists( 'description', $source ) ||
                ! array_key_exists( 'page',        $source ) ||
                ! array_key_exists( 'thumbnail',   $source ) ) {

                continue;
            }

            /**
             * @internal
             *
             * Required Content Location / Embeddable Player Location
             *
             * At least one of them must be present
             */
            if( ! array_key_exists( 'contentLocation', $source ) &&
                ! array_key_exists( 'playerLocation',  $source ) ) {

                continue;
            }

            // Filtering out unknown Flags

            $source = array_filter(

                $source, function( $key ) use( $valid ) {

                    return ( in_array( $key, $valid ) );

                }, \ARRAY_FILTER_USE_KEY
            );

            if( count( $source ) > 0 ) {
                $this -> data[] = $source;
            }
        }
    }

    // Auxiliary Methods

    /**
     * Checks Provider's Integrity
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if filtered Data-source provided is empty
     */
    private function checkIntegrity() {

        if( count( $this -> data ) == 0 ) {

            throw new Exception(
                'No valid Video Sitemap Tags found', Exception::WRONG_USE
            );
        }
    }
}