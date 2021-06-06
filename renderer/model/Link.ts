export type ProvedorLink = 'MEGA' | 'PANDA' | 'OUTRO';
export type QualidadeLink = '480P' | '720P' | '1080P' | 'OUTRO';
export type TipoIdiomaLink = 'DUBLADO' | 'LEGENDADO' | 'OUTRO';
export type FormatoLink = 'MP4' | 'OUTRO';

export default interface Link {
	provedor: ProvedorLink
	qualidade: QualidadeLink
	tipoIdioma: TipoIdiomaLink
	formato: FormatoLink
    titulo: string
	linkOriginal: string
	linkConvertido?: string
}

export interface LinkMega extends Link {
	provedor: 'MEGA'
}