<?php

/**
 * Video 'Rating' Node Tag | Cartographer\Tags\Video\Rating.php
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
 * Describes a Video Rating with the `<video:rating>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Rating extends AbstractNode {

    /**
     * Lowest Video Rating Value
     *
     * @var float
     */
    const LOWEST = 0.0;

    /**
     * Highest Video Rating Value
     *
     * @var float
     */
    const HIGHEST = 5.0;

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:rating', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Ratings must be a float value between 0.0 and 5.0
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = (float) $this -> options -> data;

        if( ( $data < self::LOWEST ) || ( $data > self::HIGHEST ) ) {

            $this -> _error = 'Video Ratings must be a float value between 0 and 5.0';

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
     *  TRUE because 'Rating' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}