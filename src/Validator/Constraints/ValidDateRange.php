<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidDateRange extends Constraint
{
    public $message = 'There is another trip for that user that overlaps';

    public function validatedBy()
    {
        return ValidDateRangeValidator::class;
    }

    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }
}