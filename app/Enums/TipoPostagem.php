<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TipoPostagem extends Enum
{
    const FILME =   "Filme";
    const SERIE_TORRENT =   "Série Torrent";
    const SERIE_ONLINE = "Série Online";
    const JOGOS = "Jogos";
    const ANIMES = "Animes";
}
