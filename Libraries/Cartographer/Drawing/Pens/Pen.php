<?php

/**
 * Drawing Pens Interface | Cartographer\Drawing\Pens\Pen.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Pens;

/**
 * Defines a Drawing Pen The Cartographer will use to draw the Sitemap Nodes'
 * Composite, effectively rendering the logic structure in a humanized format
 *
 * @package    Cartographer
 */
interface Pen {

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
    public function addParent( $name, $attributes = [] );

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
    public function addChildren( $name, $value = NULL, $attributes = [], $addCDATABlock = FALSE );

    /**
     * Closes the Node being drawn
     */
    public function closeNode();

    /**
     * Writes down the Sitemap Nodes' Composite.
     *
     * @param array|optional $options
     *  Additional arguments to be passed to the Drawing Pen
     */
    public function write( array $options = [] );
}