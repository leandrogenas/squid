<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class Sites extends Enum
{
    const FILMESVIATORRENT_INFO = "filmesviatorrentinfo";
    const FILMESVIATORRENT_ORG = "filmesviatorrentorg";
    const FILMESVIATORRENT_BIZ = "filmesviatorrentbiz";
    const PIRATEFILMES_NET = "piratefilmes";
    const FILMESTORRENT_VIP = "filmestorrentvip";
    const JOGOS_TORRENT = "jogostorrent";
    const TORRENT_VIP = "torrentvip";
    const SERIESONLINE_PRO = "seriesonlinepro";
    const ANIMESONLINE_VIP = "animesonlinevip";
    const KFILMES = "kfilmestorrent";

    /**
     * @return array
     */
    public static function get_movie_sites()
    {
        return [self::FILMESVIATORRENT_INFO, self::FILMESVIATORRENT_ORG, self::FILMESVIATORRENT_BIZ, self::PIRATEFILMES_NET, self::FILMESTORRENT_VIP, self::TORRENT_VIP];
    }

    /**
     * @return array
     */
    public static function get_games_sites()
    {
        $sites = [self::JOGOS_TORRENT];
        return $sites;
    }

    public static function get_movies_sites_active()
    {
        return [self::FILMESVIATORRENT_BIZ,self::FILMESTORRENT_VIP, self::TORRENT_VIP,self::KFILMES];

    }

    public static function get_serie_sites_active()
    {
        return [self::FILMESVIATORRENT_BIZ,self::FILMESTORRENT_VIP];

    }

    public static function get_only_series_sites(){
        return [self::SERIESONLINE_PRO];
    }
}
