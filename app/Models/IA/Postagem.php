<?php

namespace App\Models\IA;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IA\Postagem
 *
 * @property int $id
 * @property string $post_id
 * @property string $url
 * @property string $title
 * @property string $tipo
 * @property string $status
 * @property string|null $log
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IA\Postagem whereUrl($value)
 * @mixin \Eloquent
 */
class Postagem extends Model
{
    protected $fillable = ["post_id","url","title","tipo","status","log"];
}
