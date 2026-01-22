<?php

namespace Liujinyong\PackagistBuild;


use Liujinyong\PackagistBuild\Commands\Build;
use Symfony\Component\Console\Application as Base;

class Application extends Base
{
    /**
     * Application constructor.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct()
    {
        parent::__construct();
        $this->add(new Build());

    }
}