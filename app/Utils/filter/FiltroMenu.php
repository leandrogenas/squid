<?php


    namespace App\Utils\filter;

    use JeroenNoten\LaravelAdminLte\Menu\Builder;
    use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

    class FiltroMenu implements FilterInterface
    {

        public function transform($item, Builder $builder)
        {
            if (isset($item["group-permission"])) {
                if (!FiltroGrupoPermissao::verifica_permissao_por_grupo($item["group-permission"])) {
                    return false;
                }
            }
            return $item;
        }
    }
