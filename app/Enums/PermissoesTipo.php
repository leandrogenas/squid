<?php

    namespace App\Enums;

    use BenSampo\Enum\Enum;


    final class PermissoesTipo extends Enum
    {
        //filmes
        const VER_PUBLICAR_FILME = "Ver Publicar Filme";
        const VER_TROCAR_CAPA_FILMES = "Ver Trocar Capa Filmes";
        const VER_PROCURA_IMG_OFFLINE = "Ver Procura IMG Offline";

        //series torrent
        const VER_PUBLICAR_SERIE = "Ver Publicar Serie";
        const VER_ATUALIZAR_SERIE = "Ver Atualizar Série";

        //series online
        const VER_PUBLICA_SERIE_ONLINE = "Ver Publicar Serie Online";

        //jogos
        const VER_PUBLICAR_JOGOS = "Ver Publicar Jogos";

        //animes
        const  VER_FAZER_POSTAGEM = "Ver Fazer Postagem";
        const  VER_FAZER_POSTAGEM_ORION = "Ver Fazer Postagem (ORION)";
        const  VER_ATUALIZAR_POSTAGEM = "Ver atualizar postagem anime";
        const VER_VERIFICAR_POSTAGEM_ORION = "Ver Verificar Postagem Orion";

        const VER_FEEDS = "Ver Feeds";

        const ADMINISTRAR_SISTEMA = "Administrar Sistema";

        public static function getFilmes()
        {
            return [self::VER_PROCURA_IMG_OFFLINE, self::VER_TROCAR_CAPA_FILMES, self::VER_PUBLICAR_FILME];
        }

        public static function getSeriesTorrent()
        {
            return [self::VER_PUBLICAR_SERIE, self::VER_ATUALIZAR_SERIE];
        }

        public static function getSeriesOnline()
        {
            return [self::VER_PUBLICA_SERIE_ONLINE];
        }

        public static function getJogos()
        {
            return [self::VER_PUBLICAR_JOGOS];
        }

        public static function getAnimes()
        {
            return [self::VER_FAZER_POSTAGEM, self::VER_FAZER_POSTAGEM_ORION, self::VER_ATUALIZAR_POSTAGEM, self::VER_VERIFICAR_POSTAGEM_ORION];
        }
        public static function getFeeds()
        {
            return [self::VER_FEEDS];
        }
    }
