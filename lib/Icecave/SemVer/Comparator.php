<?php
namespace Icecave\SemVer;

use Icecave\SemVer\TypeCheck\TypeCheck;

/**
 * Compares two Version instances using the rules defined at http://semver.org/ @ 2.0.0-rc.1
 */
class Comparator
{
    public function __construct()
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());
    }

    /**
     * @param Version $left
     * @param Version $right
     *
     * @return integer A number less than zero if $left is less than $right, greater than zero if $right is less $left, or zero if they are equivalent.
     */
    public function compare(Version $left, Version $right)
    {
        $this->typeCheck->compare(func_get_args());

        // Compare the major version ...
        if ($diff = $left->major() - $right->major()) {
            return $diff;

        // Compare the minor version ...
        } elseif ($diff = $left->minor() - $right->minor()) {
            return $diff;

        // Compare the patch version ...
        } elseif ($diff = $left->patch() - $right->patch()) {
            return $diff;

        // Compare the pre-release version ...
        } elseif ($diff = $this->compareIdentifierParts($left->preReleaseVersionParts(), $right->preReleaseVersionParts())) {
            return $diff;

        // Do not compare the build meta-data ...
        } else {
            return 0;
        }
    }

    /**
     * Compare the dot-separted parts of a pre-release or build meta-data.
     *
     * @param array<string> $left
     * @param array<string> $right
     *
     * @return integer
     */
    protected function compareIdentifierParts(array $left, array $right)
    {
        $this->typeCheck->compareIdentifierParts(func_get_args());

        $leftCount = count($left);
        $rightCount = count($right);

        // If either of the sides is empty we can bail early ...
        if (0 === $leftCount || 0 === $rightCount) {
            return $rightCount - $leftCount;
        }

        // Compare the individual parts ...
        for ($index = 0; $index < $leftCount && $index < $rightCount; ++$index) {
            if ($diff = $this->compareIdentifierPart($left[$index], $right[$index])) {
                return $diff;
            }
        }

        // All parts compared equal, the version with the shorter identifier is considered less ...
        return $leftCount - $rightCount;
    }

    /**
     * @param string $left
     * @param string $right
     *
     * @return integer
     */
    protected function compareIdentifierPart($left, $right)
    {
        $this->typeCheck->compareIdentifierPart(func_get_args());

        $leftDigits = ctype_digit($left);
        $rightDigits = ctype_digit($right);

        // If both are digits compare as numbers ...
        if ($leftDigits && $rightDigits) {
            return intval($left) - intval($right);

        // Digits are always "less" than text ...
        } elseif ($leftDigits) {
            return -1;

        // Digits are always "less" than text ...
        } elseif ($rightDigits) {
            return +1;
        }

        // Compare both sides as strings ...
        return strcmp($left, $right);
    }

    private $typeCheck;
}
