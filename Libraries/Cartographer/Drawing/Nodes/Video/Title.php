<?php

/**
 * Video 'Title' Node Tag | Cartographer\Drawing\Nodes\Video\Title.php
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
 * Describes a Video Title with the `<video:title>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Title extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        // Regarding the content, we'll surround it with a CDATA Block

        $pen -> addChildren(

            'video:title',

            $this -> options -> data, NULL, TRUE
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag
     * A simple string with the Video Title.
     *
     * @return boolean
     *  Always TRUE. There's nothing to validate as for Video Title
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
     *  TRUE because 'Title' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}