<?php

namespace LesserPhp\Formatter;

/**
 * lesserphp
 * https://www.maswaba.de/lesserphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org
 *
 * Copyright 2013, Leaf Corcoran <leafot@gmail.com>
 * Copyright 2016, Marcus Schwarz <github@maswaba.de>
 * Licensed under MIT or GPLv3, see LICENSE
 * @package LesserPhp
 */
interface FormatterInterface
{
    /**
     * @param $block
     *
     * @return mixed
     */
    public function block($block);

    public function getSelectorSeparator();

    public function getCompressColors();

    public function property($name, $value);

}
