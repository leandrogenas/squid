<?php

    namespace App\Models\loadbalancer;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;

    /**
 * Class LinkLoadBalancer
 *
 * @property string link_original
 * @property string link_redirect
 * @property Carbon expira_em
 * @property string link_server
 * @package App\Models\loadbalancer
 * @property int $id
 * @property string $link_original
 * @property string $link_redirect
 * @property string $link_server
 * @property \Illuminate\Support\Carbon $expira_em
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer whereExpiraEm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer whereLinkOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer whereLinkRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\loadbalancer\LinkLoadBalancer whereLinkServer($value)
 * @mixin \Eloquent
 */
    class LinkLoadBalancer extends Model
    {
        protected $fillable = ['link_original', 'link_redirect','link_server', 'expira_em'];
        protected $table = "link_load_balancers";
        public $timestamps = false;
        protected $casts = [
            'expira_em' => "datetime"
        ];

        public static function getServerAleatorio()
        {
            $lista = [];
            try {
                $index_aleatorio = random_int(0, (count($lista) - 1));
                return $lista[$index_aleatorio];
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return $lista[0];
            }
        }
    }
