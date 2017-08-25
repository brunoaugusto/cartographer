<?php

/**
 * Video Informations Node Tag | Cartographer\Tags\Video\Video.php
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
 * Encloses all informations about the Video with the `<video:video>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Video extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addParent( 'video:video' );

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
     *  FALSE because Video Informations Tag encloses all individual
     *  informations described individually by each of its children
     */
    public function isLeaf() {
        return FALSE;
    }
}