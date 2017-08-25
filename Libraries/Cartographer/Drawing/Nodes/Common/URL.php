<?php

/**
 * URL Node Tag | Cartographer\Drawing\Nodes\Common\URL.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Common;

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a URL with the `<url>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class URL extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addParent( 'url' );

        // Drawing all Children Nodes

        $this -> drawChildren( $pen );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {
        // Nothing
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  FALSE because URL Tag groups data for each URL, so it will have
     *  other Children Nodes
     */
    public function isLeaf() {
        return FALSE;
    }
}