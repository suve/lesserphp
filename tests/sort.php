<?php
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

// sorts the selectors in stylesheet in order to normalize it for comparison

$exe = array_shift($argv); // remove filename

if (!$fname = array_shift($argv)) {
    $fname = 'php://stdin';
}

class LesscNormalized extends \LesserPhp\Compiler
{

    /**
     * LesscNormalized constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setNumberPrecision(3);
    }

    /**
     * Compiles a primitive value into a CSS property value.
     *
     * Values in lessphp are typed by being wrapped in arrays, their format is
     * typically:
     *
     *     array(type, contents [, additional_contents]*)
     *
     * The input is expected to be reduced. This function will not work on
     * things like expressions and variables.
     *
     * @param array $value
     *
     * @return string
     * @throws \LesserPhp\Exception\GeneralException
     */
    public function compileValue(array $value)
    {
        if ($value[0] === 'raw_color') {
            $value = $this->getCoerce()->coerceColor($value);
        }

        return parent::compileValue($value);
    }
}

/**
 * Class SortingFormatter
 */
class SortingFormatter extends \LesserPhp\Formatter\Lessjs
{

    /**
     * @param $block
     *
     * @return string
     */
    public function sortKey($block)
    {
        if (!isset($block->sortKey)) {
            sort($block->selectors, SORT_STRING);
            $block->sortKey = implode(',', $block->selectors);
        }

        return $block->sortKey;
    }

    /**
     * @param $block
     */
    public function sortBlock($block)
    {
        usort($block->children, function ($a, $b) {
            $sort = strcmp($this->sortKey($a), $this->sortKey($b));
            if ($sort === 0) {
                // TODO
            }

            return $sort;
        });
    }

    /**
     * @param $block
     *
     * @return mixed
     */
    public function block($block)
    {
        $this->sortBlock($block);

        return parent::block($block);
    }

}

$less = new LesscNormalized();
$less->setFormatter(new SortingFormatter);
echo $less->parse(file_get_contents($fname));
