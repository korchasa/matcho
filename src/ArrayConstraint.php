<?php declare(strict_types=1);

namespace korchasa\matched;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class ArrayConstraint extends Constraint
{
    /**
     * @var array
     */
    protected $pattern;

    /**
     * @param array $pattern
     */
    public function __construct(array $pattern = null)
    {
        parent::__construct();
        $this->pattern = $pattern;
    }

    /**
     * @param mixed $other
     * @param string $description
     * @param boolean $returnResult
     *
     * @return boolean
     * @throws \Exception
     */
    public function evaluate(
        $other,
        $description = 'Failed asserting that array matched pattern',
        $returnResult = false
    ): bool {
        return Match::array(
            $this->pattern,
            $other,
            Match::ANY_SYMBOL,
            function ($expected, $actual, $message) use ($returnResult) {
                if ($returnResult) {
                    return;
                }
                $diffBuilder = new UnifiedDiffOutputBuilder("--- Pattern\n+++ Actual\n");
                $diff = (new Differ($diffBuilder))->diff(var_export($expected, true), var_export($actual, true));
                throw new ExpectationFailedException($message."\n".$diff);
            }
        );
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            'matches array `%s`',
            json_encode($this->pattern)
        );
    }
}
