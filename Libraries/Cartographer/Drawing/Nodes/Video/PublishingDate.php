<?php

/**
 * Video 'Publishing Date' Node Tag | Cartographer\Tags\Video\PublishingDate.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Next\Validate\Validators\DateTime;          # W3C DateTime Validator

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Publishing Date with the `<video:publication_date>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\Validators\DateTime,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class PublishingDate extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:publication_date', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * A W3C DateTime compliant representing when the Video has
     * been published
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     *
     * @link https://www.w3.org/TR/NOTE-datetime
     */
    public function validate() {

        $validator = new DateTime(
            [ 'value' => $this -> options -> data ]
        );

        if( ! $validator -> validate() ) {

            $this -> _error = 'Publishing Date is not in a valid W3C DateTime Format';

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
     *  TRUE because 'Publishing Date' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}