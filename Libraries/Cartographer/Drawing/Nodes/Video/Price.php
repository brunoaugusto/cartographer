<?php

/**
 * Video 'Price' Node Tag | Cartographer\Tags\Video\Price.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Next\Validate\ISO\ISO4217;                  # ISO 4217 Validator Class
use Next\Validate\Validators\Currency;          # Currency Validator Class

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Price(s) with the `<video:price>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\ISO\ISO4217, \Next\Validate\Validators\Currency,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Price extends AbstractNode {

    /**
     * Flag value describing the Video will belong to the User
     * after bought
     *
     * @var string
     */
    const FLAG_VALUE_OWN  = 'own';

    /**
     * Flag value describing the User will have access to the Video
     * for limited time
     *
     * @var string
     */
    const FLAG_VALUE_RENT = 'rent';

    /**
     * Flag value describing the Video bought/rented is HD
     *
     * @var string
     */
    const FLAG_VALUE_HD   = 'HD';

    /**
     * Flag value describing the Video bought/rented is
     * in SD (Standard Definition)
     *
     * @var string
     */
    const FLAG_VALUE_SD   = 'SD';

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        // Preparing Node Attributes

        $attributes['currency'] = $this -> options -> data -> currency;

        if( isset( $this -> options -> data -> type ) ) {
            $attributes['type'] = $this -> options -> data -> type;
        }

        if( isset( $this -> options -> data -> resolution ) ) {
            $attributes['resolution'] = $this -> options -> data -> resolution;
        }

        $pen -> addChildren(
            'video:price', $this -> options -> data -> price, $attributes
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Price(s) must have, obviously, the 'price' itself but
     * also a required and valid ISO 4217 Country Currency Code.
     * They also accept two optional arguments:
     * - 'type' of purchase, if 'own' or 'rent', meaning, respectively,
     *   the user is buying-off the Video or just renting to watching for a limited time
     * - 'resolution' of the Video, 'HD' for High Definition and
     *   'SD' for Standard Definition
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        if( ! isset( $this -> options -> data -> price ) ) {

            $this -> _error = 'Missing required entry \'price\'';

            return FALSE;
        }

        if( ! isset( $this -> options -> data -> currency ) ) {

            $this -> _error = 'Missing required entry \'currency\'';

            return FALSE;
        }

        $validator = new ISO4217(
            [ 'value' => $this -> options -> data -> currency ]
        );

        if( ! $validator -> validate() ) {

            $this -> _error = sprintf(

                '\'%s\' is not a valid ISO 4217 Country Currency Code',

                $this -> options -> data -> currency
            );

            return FALSE;
        }

        $validator = new Currency(

            [
              /**
               * @internal
               *
               * For the purpose of Video Prices negative values
               * and zeros are not allowed, after all the User
               * can't buy the Video for a negative amount and
               * 'zero' means 'free', which defeats the purpose
               * of Price Flag
               */
              'allowNegative'   => FALSE,
              'allowZero'       => FALSE,

              'value'           => $this -> options -> data -> price
            ]
        );

        if( ! $validator -> validate() ) {

            $this -> _error = sprintf(

                '\'%s\' is not a valid Video Currency',

                $this -> options -> data -> currency
            );

            return FALSE;
        }

        // Optional Flags

        if( isset( $this -> options -> data -> type ) ) {

            $type = strtolower(
                preg_replace('/[^\00-\255]+/u', '', $this -> options -> data -> type )
            );

            if( $this -> options -> data -> type != self::FLAG_VALUE_OWN &&
                  $this -> options -> data -> type != self::FLAG_VALUE_RENT ) {

                $this -> _error = 'Video Price(s) \'type\' must be \'own\' to represent buy-off purchases or \'rent\' for Video with an expiring subscription';

                return FALSE;
            }
        }

        if( isset( $this -> options -> data -> resolution ) ) {

            $data = strtoupper(
                preg_replace('/[^\00-\255]+/u', '', $this -> options -> data -> resolution )
            );

            if( $this -> options -> data -> resolution != self::FLAG_VALUE_HD &&
                  $this -> options -> data -> resolution != self::FLAG_VALUE_SD ) {

                $this -> _error = 'Video Price(s) \'resolution\' must be \'HD\' or \'SD\' for, respectively, High Definition or Standard Definition Videos';

                return FALSE;
            }
        }

        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Price' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}