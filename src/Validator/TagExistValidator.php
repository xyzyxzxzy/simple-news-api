<?php

namespace App\Validator;

use App\Entity\Tag;
use App\Validator\Constraint\TagExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TagExistValidator extends ConstraintValidator
{
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TagExist) {
            throw new UnexpectedTypeException($constraint, TagExist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->em->getRepository(Tag::class)->find($value)) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
