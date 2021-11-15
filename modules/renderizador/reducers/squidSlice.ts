import { createAsyncThunk, createSlice, PayloadAction, ThunkAction } from "@reduxjs/toolkit"
import SquidState from "../model/SquidState";
import { AppState } from "../store";
import { obterTituloPagina, sincronizarSite, statusMega } from "./squidAPI";

import downloadThunks from './Download/downloadThunks';
import serieThunks from './Serie/serieThunks';
import siteThunks from './Site/siteThunks';

import siteReducers from "./Site/siteReducers";

const initialState: SquidState = {
	status: 'aguardando',
	paginaAtual: 'Dashboard',
	downloadsAberto: false,
	configMega: {
		pidServer: undefined,
		pidShell: undefined,
		status: 'desconhecido',
		stdout: []
	},
	sites: [
		{
			status: 'sincronizado',
			uuid: '9666e8bb-c801-431f-9d9c-94443f3ccd91',
			nome: 'baixarseriesmp4.net',
			baixaveis: 132,
			baixados: 30,
			usaApi: false,
			usaWeb: false,
			usaWordpress: true,
			sincronizando: false
		}
	],
	downloads: [],
	series: [],
}

export const squidSlice = createSlice({
	name: 'squid',
	initialState,
	reducers: {
		toggleDownloadsAberto: state => 
		{
			state.downloadsAberto = !state.downloadsAberto;
		},
		setPaginaAtual: (state, action: PayloadAction<string>) =>
		{
			console.log(`página atual: ${action.payload}`);
			
			let paginaAtual = obterTituloPagina(action.payload);
			if(paginaAtual.includes(':uuid')){
				if(action.payload.includes('site')){
					const split = action.payload.split('/');
					const uuid = split[split.length - 1];

					paginaAtual = (state.sites.filter(site => site.uuid === uuid)[0]).nome;
				}
			}

			state.paginaAtual = paginaAtual;
		},
		...siteReducers,
	},
	extraReducers: {
		...siteThunks,
		...serieThunks,
		...downloadThunks,
	}
})

export const { 
	toggleDownloadsAberto, 
	setPaginaAtual,
	adicionarSite,
	excluirSite,
	listarSites,
} = squidSlice.actions

export const selectSquid = (state: AppState) => state

export default squidSlice.reducer