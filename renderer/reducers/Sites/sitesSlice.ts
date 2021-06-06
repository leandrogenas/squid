import { createAsyncThunk, createSlice } from "@reduxjs/toolkit"
import { AppState } from "../../store";
import { fetchSeriesFromWordpress } from "./sitesAPI";
import { salvarSeries } from '../Series/seriesAPI';
import ListagemSites from "../../model/ListagemSites";
import * as api from "../Series/mp4seriesAPI";
import Serie from "../../model/Serie";


const initialState: ListagemSites = {
	count: 0,
	status: 'parado',
	values: []
}


export const sincronizarSiteThunk = createAsyncThunk(
	'sites/sincronizar',
	async (siteUUID: string) => 
	{
		const series = await fetchSeriesFromWordpress(1);
		console.log('series obtidas: ' + series.length);
		console.log('gravando no indexed db');
		const ok = await salvarSeries(series.map(s => {
			try{
				return {
					...s,
					siteUUID: siteUUID,
					tituloShow: api.limparTituloSlug(s.slug),
					links: api.extrairLinks(s),
					infos: api.extrairInfosPost(s)
				} as Serie;
			}catch(e){
				console.error(e);
				return {
					...s,
					siteUUID: siteUUID,
				}
			}
		}));
		console.log('resultado da gravação: ' + ok);

		return series;
	}
)

export const listarAsync = createAsyncThunk(
    'sites/listar',
    async (qtd: number = 1) => 
    {
		
        //const series = await fetchSeriesFromWordpress(qtd);

        return [];
    }
)

export const sitesSlice = createSlice({
	name: 'sites',
	initialState,
	reducers: {
		listar: (state) => 
		{
			const ls = window.localStorage;
			const sitesStr = ls.getItem('sites');
			if(sitesStr == null)
				return;

			console.log(sitesStr);
			state.values = JSON.parse(sitesStr);
		},
		adicionar: (state, site) => 
		{
			const ls = window.localStorage;
			if(!ls)
				throw 'ls não definido'

			
			
		},
		excluir: state => 
		{
			console.log('excluir', state);
		},	
	},
	extraReducers: builder => 
	{
		builder
			.addCase(sincronizarSiteThunk.pending, state => {
				state.status = 'carregando' 
			})
			.addCase(sincronizarSiteThunk.fulfilled, (state, action) => 
			{
				state.status = 'parado';
				state.values = action.payload
			})
	}
})

export const { listar, adicionar, excluir } = sitesSlice.actions

export const selectSites = (state: AppState) => state.sites

export default sitesSlice.reducer