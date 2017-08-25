<?php

/**
 * Video 'Country Restriction' Node Tag | Cartographer\Tags\Video\CountryRestriction.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Next\Validate\ISO\ISO3166;                  # ISO 3166-1 Validator

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Country Restriction with the `<video:restriction>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\ISO\ISO3166,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class CountryRestriction extends AbstractNode {

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
     * List of valid Country Codes
     *
     * @var array $countries
     */
    protected $countries = [];

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
         * CountryRestriction::validate() returns FALSE the Node Object
         * won't be added to the Composite structure anyways, so doesn't
         * matter if we use the current key of unfiltered/unvalidated input
         * data as Relationship rule
         */
        $rule = key( $this -> options -> data );

        $pen -> addChildren(

            'video:restriction',

            implode( ' ', array_map( 'trim', $this -> countries ) ),

            [ 'relationship' => $rule ]
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Country Restriction must be an associative
     * structure (i.e. array or overloaded object) with a leading
     * index 'allow' or 'deny' and, as for the value, either an array
     * or a comma-separated list of ISO 3166-2 Country Codes
     * in which the Video will have its playback authorized or not
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = $this -> options -> data;

        if( count( $data ) == 0 ) {

            $this -> _error = 'Empty Country Restriction Relationship definitions';

            return FALSE;
        }

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

            $this -> _error = 'Country Restriction Flag Relationship accepts only the values \'allow\' or \'deny\'';

            return FALSE;
        }

        $countriesList = array_filter(

            ( is_string( $data -> {$rule} ) ?
                preg_split( '/,\s+/', $data -> {$rule} ) :
                    $data -> {$rule} ),

            function( $country ) {

                $validator = new ISO3166([ 'value' => $country ]);

                return ( $validator -> validate() !== FALSE );
            }
        );

        if( count( $countriesList ) == 0 ) {

            $this -> error = 'No valid Country Codes available to create a Country Restriction Relationship';

            return FALSE;
        }

        $this -> countries = $countriesList;

        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Country Restriction' Tag describes a single content
     *  and thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}