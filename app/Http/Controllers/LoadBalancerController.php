<?php

    namespace App\Http\Controllers;

    use App\Models\loadbalancer\LinkLoadBalancer;
    use Carbon\Carbon;
    use GuzzleHttp\Client;
    use Illuminate\Http\Request;

    class LoadBalancerController extends Controller
    {
        public function redirect(Request $request)
        {
            if ($request->has("d")) {
                $link = $request->get("d");
                /***@var \App\Models\loadbalancer\LinkLoadBalancer $linkBalancer */
                $linkBalancer = LinkLoadBalancer::where("link_original", $link)->first();
                if ($linkBalancer) {
                    $linkBalancer->expira_em->addMinutes(30);
                    $linkBalancer->save();
                    return redirect()->to($linkBalancer->link_redirect);
                } else {
                    $server_aleatorio = LinkLoadBalancer::getServerAleatorio();
                    $link_server_aleatorio = $server_aleatorio . "/loadbalancer.php";
                    $cliente = new Client();
                    $response = $cliente->request("POST", $link_server_aleatorio, [
                        'form_params' => [
                            'link' => $link,
                            'file' => $this->getFileName($link),
                            'token' => env("TOKEN_LOADBALANCER", "")
                        ]
                    ]);
                    $resultado = $response->getBody()->getContents();
                    $resultado = json_decode($resultado);
                    $link_redirect = $resultado->link;
                    if (!empty($link_redirect)) {
                        LinkLoadBalancer::create([
                            "link_original" => $link,
                            "link_redirect" => $link_redirect,
                            'link_server' => $link_server_aleatorio,
                            "expira_em" => Carbon::now()->addHours(3)
                        ]);
                        return redirect()->to($link_redirect);
                    } else {
                        return redirect()->to($link);
                    }
                }
            } else {
                return abort(503);
            }
        }

        private function getFileName($link)
        {
            $re = '/assistironline\/.*\/(.*)/m';
            try {
                preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);
                return $matches[0][1];
            } catch (\Throwable $ex) {
                \Log::error($ex);
                return "";
            }
        }
    }


