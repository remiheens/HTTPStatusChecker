<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:58
 */

namespace Remiheens\HttpStatusChecker\Crawler;

use Symfony\Component\Console\Output\OutputInterface;

class CrawlLogger implements CrawlObserver
{

    const UNRESPONSIVE_HOST = 'Host did not respond';

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;
    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function hasBeenCrawled(Url $url, $response)
    {
        $statusCode = $response ? $response->getStatusCode() : self::UNRESPONSIVE_HOST;
        $reason = $response ? $response->getReasonPhrase() : '';

        if($response && starts_with($statusCode, '3'))
        {
            $locations = $response->getHeader('Location');
            $location = reset($locations);
            $schemeLocation = Url::create($location)->scheme;
        }
        else
        {
            $schemeLocation = $url->scheme;
        }

        $timestamp = date('Y-m-d H:i:s');

        $colors = [
            'info', 'comment', 'error'
        ];

        $i = 0;
        if ($statusCode != $url->expectedCode)
        {
            $i++;
            $this->errors[] =
                'Statuscode don\'t match : ' . $url->expectedCode . ' was expected but ' . $statusCode . ' appear';
        }

        if ($schemeLocation != $url->expectedScheme)
        {
            $i++;
            $this->errors[] =
                'Scheme don\'t match : ' . $url->expectedScheme . ' was expected but ' . $url->scheme . ' appear';
        }

        $colorTag = $colors[ $i ];

        $this->output->writeln("<{$colorTag}>[{$timestamp}] {$statusCode} {$reason} - {$url}</{$colorTag}>");
    }

    public function finishedCrawling()
    {
        $this->output->writeln('');
        $this->output->writeln('Test summary');
        $this->output->writeln('----------------');

        if (isset($this->errors) && !empty($this->errors))
        {
            foreach ($this->errors as $error)
            {
                $this->output->writeln($error);
            }
        }
        else
        {
            $this->output->writeln('Everything is OK');
        }
        $this->output->writeln('');
    }

}