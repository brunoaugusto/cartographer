<?php

/**
 * Video 'Description' Node Tag | Cartographer\Tags\Video\Description.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Next\Components\Types\String;               # String Data-type Class

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Description with the `<video:description>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Types\String,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Description extends AbstractNode {

    /**
     * Maximum Video Description Length.
     * The real length is 2048, but we save 12 chars for the CDATA Block
     *
     * @var integer
     */
    const MAXIMUM_CHARACTERS = 2036;

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $data = ( $this -> options -> data instanceof String ?
                      $this -> options -> data :
                        new String( (string) $this -> options -> data ) );

        $pen -> addChildren(

            'video:description',

            // Regarding the content, we'll surround it with a CDATA Block

            $data -> truncate(
                self::MAXIMUM_CHARACTERS,  String::TRUNCATE_AFTER
            ) -> get(), NULL, TRUE
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag
     * A 2036 characters long or less serving a Video Description
     *
     * @return boolean
     *  Always TRUE. There's nothing to validate as for Video Description
     *  except of its maximum length but, if exceeding, we'll truncate
     *
     * @see Description::MAXIMUM_CHARACTERS
     *  For the reason of why 2036 instead of 2048
     */
    public function validate() {
        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Description' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}