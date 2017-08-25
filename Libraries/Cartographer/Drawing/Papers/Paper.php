<?php

/**
 * Drawing Papers Interface | Cartographer\Drawing\Papers\Paper.php
 *
 * @author       Bruno Augusto
 *
 * @copyright    Copyright (c) 2017 Next Studios
 * @license      http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License 3.0
 */
namespace Cartographer\Drawing\Papers;

/**
 * Defines a Drawing Paper The Cartographer will use to output the Sitemap drawn
 *
 * @package    Cartographer
 */
interface Paper {

    /**
     * Publishes Sitemap drawn
     *
     * @param string $data
     *  Sitemap Drawn
     */
    public function publish( $data );
}