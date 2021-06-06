import Serie from "./Serie";
import Site from "./Site";

export default interface Download {
    uuid: string
	nome: string
	progresso: number
	status: 'baixando' | 'parado'
	tipo: string
	site: Site
	serie: Serie
}