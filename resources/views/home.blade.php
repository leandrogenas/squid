@extends('adminlte::page')
@php
    $mensagens = \App\Utils\MensagensFodas::mensagem_aleatoria();
    $mensagem = \App\Utils\MensagensFodas::$mensagens[$mensagens]
@endphp
@section('title', 'AdminLTE')
@section('content_header')
    <h1>Seja Bem-Vindo {{auth()->user()->name}}</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-text-width"></i>
                        Mensagens fodas para você
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <blockquote>
                        <p>{{$mensagem['text']}}</p>
                        <small>{{$mensagem["by"]}}</small>
                    </blockquote>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <div class="form-group">
        <img src="{{asset("img/logo/mestre.jpg")}}" class="img-fluid">
    </div>
@stop
