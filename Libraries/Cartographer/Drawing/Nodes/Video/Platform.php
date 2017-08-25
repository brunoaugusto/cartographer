<?php

/**
 * Video 'Platform Restriction' Node Tag | Cartographer\Tags\Video\Platform.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Platform Restriction with the `<video:platform>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Platform extends AbstractNode {

    /**
     * Flag value describing the Video is available only Allowed
     * in Countries listed
     *
     * @var string
     */
    const FLAG_VALUE_ALLOW  = 'allow';

    /**
     * Flag value describing the Video is not available on Denied
     * Countries listed
     *
     * @var string
     */
    const FLAG_VALUE_DENY   = 'deny';

    /**
     * List of Valid Platforms
     *
     * @var array
     */
    const VALID_PLATFORMS = [ 'mobile', 'tv', 'web' ];

    /**
     * List of valid Platforms
     *
     * @var array $platforms
     */
    protected $platforms = [];

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        /**
         * @internal
         *
         * We won't be using input data to create the Node and if
         * Platform::validate() returns FALSE the Node Object
         * won't be added to the Composite structure anyways, so doesn't
         * matter if we use the current key of unfiltered/unvalidated input
         * data as Relationship rule
         */
        $rule = key( $this -> options -> data );

        $pen -> addChildren(

            'video:platform',

            implode( ' ', array_map( 'trim', $this -> platforms ) ),

            [ 'relationship' => $rule ]
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Platform Restriction must be an associative
     * structure (i.e. array or overloaded object) with a leading
     * index 'allow' or 'deny' and, as for the value, either an array
     * or a comma-separated list of Platforms in which the Video
     * will have its playback authorized or not.
     * Valid platforms are 'mobile', 'tv' and 'web'
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = $this -> options -> data;

        /**
         * @internal
         *
         * Only one rule may exist for this tag definition,
         * 'allow' OR 'deny', so we work with the first one only
         */
        $rule = strtolower(
            preg_replace( '/[^\00-\255]+/u', '', key( $data ) )
        );

        // Invalid Rule

        if( $rule != self::FLAG_VALUE_ALLOW && $rule != self::FLAG_VALUE_DENY ) {

            $this -> _error = 'Platforms Flag Relationship accepts only the values \'allow\' or \'deny\'';

            return FALSE;
        }

        $platforms = ( is_string( $this -> options -> data -> {$rule} ) ?
                           preg_split( '/,\s+/', $this -> options -> data -> {$rule} ) :
                               $this -> options -> data -> {$rule} );

        $platforms = array_filter( $platforms, function( $platform ) {
            return ( in_array( strtolower( $platform ), self::VALID_PLATFORMS ) );
        });

        if( count( $platforms ) == 0 ) {

            $this -> _error = 'No valid Denied Platforms informed';

            return FALSE;
        }

        $this -> platforms = $platforms;

        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Platform Restriction' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}