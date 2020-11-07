<?php

    namespace App\Http\Controllers;

    use App\User;
    use Illuminate\Http\Request;

    class UsuariosController extends Controller
    {
        public function index()
        {
            $usuarios = User::all();
            return view("screens.users.usuarios-lista")->with(['usuarios' => $usuarios]);
        }

        public function create()
        {
            return view("screens.users.create-usuario");
        }

        public function cadastrar(Request $request)
        {
            $usuario = User::create([
                "name" => $request->post('name'),
                'email' => $request->post("email"),
                "password" => \Hash::make($request->post("password"))
            ]);
            $permissoes = [];
            foreach ($request->post("permissoes") as $permissao) {
                $permissoes[] = $permissao;
            }
            $usuario->syncPermissions($permissoes);
            alert()->success("Pronto", "Usuario cadastrado com sucesso");
            return redirect()->back();
        }

        public function edit($id){
            $usuario = User::find($id);
            return view("screens.users.edit-usuario")->with(["usuario"=>$usuario]);
        }
        public function update($id,Request $request){
            $usuario = User::find($id);
            $data = $request->except(["_token","password"]);
            if(!empty($request->post("password"))){
                $data["password"] = \Hash::make($request->post("password"));
            }
            $usuario->update($data);
            $permissoes = [];
            foreach ($request->post("permissoes") as $permissao) {
                $permissoes[] = $permissao;
            }
            $usuario->syncPermissions($permissoes);
            alert()->success("Pronto", "Usuario Editado com sucesso");
            return redirect()->route("usuarios.lista");
        }
    }
