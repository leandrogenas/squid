<?php


namespace App\Models\firebase;


use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseUtil
{
    public static function enviarNotificacao($topic,$title,$body,$image){
        try{
            $enviador = Firebase::messaging();
            $mensagem = CloudMessage::withTarget('topic', $topic)->withNotification(['title'=>$title,'body'=>$body,'image'=>$image]);
            $enviador->send($mensagem);
            return "Notificação post_id $topic, title $title Enviada\n";
        }catch (\Throwable $ex){
            \Log::error($ex);
            return "Houve um erro ao enviar a notificação POST_id: $topic, title: $title , erro: ".$ex->getMessage()."\n";
        }

    }
}
