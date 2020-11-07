<?php


    namespace App\Models\animes;


    use Corcel\Model\Post as Corcel;

    /**
 * App\Models\animes\PostAnimesOrion
 *
 * @property int $ID
 * @property int $post_author
 * @property \Illuminate\Support\Carbon $post_date
 * @property \Illuminate\Support\Carbon $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property \Illuminate\Support\Carbon $post_modified
 * @property \Illuminate\Support\Carbon $post_modified_gmt
 * @property string $post_content_filtered
 * @property int $post_parent
 * @property string $guid
 * @property int $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property int $comment_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Corcel\Model\Post[] $attachment
 * @property-read int|null $attachment_count
 * @property-read \Corcel\Model\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Corcel\Model\Post[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Corcel\Model\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Corcel\Model\Collection\MetaCollection|\Corcel\Model\Meta\PostMeta[] $fields
 * @property-read int|null $fields_count
 * @property-read \AdvancedCustomFields $acf
 * @property-read string $content
 * @property-read string $excerpt
 * @property-read string $image
 * @property-read array $keywords
 * @property-read string $keywords_str
 * @property-read string $main_category
 * @property-read array $terms
 * @property-read \Corcel\Model\Collection\MetaCollection|\Corcel\Model\Meta\PostMeta[] $meta
 * @property-read int|null $meta_count
 * @property-read \Corcel\Model\Post $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Corcel\Model\Post[] $revision
 * @property-read int|null $revision_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Corcel\Model\Taxonomy[] $taxonomies
 * @property-read int|null $taxonomies_count
 * @property-read \Corcel\Model\Meta\ThumbnailMeta|null $thumbnail
 * @method static \Illuminate\Database\Eloquent\Builder|\Corcel\Model\Post hasMeta($meta, $value = null, $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|\Corcel\Model\Post hasMetaLike($meta, $value = null)
 * @method static \Corcel\Model\Builder\PostBuilder|\App\Models\animes\PostAnimesOrion newModelQuery()
 * @method static \Corcel\Model\Builder\PostBuilder|\App\Models\animes\PostAnimesOrion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Corcel\Model\Post newest()
 * @method static \Illuminate\Database\Eloquent\Builder|\Corcel\Model\Post oldest()
 * @method static \Corcel\Model\Builder\PostBuilder|\App\Models\animes\PostAnimesOrion query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereCommentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereMenuOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePinged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostContentFiltered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostDateGmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostModified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostModifiedGmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\animes\PostAnimesOrion whereToPing($value)
 * @mixin \Eloquent
 */
class PostAnimesOrion extends Corcel
    {
        protected $connection = "animesorion";
    }
