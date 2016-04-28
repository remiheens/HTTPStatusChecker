<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:53
 */

namespace Remiheens\HttpStatusChecker\Crawler;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Remiheens\HttpStatusChecker\Exceptions\InvalidBaseUrl;

class Crawler
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Remiheens\HttpStatusChecker\Crawler\Url;
     */
    protected $baseUrl;

    /**
     * @var \Remiheens\HttpStatusChecker\Crawler\CrawlObserver
     */
    protected $crawlObserver;


    /**
     * @return static
     */
    public static function create()
    {
        $client = new Client([
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::COOKIES => true,
        ]);

        return new static($client);
    }

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->crawledUrls = collect();
    }

    /**
     * Set the crawl observer.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\CrawlObserver $crawlObserver
     *
     * @return $this
     */
    public function setCrawlObserver(CrawlObserver $crawlObserver)
    {
        $this->crawlObserver = $crawlObserver;

        return $this;
    }

    /**
     * Start the crawling process.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\Url|string $baseUrl
     *
     * @throws \Remiheens\HttpStatusChecker\Exceptions\InvalidBaseUrl
     */
    public function startCrawling($url)
    {
        if (!$url instanceof Url)
        {
            $url = Url::create($url);
        }
        if ($url->isRelative())
        {
            throw new InvalidBaseUrl($url.' is not a valid url');
        }
        $this->crawlUrl($url);
        $this->crawlObserver->finishedCrawling();
    }

    /**
     * Start the crawling process.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\Url|string $baseUrl
     *
     * @throws \Remiheens\HttpStatusChecker\Exceptions\InvalidBaseUrl
     */
    public function startCrawlingMulti($urls)
    {
        foreach ($urls as $url)
        {
            $rawUrl = $url;
            if (!$url instanceof Url)
            {
                $url = Url::create($url);
            }
            if ($url->isRelative())
            {
                throw new InvalidBaseUrl($rawUrl.' is not a valid url');
            }
            $this->crawlUrl($url);
        }
        $this->crawlObserver->finishedCrawling();
    }

    /**
     * Crawl the given url.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\Url $url
     */
    protected function crawlUrl(Url $url)
    {
        if ($this->hasAlreadyCrawled($url))
        {
            return;
        }
        try
        {
            $response = $this->client->request('GET', (string)$url);
        }
        catch (RequestException $exception)
        {
            $response = $exception->getResponse();
        }
        $this->crawlObserver->hasBeenCrawled($url, $response);
        $this->crawledUrls->push($url);
        if (!$response)
        {
            return;
        }
    }

    /**
     * Determine if the crawled has already crawled the given url.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\Url $url
     *
     * @return bool
     */
    protected function hasAlreadyCrawled(Url $url)
    {
        foreach ($this->crawledUrls as $crawledUrl)
        {
            if ((string)$crawledUrl === (string)$url)
            {
                return true;
            }
        }

        return false;
    }
}