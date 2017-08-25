<?php

/**
 * Video 'Duration' Node Tag | Cartographer\Tags\Video\Duration.php
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
 * Describes a Video Duration with the `<video:duration>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Duration extends AbstractNode {

    /**
     * Minimum Video Duration Value
     *
     * @var integer
     */
    const MINIMUM = 0;

    /**
     * Maximum Video Duration Value
     *
     * @var integer
     */
    const MAXIMUM = 28800;

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:duration', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Duration must be an integer value representing its length
     * in seconds, between 0 (zero) and 28800 (8h)
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = (float) $this -> options -> data;

        if( ( $data < self::MINIMUM ) || ( $data > self::MAXIMUM ) ) {

            $this -> _error = 'Video Durations must be an integer value between zero and 28800 (8h)';

            return FALSE;
        }

        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Duration' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}