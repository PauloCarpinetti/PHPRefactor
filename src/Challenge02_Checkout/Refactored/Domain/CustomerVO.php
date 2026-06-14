<?php

namespace App\Challenge02_Checkout\Refactored\Domain;

class CustomerVO {
    public function __construct(
        public readonly string $email,
    ) {}
}
