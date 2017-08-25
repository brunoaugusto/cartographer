<?php

/**
 * Video 'Uploader' Node Tag | Cartographer\Tags\Video\Uploader.php
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
 * Describes a Video Uploader with the `<video:uploader>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\Validators\URL,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Uploader extends AbstractNode {

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(

            'video:uploader',

            $this -> options -> data -> name,

            isset( $this -> options -> data -> webpage ) ?
                [ 'webpage' => $this -> options -> data -> webpage ] : []
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Video Uploader must have at least an entry 'name' but also
     * accepts an optional entry 'webpage' with a valid URL with
     * more about the Uploader
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $data = $this -> options -> data;

        if( ! isset( $data -> name ) ) {

            $this -> _error = 'Missing required entry \'name\'';

            return FALSE;
        }

        if( isset( $data -> webpage ) ) {

            $validator = new URL( [ 'value' => $data -> webpage ] );

            if( ! $validator -> validate() ) {

                $this -> _error = 'Invalid Webpage URL';

                return FALSE;
            }
        }

        return TRUE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Uploader Tag' describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}