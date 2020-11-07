<?php

    namespace App\Models\feed;

    use App\Enums\FeedLinkStatus;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;


    /**
 * App\Models\feed\ListFeed
 *
 * @property int $id
 * @property string $link
 * @property string $titulo
 * @property string $tipo
 * @property string|null $tipo_link
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereTipoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\feed\ListFeed whereUpdatedAt($value)
 * @mixin \Eloquent
 */
    class ListFeed extends Model
    {
        protected $fillable = ["link", "status", "titulo", "tipo", "tipo_link"];


        public static function limparLinksAntigo($dias = 30)
        {
            try {
                $data = Carbon::now()->subDays($dias);
                ListFeed::where("created_at", "<=", $data)->delete();
            } catch (\Throwable $ex) {
                \Log::error($ex);
                \Log::info("Houve um erro ao deletar links");
            }
        }

        /**
         * @param $tipo
         * @param $tipo_link
         * @return \Illuminate\Database\Eloquent\Builder|Model|object|null|ListFeed
         */
        public static function getUltimoLink($tipo, $tipo_link)
        {
            return ListFeed::where("tipo_link", $tipo_link)->where("status",FeedLinkStatus::ENVIADO)->where("tipo", $tipo)->latest()->first();
        }
    }
