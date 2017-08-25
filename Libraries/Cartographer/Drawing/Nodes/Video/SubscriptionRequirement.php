<?php

/**
 * Video 'Subscription Requirement' Node Tag | Cartographer\Tags\Video\SubscriptionRequirement.php
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
 * Describes a Video Subscription Requirement with the `<video:requires_subscription>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class SubscriptionRequirement extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:requires_subscription', strtolower( $this -> options -> data )
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Subscription Requirement Tag accepts either 'yes' or 'no' as
     * possible values indicating the Video requires some for of
     * subscription, either free or paid
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = strtolower(
            preg_replace('/[^\00-\255]+/u', '', $this -> options -> data )
        );

        if( $data != 'yes' && $data != 'no' ) {

            $this -> _error = 'Subscription Requirement Flag accepts only \'yes\' or \'no\' as possible values';

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
     *  TRUE because 'Subscription Requirement' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}