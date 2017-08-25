<?php

/**
 * Node Tag Abstract Class | Cartographer\Drawing\Nodes\AbstractNode.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes;

use Next\Validate\Validatable;          # Validatable Interface
use Next\Components\Debug\Exception;    # Exception Class
use Next\Components\Object;             # Object Class

use Cartographer\Drawing\Pens\Pen;      # Drawing Pen Interface

/**
 * Defines the basic structure for a Sitemap Nodes' Composite
 *
 * @package    Cartographer
 *
 * @uses       \ArrayObject,
 *             \Next\Validate\Validatable, \Next\Components\Debug\Exception,
 *             \Next\Components\Object,
 *             \Cartographer\Drawing\Nodes\Node,
 *             \Cartographer\Drawing\Pens\Pen,
 */
abstract class AbstractNode extends Object implements Node, Validatable {

    /**
     * Default Node Options
     *
     * @var array $defaultOptions
     */
    protected $defaultOptions = [

        /**
         * Automatically closes a Node not marked as
         * Leaf (i.e a Parent Node) so any other occurrence
         * of it won't nest unduly.
         * Defaults to TRUE
         */
        'autoCloseParentNodes' => TRUE
    ];

    /**
     * Component Children
     *
     * @var ArrayObject $children
     */
    protected $children;

    /**
     * Additional Initialization.
     * Initializes Components' Children ArrayObject
     */
    public function init() {

        if( ! $this -> isLeaf() ) {
            $this -> children = new \ArrayObject;
        }
    }

    /**
     * Adds a new Node
     *
     * @param \Cartographer\Drawing\Nodes\Node $node
     *  Node to add
     *
     * @throws \Next\Components\Debug\Exception
     *  Thrown if trying to add a new Node inside another marked as leaf
     */
    public function add( Node $node ) {

        if( $this -> isLeaf() ) {
            throw new Exception( 'A Node marked as leaf cannot have children', Exception::LOGIC_ERROR );
        }

        $this -> children -> append( $node );

        return $this;
    }

    // Auxiliary Methods Definition

    /**
     * Draws Children Nodes
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    protected function drawChildren( Pen $pen ) {

        if( ! $this -> isLeaf() ) {

            $iterator = $this -> children -> getIterator();

            for( $iterator -> rewind(); $iterator -> valid(); $iterator -> next() ) {
                $iterator -> current() -> draw( $pen );
            }

            if( $this -> options -> autoCloseParentNodes ) {
                $pen -> closeNode();
            }
        }
    }
}