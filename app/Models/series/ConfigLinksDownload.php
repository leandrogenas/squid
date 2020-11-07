<?php


namespace App\Models\series;


class ConfigLinksDownload
{
    public $pegar_tudo = true;
    /**
     * @var array
     * ex: $pegar_temporada_e_episodios[numero_temporada][configuracao_da_temporada]
     */
    public $pegar_temporada_e_episodios = [];
}
