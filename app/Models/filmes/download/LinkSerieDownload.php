<?php


namespace App\Models\filmes\download;

/**
 * Class LinkSerieDownload
 * @property LinkSerie[] $links_serie
 * @property boolean $is_temporada_completa
 * @package App\Models\filmes\download
 */
class LinkSerieDownload
{
    public $episodio_numero,$links_serie,$is_temporada_completa;

    /**
     * LinkSerieDownload constructor.
     */
    public function __construct()
    {
        $this->episodio_numero = "0";
        $this->links_serie = [];
        $this->is_temporada_completa = false;
    }


}
