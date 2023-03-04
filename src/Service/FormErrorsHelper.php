<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormErrorsHelper
{
    public function prepareErrors(FormInterface $form): array
    {
        $allErrors = $this->getErrorMessages($form);
        $formattedErrors = $this->prepareFieldsErrors($allErrors);

        return $formattedErrors;
    }

    public function getErrorMessages(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $errors[$key] = $error->getMessage();
        }

        if ($form->count()) {
            foreach ($form as $child) {
                if ($child->isSubmitted() &&
                    !$child->isValid() &&
                    count($childErrors = $this->getErrorMessages($child))
                ) {
                    $errors[$child->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    private function prepareFieldsErrors(array $allErrors): array
    {
        $fieldErrors = [];
        if (isset($allErrors['fields'])) {
            $fieldErrors = $allErrors['fields'];
            unset($allErrors['fields']);
        }

        foreach ($fieldErrors as $fieldId => $errors) {
            $fieldsErrors[$fieldId] = isset($fieldsErrors[$fieldId]) ?
                array_merge($fieldErrors[$fieldId], $errors) :
                $errors;
        }

        $allErrors = $this->toFlatArray($allErrors);

        return [
            'common' => $allErrors,
            'fields' => $fieldErrors,
        ];
    }

    protected function toFlatArray(array $array): array
    {
        $res = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $res = array_merge($res, $this->toFlatArray($item));
            } else {
                $res[] = $item;
            }
        }

        return $res;
    }
}