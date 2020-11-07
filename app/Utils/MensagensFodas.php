<?php


    namespace App\Utils;


    class MensagensFodas
    {
        public static $mensagens = [
            [
                "text" => "A nossa maior ilusão é acreditar que somos o que pensamos ser",
                "by" => "Henri Amiel"
            ],
            [
                "text" => "Há muitas razões para duvidar e uma só para crer.",
                "by" => "Carlos Drummond de Andrade"
            ],
            [
                "text" => "Tudo o que um sonho precisa para ser realizado é alguém que acredite que ele possa ser realizado.",
                "by" => "Roberto Shinyashiki"
            ],
            [
                "text" => "Somente programador raiz usa laravel!",
                "by" => "Jeanderson O Foda"
            ],
            [
                "text" => "No fundo todos somos nutelas!",
                "by" => "Jeanderson O Foda"
            ],
            [
                "text" => "Viver é a coisa mais rara do mundo. A maioria das pessoas apenas existe.",
                "by" => "Oscar Wilde"
            ],
            [
                "text" => "A nossa maior glória não reside no fato de nunca cairmos, mas sim em levantarmo-nos sempre depois de cada queda.",
                "by" => "Oliver Goldsmith"
            ],
            [
                "text" => "Coloque a lealdade e a confiança acima de qualquer coisa; não te alies aos moralmente inferiores; não receies corrigir teus erros.",
                "by" => "Confúcio"
            ],
            [
                "text" => "O maior erro que você pode cometer é o de ficar o tempo todo com medo de cometer algum.",
                "by" => "Elbert Hubbard"
            ],
            [
                "text" => "Não há problema em ser nutella, mas pelo menos seja um nutela raiz",
                "by" => "Jeanderson O Foda"
            ],
            [
                "text" => "A terra é o sistema operacional e nós somos o bug.",
                "by" => "Jeanderson O Foda"
            ],
            [
                "text" => "Se você é feio, vai continuar sendo feio, mas seja um feio foda!",
                "by" => "Jeanderson O Foda"
            ],
            [
                "text" => "Hoje é dia de maldade piratinha se prepara hahahah",
                "by" => "Sync"
            ]
        ];

        public static function mensagem_aleatoria(){
            return array_rand(self::$mensagens,1);
        }
    }
