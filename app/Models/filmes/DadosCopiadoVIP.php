<?php

namespace App\Models\filmes;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\filmes\DadosCopiadoVIP
 *
 * @property int $id
 * @property string $title
 * @property int $post_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\filmes\DadosCopiadoVIP whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DadosCopiadoVIP extends Model
{
    protected $table = "dados_copiado_v_i_p_s";
}
