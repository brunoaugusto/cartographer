<?php

/**
 * Video 'Thumbnail' Node Tag | Cartographer\Tags\Video\Thumbnail.php
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
 * Describes a Video Thumbnail with the `<video:thumbnail_loc>` Tag
 *
 * @package    Cartographer
 *
 * @uses       \Next\Validate\Validators\URL,
 *             \Cartographer\Drawing\Pens\Pen,
 *             \Cartographer\Drawing\Nodes\AbstractNode
 */
class Thumbnail extends AbstractNode {

    /**
     * Flag describing the Minimum Width for a Video Thumbnail
     *
     * @var integer
     */
    const MINIMUM_THUMBNAIL_WIDTH  = 160;

    /**
     * Flag describing the Maximum Width for a Video Thumbnail
     *
     * @var integer
     */
    const MAXIMUM_THUMBNAIL_WIDTH  = 1920;

    /**
     * Flag describing the Minimum Height for a Video Thumbnail
     *
     * @var integer
     */
    const MINIMUM_THUMBNAIL_HEIGHT = 90;

    /**
     * Flag describing the Maximum Height for a Video Thumbnail
     *
     * @var integer
     */
    const MAXIMUM_THUMBNAIL_HEIGHT = 1080;

    // Drawable Interface Method Implementation

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen ) {

        $pen -> addChildren(
            'video:thumbnail_loc', $this -> options -> data
        );
    }

    // Validatable Interface Method Implementation

    /**
     * Validates the Tag.
     * Thumbnails must be a valid URL and the image dimensions
     * must be at least 160x90 pixels and at most 1920x1080 pixels
     *
     * @return boolean
     *  TRUE if valid and FALSE otherwise
     */
    public function validate() {

        $validator = new URL(
            [ 'value' => $this -> options -> data ]
        );

        if( $validator -> validate() !== FALSE ) {

            $image = @getimagesize( $this -> options -> data );

            /**
             * @internal
             *
             * If we can't retrieve image dimensions we can't say
             * for sure it's invalid or not ¯\_(ツ)_/¯
             */
            if( $image === FALSE ) return TRUE;

            list( $width, $height, $type, $attr ) = $image;

            if( ( ( $width  >= self::MINIMUM_THUMBNAIL_WIDTH )  && ( $width  <= self::MAXIMUM_THUMBNAIL_WIDTH ) ) &&
                ( ( $height >= self::MINIMUM_THUMBNAIL_HEIGHT ) && ( $height <= self::MAXIMUM_THUMBNAIL_HEIGHT ) ) ) {

                return TRUE;
            }

            return FALSE;
        }

        return FALSE;
    }

    // Tag Method Implementation

    /**
     * Defines whether or not a Tag is a leaf Node or not.
     * Leaf Nodes can't have any children
     *
     * @return boolean
     *  TRUE because 'Thumbnail' Tag describes a single content and
     *  thus doesn't need any other Children Node within it
     */
    public function isLeaf() {
        return TRUE;
    }
}