import Site from "./Site";

export default interface ListagemSites {
    count: number
	status: 'parado' | 'carregando' | 'pronto' | 'falhou'
	values: Site[]
}