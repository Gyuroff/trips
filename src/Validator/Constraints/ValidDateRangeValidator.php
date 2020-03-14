<?php


namespace App\Validator\Constraints;


use App\Repository\TripRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidDateRangeValidator extends ConstraintValidator
{
    /**
     * @var TripRepository
     */
    private $tripRepository;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * ValidEndDateValidator constructor.
     */
    public function __construct(TripRepository $tripRepository, Security $security)
    {
        $this->tripRepository = $tripRepository;
        $this->user = $security->getUser();;
    }

    public function validate($value, Constraint $constraint)
    {
        $object = $this->context->getObject();
        $startDate = $object->getStartDate();
        $endDate = $object->getEndDate();

        if ($this->tripRepository->isThereOverlaps($startDate, $endDate, $this->user)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}