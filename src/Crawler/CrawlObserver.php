<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:50
 */

namespace Remiheens\HttpStatusChecker\Crawler;


interface CrawlObserver
{
    /**
     * Called when the crawler has crawled the given url.
     *
     * @param \Remiheens\HttpStatusChecker\Crawler\Url $url
     * @param \Psr\Http\Message\ResponseInterface|null $response
     */
    public function hasBeenCrawled(Url $url, $response);

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling();
}