<?php

namespace App\Challenge02_Checkout\Refactored\Domain;

class CartItemVO {
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly int    $qty,
        public readonly float  $price,
    ) {}
}
