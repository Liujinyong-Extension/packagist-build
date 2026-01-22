<?php

namespace Liujinyong\PackagistBuild\Commands;

use Symfony\Component\Console\Command\Command;

class Base extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }
    protected function getHelperHandle()
    {
        return $this->getHelper("question");

    }
}