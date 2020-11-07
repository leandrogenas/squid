@extends('adminlte::page')
@php
    $data_agora = \Carbon\Carbon::now();
@endphp
@section('content')
    @component("layouts.componentes.box",["title"=>"Data Ultimos Links Enviados"])
        <div class="form-group">
            <canvas id="barChart"></canvas>
        </div>
        <hr>
        <div class="row">
            @foreach($dados as $key => $value)
                <div class="col-md-3">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">{{$key}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <i class="fab fa-google-drive"></i> <strong>Drive: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::GOOGLE_DRIVE]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::GOOGLE_DRIVE]->updated_at)}} Hora(s)
                            </div>
{{--                            <hr>--}}
{{--                            <div class="form-group">--}}
{{--                                <i class="fab fa-nimblr"></i> <strong>Nimbus: </strong>--}}
{{--                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::NIMBUS]->updated_at)}} Dia(s) ---}}
{{--                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::NIMBUS]->updated_at)}} Hora(s)--}}
{{--                            </div>--}}
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-evernote"></i> <strong>Evernote: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::EVERNOTE]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::EVERNOTE]->updated_at)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-twitter"></i> <strong>Twitter: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TWITTER]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::TWITTER]->updated_at)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-wordpress"></i> <strong>Wordpress: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::WORDPRESS]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::WORDPRESS]->updated_at)}} Hora(s)
                            </div>
                            <hr>
                            @if (isset($value[\App\Enums\TipoLinksFeed::PINTEREST]))
                            <div class="form-group">
                                <i class="fab fa-pinterest"></i> <strong>Pinterest: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::PINTEREST]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::PINTEREST]->updated_at)}} Hora(s)
                            </div> 
                            @endif                            
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-tumblr"></i> <strong>TUMBLR: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TUMBLR]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::TUMBLR]->updated_at)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-blogger"></i> <strong>BLOGGER: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::BLOGGER]->updated_at)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::BLOGGER]->updated_at)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-trello"></i> <strong>Trello: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TRELLO]->updated_at ?? $data_agora)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::TRELLO]->updated_at ?? $data_agora)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-weebly"></i> <strong>Weebly: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::WEEBLY]->updated_at ?? $data_agora)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::WEEBLY]->updated_at ?? $data_agora)}} Hora(s)
                            </div>
                            <hr>
                            <div class="form-group">
                                <i class="fab fa-bity"></i> <strong>BitLy: </strong>
                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::BITLY]->updated_at ?? $data_agora)}} Dia(s) -
                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::BITLY]->updated_at ?? $data_agora)}} Hora(s)
                            </div>
                            <hr>
{{--                            <div class="form-group">--}}
{{--                                <i class="fab fa-newspaper"></i> <strong>Narro: </strong>--}}
{{--                                {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::NARRO]->updated_at ?? $data_agora)}} Dia(s) ---}}
{{--                                {{$data_agora->diffInHours($value[\App\Enums\TipoLinksFeed::NARRO]->updated_at ?? $data_agora)}} Hora(s)--}}
{{--                            </div>--}}
{{--                            <hr>--}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endcomponent
@endsection
@section("js")
    <script>

        $(function () {
            /* ChartJS
             * -------
             * Here we will create a few charts using ChartJS
             */

            //--------------
            //- AREA CHART -
            //--------------

            // Get context with jQuery - using jQuery's .get() method.

            let areaChartData = {
                labels  : [
                    @foreach($dados as $key =>$value)
                        '{{$key}}',
                    @endforeach
                ],
                datasets: [

                    {{--{--}}
                    {{--    label               : '{{\App\Enums\TipoLinksFeed::NIMBUS}}',--}}
                    {{--    backgroundColor     : 'rgba(210, 214, 222, 1)',--}}
                    {{--    borderColor         : 'rgba(210, 214, 222, 1)',--}}
                    {{--    pointRadius         : false,--}}
                    {{--    pointColor          : 'rgba(210, 214, 222, 1)',--}}
                    {{--    pointStrokeColor    : '#c1c7d1',--}}
                    {{--    pointHighlightFill  : '#fff',--}}
                    {{--    pointHighlightStroke: 'rgba(220,220,220,1)',--}}
                    {{--    data                : [--}}
                    {{--        @foreach($dados as $key =>$value)--}}
                    {{--        {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::NIMBUS]->updated_at)}},--}}
                    {{--        @endforeach--}}
                    {{--    ]--}}
                    {{--},--}}
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::GOOGLE_DRIVE}}',
                        backgroundColor     : 'rgba(37,191,53,0.9)',
                        borderColor         : 'rgba(37,191,53,0.9)',
                        pointRadius          : false,
                        pointColor          : '#1ac445',
                        pointStrokeColor    : 'rgba(37,191,53,0.9)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(37,191,53,0.9)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::GOOGLE_DRIVE]->updated_at)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::EVERNOTE}}',
                        backgroundColor     : 'rgb(40,103,229)',
                        borderColor         : 'rgb(40,103,229)',
                        pointRadius         : false,
                        pointColor          : 'rgb(40,103,229)',
                        pointStrokeColor    : '#276de0',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::EVERNOTE]->updated_at)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::TWITTER}}',
                        backgroundColor     : 'rgb(11,169,243)',
                        borderColor         : 'rgb(11,169,243)',
                        pointRadius         : false,
                        pointColor          : 'rgb(11,169,243)',
                        pointStrokeColor    : '#0ab9ee',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TWITTER]->updated_at)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::WORDPRESS}}',
                        backgroundColor     : 'rgb(243,104,11)',
                        borderColor         : 'rgb(243,104,11)',
                        pointRadius         : false,
                        pointColor          : 'rgb(243,104,11)',
                        pointStrokeColor    : '#ee6d0a',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::WORDPRESS]->updated_at)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::PINTEREST}}',
                        backgroundColor     : 'rgb(200,43,243)',
                        borderColor         : 'rgb(200,43,243)',
                        pointRadius         : false,
                        pointColor          : 'rgb(200,43,243)',
                        pointStrokeColor    : 'rgb(200,43,243)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            @if (isset($value[\App\Enums\TipoLinksFeed::PINTEREST]))
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::PINTEREST]->updated_at)}},
                            @else
                            0,
                            @endif
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::TUMBLR}}',
                        backgroundColor     : 'rgb(214,11,140)',
                        borderColor         : 'rgb(214,11,140)',
                        pointRadius         : false,
                        pointColor          : 'rgb(214,11,140)',
                        pointStrokeColor    : 'rgb(214,11,140)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TUMBLR]->updated_at)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::BLOGGER}}',
                        backgroundColor     : 'rgb(162,141,5)',
                        borderColor         : 'rgb(162,141,5)',
                        pointRadius         : false,
                        pointColor          : 'rgb(162,141,5)',
                        pointStrokeColor    : 'rgb(162,141,5)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::BLOGGER]->updated_at ?? $data_agora)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::TRELLO}}',
                        backgroundColor     : 'rgb(221,57,7)',
                        borderColor         : 'rgb(221,57,7)',
                        pointRadius         : false,
                        pointColor          : 'rgb(221,57,7)',
                        pointStrokeColor    : 'rgb(221,57,7)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::TRELLO]->updated_at ?? $data_agora)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::WEEBLY}}',
                        backgroundColor     : 'rgb(226,233,17)',
                        borderColor         : 'rgb(226,233,17)',
                        pointRadius         : false,
                        pointColor          : 'rgb(226,233,17)',
                        pointStrokeColor    : 'rgb(226,233,17)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::WEEBLY]->updated_at ?? $data_agora)}},
                            @endforeach
                        ]
                    },
                    {
                        label               : '{{\App\Enums\TipoLinksFeed::BITLY}}',
                        backgroundColor     : 'rgb(7,142,153)',
                        borderColor         : 'rgb(7,142,153)',
                        pointRadius         : false,
                        pointColor          : 'rgb(7,142,153)',
                        pointStrokeColor    : 'rgb(7,142,153)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : [
                            @foreach($dados as $key =>$value)
                            {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::BITLY]->updated_at ?? $data_agora)}},
                            @endforeach
                        ]
                    },
                    {{--{--}}
                    {{--    label               : '{{\App\Enums\TipoLinksFeed::NARRO}}',--}}
                    {{--    backgroundColor     : 'rgb(120,9,246)',--}}
                    {{--    borderColor         : 'rgb(120,9,246)',--}}
                    {{--    pointRadius         : false,--}}
                    {{--    pointColor          : 'rgb(120,9,246)',--}}
                    {{--    pointStrokeColor    : 'rgb(120,9,246)',--}}
                    {{--    pointHighlightFill  : '#fff',--}}
                    {{--    pointHighlightStroke: 'rgba(220,220,220,1)',--}}
                    {{--    data                : [--}}
                    {{--        @foreach($dados as $key =>$value)--}}
                    {{--        {{$data_agora->diffInDays($value[\App\Enums\TipoLinksFeed::NARRO]->updated_at ?? $data_agora)}},--}}
                    {{--        @endforeach--}}
                    {{--    ]--}}
                    {{--},--}}
                ]
            }

            //-------------
            //- BAR CHART -
            //-------------
            let barChartCanvas = $('#barChart').get(0).getContext('2d')
            let barChartData = jQuery.extend(true, {}, areaChartData)
            let temp0 = areaChartData.datasets[0]
            let temp1 = areaChartData.datasets[1]
            barChartData.datasets[0] = temp1
            barChartData.datasets[1] = temp0

            let barChartOptions = {
                responsive              : true,
                maintainAspectRatio     : false,
                datasetFill             : false,
                scales: {
                    yAxes: [{
                        ticks: {
                            suggestedMin: 1,
                            suggestedMax: 5
                        }
                    }]
                }
            }

            let barChart = new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            })
        })
    </script>
@endsection
