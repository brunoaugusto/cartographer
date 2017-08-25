<?php

/**
 * Video 'Family Friendliness' Node Tag | Cartographer\Tags\Video\FamilyFriendliness.php
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
 * Describes a Video Family Friendliness with the `<video:family_friendly>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class FamilyFriendliness extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:family_friendly', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Family Friendliness only accepts 'no' as value, describing
     * the video is not Family Friendly.
     * At example of Google Search this mean the 'SafeSearch Filter'
     * should be disabled or the Video won't appear
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = strtolower(
            preg_replace('/[^\00-\255]+/u', '', $this -> options -> data )
        );

        if( $data != 'no' ) {

            $this -> _error = 'Family Friendliness Tag value must be \'no\' (without quotes) ONLY when applied';

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
     *  TRUE because 'Family Friendliness' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}