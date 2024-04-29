<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface {
    public function transform(mixed $value): mixed
    {
        if($value === null) {
            return null;
        }
        return $value / 100; 
    }

    public function reverseTransform(mixed $value): mixed
    {
        if($value === null) {
            return null;
        }
        return $value * 100;
    }
}