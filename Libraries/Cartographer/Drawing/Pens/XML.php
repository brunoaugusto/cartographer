<?php

/**
 * XML Drawing Pen | Cartographer\Drawing\Pens\XML.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Pens;

use Next\Components\Object;              # Object Class
use Next\Components\Invoker;             # Invoker Object
use Next\Components\Utils\ArrayUtils;    # Array Utils Class

/**
 * Draws the Sitemap Nodes' Composite as a XML Node structure
 *
 * @package    Cartographer
 *
 * @uses       \Next\Components\Object, Next\Components\Invoker\
 *             \Next\Components\Utils\ArrayUtils, \Next\XML\Writer,
 *             \Cartographer\Drawing\Pens\Pen
 */
class XML extends Object implements Pen {

    /**
     * XML Writer Object
     *
     * @var \Next\XML\Writer $writer
     */
    protected $writer;

    /**
     * Additional Initialization.
     * Instantiates a XML Writer Object and bridges the
     * Drawing Pen Object to it so all other methods not described by
     * \Cartographer\Drawing\Pens\Pen can be used if needed
     */
    public function init() {

        $this -> writer = new \Next\XML\Writer;

        // Bridging Drawing Pen Object to XML Writer Object

        $this -> extend( new Invoker( $this, $this -> writer ) );
    }

    // Pen Interface Methods Implementations

    /**
     * Adds a Parent Node.
     * Parent Nodes can have children Nodes within them at expense of
     * not being allowed to have a direct value
     *
     * @param string $name
     *  Node name
     *
     * @param mixed|array|optional $attributes
     *  Optional Node attributes
     */
    public function addParent( $name, $attributes = [] ) {
        return $this -> writer -> addParent( $name, ArrayUtils::map( $attributes ) );
    }

    /**
     * Adds a Child Node.
     * A Child Node are a leaf of the tree and thus cannot have any
     * children Nodes within them but, opposed to a Parent Node, can
     * optionally have a direct value
     *
     * @param string $name
     *  Node name
     *
     * @param mixed|optional $value
     *  Optional Node value
     *
     * @param mixed|array|optional $attributes
     *  Optional Node attributes
     *
     * @param boolean|optional $addCDATABlock
     *  Defines whether or not the Node value will be wrapped in
     *  a CDATA Block
     *
     * @link http://wikipedia.org/wiki/CDATA
     */
    public function addChildren( $name, $value = NULL, $attributes = [], $addCDATABlock = FALSE ) {

        return $this -> writer -> addChild(

            $name, $value,

            ( ! is_null( $attributes ) ?  ArrayUtils::map( $attributes ) : [] ),

            FALSE, $addCDATABlock
        );
    }

    /**
     * Closes the Node being drawn
     */
    public function closeNode() {
        return $this -> writer -> endElement();
    }

    /**
     * Writes down the Sitemap Nodes' Composite.
     * As for XML Pen it simply flushes the current buffer
     *
     * @param array|optional $options
     *  Additional arguments to be passed to the Drawing Pen.
     */
    public function write( array $options = [] ) {
        return $this -> writer -> flush();
    }
}