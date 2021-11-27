<?php

namespace RocketsLab\Neewton\Contracts;

interface ModuleContract
{
    public function configure(): array;

    public function depends(): array|null;
}
