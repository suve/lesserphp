<?php

namespace LesserPhp\Library;

use LesserPhp\Exception\GeneralException;

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
class Assertions
{

    private $coerce;

    public function __construct(Coerce $coerce)
    {
        $this->coerce = $coerce;
    }

    public function assertColor($value, $error = "expected color value")
    {
        $color = $this->coerce->coerceColor($value);
        if ($color === null) {
            throw new GeneralException($error);
        }

        return $color;
    }

    public function assertNumber($value, $error = "expecting number")
    {
        if ($value[0] === "number") {
            return $value[1];
        }
        throw new GeneralException($error);
    }

    public function assertArgs($value, $expectedArgs, $name = "")
    {
        if ($expectedArgs == 1) {
            return $value;
        } else {
            if ($value[0] !== "list" || $value[1] !== ",") {
                throw new GeneralException('expecting list');
            }
            $values = $value[2];
            $numValues = count($values);
            if ($expectedArgs != $numValues) {
                if ($name) {
                    $name .= ": ";
                }
                throw new GeneralException("${name}expecting $expectedArgs arguments, got $numValues");
            }

            return $values;
        }
    }

    public function assertMinArgs($value, $expectedMinArgs, $name = "")
    {
        if ($value[0] !== "list" || $value[1] !== ",") {
            throw new GeneralException("expecting list");

        }
        $values = $value[2];
        $numValues = count($values);
        if ($expectedMinArgs > $numValues) {
            if ($name) {
                $name .= ": ";
            }

            throw new GeneralException("${name}expecting at least $expectedMinArgs arguments, got $numValues");
        }

        return $values;
    }
}
