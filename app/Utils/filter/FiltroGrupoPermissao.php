<?php


    namespace App\Utils\filter;


    use App\Enums\PermissoesTipo;

    class FiltroGrupoPermissao
    {
        public static function verifica_permissao_por_grupo($nome_grupo)
        {
            switch ($nome_grupo) {
                case "filmes":
                    return self::grupo_filmes();
                case "seriesTorrent":
                    return self::grupo_seriesTorrent();
                case "seriesOnline":
                    return self::grupo_seriesOnline();
                case "jogos":
                    return self::grupo_jogos();
                case "animes":
                    return self::grupo_animes();
                case "feed":
                    return self::grupo_feeds();
                default:
                    return true;
            }
        }

        private static function grupo_filmes()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getFilmes());
        }
        private static function grupo_seriesTorrent()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getSeriesTorrent());
        }
        private static function grupo_seriesOnline()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getSeriesOnline());
        }
        private static function grupo_jogos()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getJogos());
        }
        private static function grupo_animes()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getAnimes());
        }
        private static function grupo_feeds()
        {
            return auth()->user()->hasAnyPermission(PermissoesTipo::getFeeds());
        }
    }
