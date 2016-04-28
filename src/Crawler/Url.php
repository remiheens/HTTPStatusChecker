<?php
/**
 * Created by PhpStorm.
 * User: Rémi HEENS
 * Date: 28/04/2016
 * Time: 10:47
 */

namespace Remiheens\HttpStatusChecker\Crawler;

use Remiheens\HttpStatusChecker\Exceptions\InvalidCodeNumber;
use Remiheens\HttpStatusChecker\Exceptions\InvalidPortNumber;
use Remiheens\HttpStatusChecker\Exceptions\InvalidScheme;
use Remiheens\HttpStatusChecker\HTTP;

class Url
{
    /**
     * @var null|string
     */
    public $scheme;

    /**
     * @var null|string
     */
    public $host;

    /**
     * @var int
     */
    public $port = 80;

    /**
     * @var null|string
     */
    public $path;

    /**
     * @var null|string
     */
    public $query;

    /**
     * @var null|string
     */
    public $expectedScheme;

    /**
     * @var null|int
     */
    public $expectedCode;

    /**
     * @param $url
     *
     * @return static
     */
    public static function create($url)
    {
        return new static($url);
    }

    /**
     * Url constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $urlProperties = parse_url($url);
        foreach (['scheme', 'host', 'path', 'port', 'query'] as $property)
        {
            if (isset($urlProperties[ $property ]))
            {
                $this->$property = $urlProperties[ $property ];
            }
        }
    }

    /**
     * Determine if the url is relative.
     *
     * @return bool
     */
    public function isRelative()
    {
        return is_null($this->host);
    }

    /**
     * Determine if the url is protocol independent.
     *
     * @return bool
     */
    public function isProtocolIndependent()
    {
        return is_null($this->scheme);
    }

    /**
     * Determine if this is a mailto-link.
     *
     * @return bool
     */
    public function isEmailUrl()
    {
        return $this->scheme === 'mailto';
    }

    /**
     * Determine if this is an inline javascript.
     *
     * @return bool
     */
    public function isJavascript()
    {
        return $this->scheme === 'javascript';
    }

    /**
     * Set the scheme.
     *
     * @param string $scheme
     *
     * @return $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Set the host.
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param int $port
     *
     * @return $this
     *
     * @throws \Remiheens\HttpStatusChecker\Exceptions\InvalidPortNumber
     */
    public function setPort($port)
    {
        if (!is_int($port))
        {
            throw new InvalidPortNumber();
        }
        $this->port = $port;

        return $this;
    }

    /**
     * Remove the fragment.
     *
     * @return $this
     */
    public function removeFragment()
    {
        $this->path = explode('#', $this->path)[0];

        return $this;
    }

    /**
     * HTTP Status Code expected
     *
     * @param $code
     *
     * @return $this
     * @throws InvalidCodeNumber
     */
    public function setExpectedCode($code)
    {
        if (!empty($code) && HTTP::isValidCode($code) === false)
        {
            throw new InvalidCodeNumber($code . ' is not a right code');
        }
        $this->expectedCode = $code;

        return $this;
    }

    /**
     * HTTP Scheme expected on response
     *
     * @param $scheme
     *
     * @return $this
     * @throws InvalidScheme
     */
    public function setExpectedScheme($scheme)
    {
        if (!empty($scheme) && HTTP::isValidScheme($scheme) === false)
        {
            throw new InvalidScheme();
        }
        $this->expectedScheme = $scheme;

        return $this;
    }

    /**
     * Convert the url to string.
     *
     * @return string
     */
    public function __toString()
    {
        $path = starts_with($this->path, '/') ? substr($this->path, 1) : $this->path;
        $port = ($this->port === 80 ? '' : ":{$this->port}");
        $queryString = (is_null($this->query) ? '' : "?{$this->query}");

        return "{$this->scheme}://{$this->host}{$port}/{$path}{$queryString}";
    }

}