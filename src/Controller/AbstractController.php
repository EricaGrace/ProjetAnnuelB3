<?php

namespace App\Controller;

use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use Twig\Environment;

abstract class AbstractController
{
    protected Environment $twig;
    protected Router $router;

    public function __construct(Environment $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    protected function renderIf($name, array $context = [], mixed ...$conditions)
    {
        foreach ($conditions as $condition) {
            if (!$condition) $this->return404();
        }

        return $this->render($name, $context);
    }

    protected function return404()
    {
        throw new RouteNotFoundException();
    }

    protected function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }
}
