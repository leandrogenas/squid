<?php


namespace App\Models\series;


use voku\helper\HtmlDomParser;

class RjSeries
{
    public static function getLinkPlayer($post_id){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www1.rjseries.net/wp-admin/admin-ajax.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = array(
            'post' => $post_id,
            'nume' => '1',
            'type' => 'tv',
            'action' => 'doo_player_ajax'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $dom = HtmlDomParser::str_get_html($output);
        return $dom->findOne("iframe")->getAttribute("src");
    }
}
