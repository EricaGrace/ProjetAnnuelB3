<?php

namespace App\Bootstrap;

use Symfony\Component\HttpFoundation\Request;

interface KernelInterface
{
    public function getBootstrapers();

    public function handle(Request $request);
}