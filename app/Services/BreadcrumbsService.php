<?php

namespace App\Services;

class BreadcrumbsService
{
    private $breadcrumbs;

    /**
     * If breadcrumbs already exists - we could construct service with it
     *
     * BreadcrumbsService constructor.
     * @param array $breadcrumbs
     */
    public function __construct(array $breadcrumbs = [])
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Push breadcrumb's element to the start
     *
     * @param string|null $name
     * @param string|null $url
     */
    public function pushToStart(?string $name, ?string $url)
    {
        array_unshift($this->breadcrumbs, [
            'name' => $name,
            'url' => $url,
        ]);
    }

    /**
     * Push breadcrumb's element to the end
     *
     * @param string|null $name
     * @param string|null $url
     */
    public function pushToEnd(?string $name, ?string $url)
    {
        array_push($this->breadcrumbs, [
            'name' => $name,
            'url' => $url,
        ]);
    }

    /**
     * Modify breadcrumb's element by key
     *
     * @param int $key
     * @param string|null $name
     * @param string|null $url
     */
    public function modify(int $key, ?string $name, ?string $url)
    {
        $this->breadcrumbs[$key] = [
            'name' => $name,
            'url' => $url,
        ];
    }

    /**
     * Get name of the breadcrumb's element
     *
     * @param int $key
     * @return mixed
     */
    public function getName(int $key = 0)
    {
        return $this->breadcrumbs[$key]['name'];
    }

    /**
     * Get url of the breadcrumb's element
     *
     * @param int $key
     * @return mixed
     */
    public function getUrl(int $key = 0)
    {
        return $this->breadcrumbs[$key]['url'];
    }

    /**
     * Get last breadcrumb's element
     *
     * @return int
     */
    public function getLastKey()
    {
        return array_key_last($this->breadcrumbs);
    }

    /**
     * Get constructed breadcrumbs array
     *
     * @return array
     */
    public function get()
    {
        return $this->breadcrumbs;
    }
}
