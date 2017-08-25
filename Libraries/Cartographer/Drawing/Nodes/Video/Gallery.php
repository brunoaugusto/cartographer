<?php

/**
 * Video 'Gallery' Node Tag | Cartographer\Tags\Video\Gallery.php
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
 * Describes a Video Gallery with the `<video:gallery_loc>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\Validators\URL,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Gallery extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(

            'video:gallery_loc',

            $this -> options -> data -> url,

            isset( $this -> options -> data -> title ) ?
                [ 'title' => $this -> options -> data -> title ] : []
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Gallery must have, obviously, a valid URL under entry 'url'.
     * An optional entry 'title' is also accepted but there's nothing
     * to validate about it
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = $this -> options -> data;

        if( ! isset( $data -> url ) ) {

            $this -> _error = 'Missing required entry \'url\'';

            return FALSE;
        }

        $validator = new URL( [ 'value' => $data -> url ] );

        if( ! $validator -> validate() ) {

            $this -> _error = 'Invalid Gallery URL';

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
     *  TRUE because 'Gallery' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}