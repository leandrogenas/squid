import Site from "./Site";

export default interface ListagemSites {
    count: number
	status: 'parado' | 'carregando' | 'falhou'
	values: Site[]
}