<?php

namespace Web;

interface ViewFactory
{
    public function load(string $fileName): View;
}
