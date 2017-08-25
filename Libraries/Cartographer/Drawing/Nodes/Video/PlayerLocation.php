<?php

/**
 * Video 'Video Player / Embeddable Content' Node Tag | Cartographer\Tags\Video\PlayerLocation.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes\Video;

use Next\Validate\Validators\URL;               # URL Validator Class

use Cartographer\Drawing\Pens\Pen;              # Drawing Pen Interface
use Cartographer\Drawing\Nodes\AbstractNode;    # Abstract Node Class

/**
 * Describes a Video Player / Embeddable Content with the `<video:player_loc>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\Validators\URL,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class PlayerLocation extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:player_loc', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Embeddable Player must, obviously, be a valid URL but also
     * different of the so called `Landing Page` (`<loc></loc>`)
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = preg_replace('/[^\00-\255]+/u', '', $this -> options -> data );
        $page = preg_replace('/[^\00-\255]+/u', '', $this -> options -> page );

        if( $data == $page ) {

            $this -> _error = 'Content Location URL must not be the same of Landing Page';

            return FALSE;
        }

        $validator = new URL( [ 'value' => $data ] );

        if( ! $validator -> validate() ) {

            $this -> _error = 'Invalid Content Location URL';

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
     *  TRUE because 'Video Player / Embeddable Content' Tag describes
     *  a single content and thus doesn't need any other
     *  Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}