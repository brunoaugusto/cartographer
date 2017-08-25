<?php

/**
 * Video 'Category' Node Tag | Cartographer\Tags\Video\Category.php
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
 * Describes a Video Category with the `<video:category>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Types\String,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Category extends AbstractNode {

    /**
     * Maximum Video Category Length
     *
     * @var integer
     */
    const MAXIMUM_LENGTH        = 256;

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

            'video:category',

            $data -> truncate( self::MAXIMUM_LENGTH,  String::TRUNCATE_AFTER ) -> get()
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * A simple string describing the Video Category
     *
     * @return boolean
     *  Always TRUE. There's nothing to validate as for a Video Category
     *  except of its maximum length but, if exceeding, we'll truncate
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
     *  TRUE because 'Category' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}