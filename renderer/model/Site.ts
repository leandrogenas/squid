export default interface Site {
    uuid: string
	status: 'sincronizado' | 'erro' | 'desatualizado'
	nome: string
	baixaveis: number
	baixados: number
	usaWordpress: boolean
	usaApi: boolean
	usaWeb: boolean
	sincronizando: boolean
}