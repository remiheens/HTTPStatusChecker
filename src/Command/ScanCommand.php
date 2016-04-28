<?php

/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:30
 */

namespace Remiheens\HttpStatusChecker\Command;

use Remiheens\HttpStatusChecker\Crawler\Crawler;
use Remiheens\HttpStatusChecker\Crawler\Url;
use Remiheens\HttpStatusChecker\Crawler\MultiCrawlLogger;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command
{
    protected function configure()
    {
        $this->setName('scan')
             ->setDescription('Check the http status code of all links')
             ->addArgument(
                 'config',
                 InputArgument::REQUIRED,
                 'list to parse'
             );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('config');
        $file = file_get_contents($file);

        $matches = [];
        preg_match_all('/([^\t|\s]+)\t?\s*([0-9]{3})?\t?\s*([a-z]*)\n?/', $file, $matches);

        if (count($matches) == 4)
        {
            array_shift($matches);
            $nbLine = count(reset($matches));
            $lstUrls = [];
            for ($i = 0; $i < $nbLine; $i++)
            {
                $url = $matches[0][ $i ];
                $statusCode = $matches[1][ $i ];
                $scheme = $matches[2][ $i ];

                $url = new Url($url);
                if (isset($statusCode) && !empty($statusCode))
                {
                    $url->setExpectedCode($statusCode);
                }
                if (isset($scheme) && !empty($scheme))
                {
                    $url->setExpectedScheme($scheme);
                }
                $lstUrls[] = $url;
            }

            Crawler::create()->setCrawlObserver(new MultiCrawlLogger($output))
                   ->startCrawlingMulti($lstUrls);

            return 0;
        }
    }
}