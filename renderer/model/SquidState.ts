import ConfigMega from "./ConfigMega";
import {WebDriver} from "selenium-webdriver";
import Site from "./Site";
import Download from "./Download";
import SerieMP4Series from "./SerieMP4Series";

export default interface SquidState {
	status: 'aguardando' | 'inicializando' | 'pronto' | string;
    downloadsAberto: boolean
	configMega: ConfigMega
	chrome?: WebDriver
	paginaAtual: string
	sites: Site[]
	downloads: Download[]
	series: SerieMP4Series[]
}