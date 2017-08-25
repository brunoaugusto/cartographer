<?php

/**
 * Drawable Interface | Cartographer\Drawing\Drawable.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing;

use Cartographer\Drawing\Pens\Pen;    # Drawing Pen Interface

/**
 * Drawable Objects compile informations, rendering a humanized
 * output of logic data
 *
 * @package    Cartographer
 *
 * @uses       \Cartographer\Drawing\Pens\Pen
 */
interface Drawable {

    /**
     * Draws the Tag using given Drawing Pen
     *
     * @param \Cartographer\Pens\Pen $pen
     *  A Drawing Pen Object to draw the Sitemap Node
     */
    public function draw( Pen $pen );
}