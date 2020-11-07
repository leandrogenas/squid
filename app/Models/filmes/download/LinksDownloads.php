<?php


namespace App\Models\filmes\download;

/**
 * Class LinksDownloads
 * @property string $texto_links
 * @property LinkFilme[] $links
 * @package App\Models\filmes\download
 */
class LinksDownloads
{

    public $texto_links, $links;

    /**
     * LinksDownloads constructor.
     */
    public function __construct()
    {
        $this->texto_links = "";
        $this->links = [];
    }
}
