<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:35
 */

namespace Remiheens\HttpStatusChecker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Remiheens\HttpStatusChecker\Crawler\CrawlLogger;
use Remiheens\HttpStatusChecker\Crawler\Crawler;
use Remiheens\HttpStatusChecker\Crawler\Url;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test')
             ->setDescription('Check HTTP status for a specific url')
             ->addArgument(
                 'url',
                 InputArgument::REQUIRED,
                 'url to check'
             )
             ->addArgument('code', InputArgument::OPTIONAL, 'Which HTTP Response Code ?')
             ->addArgument('scheme', InputArgument::OPTIONAL, 'Which scheme ?');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $scheme = $input->getArgument('scheme');
        $code = $input->getArgument('code');


        $oUrl = new Url($url);
        $oUrl->setExpectedCode($code);
        $oUrl->setExpectedScheme($scheme);

        Crawler::create()->setCrawlObserver(new CrawlLogger($output))
               ->startCrawling($oUrl);

        return 0;
    }

}