<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:28
 */
namespace Remiheens\HttpStatusChecker;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        parent::__construct('Http status checker', '1.0.0');
        $this->add(new Command\ScanCommand());
        $this->add(new Command\TestCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion() . ' by <comment>Remiheens</comment>';
    }
}