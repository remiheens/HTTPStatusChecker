<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:58
 */

namespace Remiheens\HttpStatusChecker\Crawler;

use Symfony\Component\Console\Output\OutputInterface;

class MultiCrawlLogger implements CrawlObserver
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

    /**
     * @var array
     */
    protected $crawledUrls = [];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function hasBeenCrawled(Url $url, $response)
    {
        $statusCode = $response ? $response->getStatusCode() : self::UNRESPONSIVE_HOST;

        if (starts_with($statusCode, '3'))
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
        if (isset($url->expectedCode) && !empty($url->expectedCode) && $statusCode != $url->expectedCode)
        {
            $i++;
            if (!isset($this->errors[ (string)$url ]))
            {
                $this->errors[ (string)$url ] = ' ';
            }
            $this->errors[ (string)$url ] .= "code = {$url->expectedCode} -> {$statusCode}";
        }

        if (isset($url->expectedScheme) && !empty($url->expectedScheme) && $schemeLocation != $url->expectedScheme)
        {
            $i++;
            if (!isset($this->errors[ (string)$url ]))
            {
                $this->errors[ (string)$url ] = ' ';
            }
            else
            {
                $this->errors[ (string)$url ] .= ' | ';
            }
            $this->errors[ (string)$url ] .= "scheme = {$url->expectedScheme} -> {$schemeLocation}";
        }

        $colorTag = $colors[ $i ];

        $this->output->writeln("<{$colorTag}>[{$timestamp}] {$statusCode} {$response->getReasonPhrase()} - {$url}</{$colorTag}>");
    }

    public function finishedCrawling()
    {
        $this->output->writeln('');
        $this->output->writeln('Test summary');
        $this->output->writeln('----------------');

        if (isset($this->errors) && !empty($this->errors))
        {
            foreach ($this->errors as $url => $error)
            {
                $this->output->writeln("[{$url}] $error");
            }
        }
        else
        {
            $this->output->writeln('Everything is OK');
        }
        $this->output->writeln('');
    }

}