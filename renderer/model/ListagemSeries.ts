import Baixavel from "./Baixavel";

export default interface ListagemSeries {
	count: number
	status: 'aguardando' | 'carregando' | 'pronto' | 'erro'
	values: Baixavel[]
}