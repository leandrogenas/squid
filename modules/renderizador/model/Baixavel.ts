import Link from "./Link";

export enum Baixaveis {
	SERIE='SERIE', FILME='FILME', ANIME='ANIME'
}

export type BaixavelTipo = keyof typeof Baixaveis;

export default interface Baixavel {
    uuid?: string
	site?: string
	tipo?: Baixaveis
	titulo?: string
	links?: Link[]
}