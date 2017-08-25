<?php

/**
 * Video Node Tag Interface | Cartographer\Drawing\Nodes\Node.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Nodes;

use Cartographer\Drawing\Drawable;    # Drawable Interface

/**
 * Defines a Drawable Node tag for the Sitemap Nodes' Composite
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Drawable
 */
interface Node extends Drawable {

    /**
     * Defines whether or not a Tag is a leaf Node.
     * Leaf Nodes can't have any children
     */
    public function isLeaf();
}