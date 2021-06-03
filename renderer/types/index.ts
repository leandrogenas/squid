// You can include shared interfaces/types in a separate file
// and then use them in any component by importing them. For
// example, to import the interface below do:
//
// import User from 'path/to/interfaces';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import parse, { HTMLReactParserOptions } from "html-react-parser";
import { BrowserWindow, IpcRenderer } from 'electron'

declare global {
  // eslint-disable-next-line @typescript-eslint/no-namespace
  namespace NodeJS {
    interface Global {
      ipcRenderer: IpcRenderer,
	  mainWindow: BrowserWindow
    }
  }
}

export type User = {
  id: number
  name: string
}

export type Site = {
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

export type ListagemSites = {
	count: number
	status: 'parado' | 'carregando' | 'falhou'
	values: Site[]
	
}

export type Download = {
	uuid: string
	nome: string
	progresso: number
	status: 'baixando' | 'parado'
	tipo: string
	site: Site
	serie: SerieWordpress
}

export type ListagemDownloads = { 
	count: number
	status: 'adicionando' | 'aguardando' | 'buscando'
	values: Download[]
}

export type Tabela = (data: any[]) => {
	coluna: any
	larguras: number[]
  };

export type Baixavel = {
	uuid: string
	tipo: 'serie' | 'filme' | 'anime'
	nome: string
	links: string[]
}

export interface SerieWordpress extends Baixavel {
	tipo: 'serie'
	wpCategory: number
	modificado: string
	post: string
	sinopse: string
	html: string
	uuid: string
	titulo: string
}

export type ListagemSeries = {
	count: number
	status: 'aguardando' | 'carregando' | 'carregado'
	values: Baixavel[]
}

export type LinkMega = {
	titulo: string
	linkOriginal: string
	linkConvertido?: string
}

export type ConfigMega = {
	status: 'iniciando' | 'rodando' | 'parando' | 'parado' | 'erro',
	pidServer?: number
	stdout: string[]
}

export type ListagemConfigs = {
	downloadsAberto: boolean
	count: number
	configMega: ConfigMega
	
}