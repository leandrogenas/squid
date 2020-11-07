<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function limpar_cache(){
        Artisan::call("config:clear");
        Artisan::call("cache:clear");
        return "Cache limpado, pode fechar!";
    }

    public function lista_episodios(){
        try {
            $texto = File::get("lista_episodios.txt");
        } catch (FileNotFoundException $e) {
            $texto = $e->getMessage();
        }
        return view("screens.json.lista-json")->with(["texto"=>$texto]);
    }

    public function excluir_lista(){
        File::delete("lista_episodios.txt");
        return redirect()->back();
    }
}
