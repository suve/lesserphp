<?php

namespace LesserPhp;

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
class Color
{

    private $r;
    private $g;
    private $b;
    private $type;
    private $alpha;

    /**
     * Color constructor.
     *
     * @param string $type
     * @param int $r
     * @param int $g
     * @param int $b
     * @param float|null $alpha
     */
    public function __construct($type, $r, $g, $b, $alpha = null)
    {
        $this->type = $type;
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->alpha = $alpha;
    }

    /**
     * @return float
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * @return float
     */
    public function getG()
    {
        return $this->g;
    }

    /**
     * @return float
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getRgb()
    {
        return [$this->r, $this->g, $this->b];
    }

    /**
     * @return null|float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

}
