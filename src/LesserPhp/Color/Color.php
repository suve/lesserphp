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
    const HSL = 'hsl';
    const RGB = 'rgb';

    private $valueA;
    private $valueB;
    private $valueC;
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
        $this->valueA = $r;
        $this->valueB = $g;
        $this->valueC = $b;
        $this->alpha = $alpha;
    }

    /**
     * @return float
     */
    public function getValueA()
    {
        return $this->valueA;
    }

    /**
     * @return float
     */
    public function getValueB()
    {
        return $this->valueB;
    }

    /**
     * @return float
     */
    public function getValueC()
    {
        return $this->valueC;
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
        return [$this->valueA, $this->valueB, $this->valueC];
    }

    /**
     * @return null|float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }
}
