import Download from "./Download";

export default interface ListagemDownloads {
    count: number
	status: 'adicionando' | 'aguardando' | 'buscando'
	values: Download[]
}