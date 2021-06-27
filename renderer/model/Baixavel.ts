import Link from "./Link";

export type BaixavelTipo = 'SERIE' | 'FILME' | 'ANIME';

export default interface Baixavel {
    uuid: string
	site: string
	tipo: BaixavelTipo
	titulo: string
	links?: Link[]
}