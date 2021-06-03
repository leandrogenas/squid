import { createAsyncThunk, createSlice, PayloadAction, ThunkAction } from "@reduxjs/toolkit"
import { Download, ListagemConfigs } from "../../types";
import { AppState } from "../../store";
import { fetchSeriesFromWordpress, sincronizarSite } from "./squidAPI";

const initialState: ListagemConfigs = {
	downloadsAberto: false,
	count: 0,
	configMega: {
		pidServer: undefined,
		status: 'parado',
		stdout: []
	}
}


export const iniciarMegaThunk = createAsyncThunk(
	'configs/mega/iniciar',
	async (): Promise<number> => 
	{
		console.log();

		return new Promise<number>((resolve, reject) => {
			resolve(1000);
		});
	}
)

export const pararMegaThunk = createAsyncThunk(
	'configs/mega/parar',
	async (): Promise<boolean> => 
	{
		console.log('configs/mega/parar');

		return new Promise<boolean>((resolve, reject) => {
			resolve(true);
		});
	}
)

export const configsSlice = createSlice({
	name: 'configs',
	initialState,
	reducers: {
		toggleDownloadsAberto: state => 
		{
			state.downloadsAberto = !state.downloadsAberto;
		}, 
		listar: state => 
		{
			console.log('listar', state);
		},
		adicionar: (state, uuid) => 
		{
			console.log('adicionar', state, uuid);
		},
		excluir: state => 
		{
			console.log('excluir', state);
		},	
	},
	extraReducers: builder => 
	{
		builder
			.addCase(iniciarMegaThunk.pending, (state, _action) => 
			{
                state.configMega.status = 'iniciando'
			})
			.addCase(iniciarMegaThunk.fulfilled, (state, action) => 
			{
				state.configMega.status = 'rodando'
				state.configMega.pidServer = action.payload
			})
			.addCase(iniciarMegaThunk.rejected, (state, action: any) => 
			{
				state.configMega.status = 'erro'
				state.configMega.pidServer = undefined;
			});
		builder
			.addCase(pararMegaThunk.pending, (state, _action) => 
			{
				state.configMega.status = 'parando'
			})
			.addCase(pararMegaThunk.fulfilled, (state, action: any) => 
			{
				state.configMega.status = 'parado'
				state.configMega.pidServer = undefined;
			})
			.addCase(pararMegaThunk.rejected, (state, action: any) => 
			{
				state.configMega.status = 'erro'
				state.configMega.pidServer = undefined;
			})
	}
})

export const { listar, adicionar, excluir, toggleDownloadsAberto } = configsSlice.actions

export const selectConfigs = (state: AppState) => state.configs

export default configsSlice.reducer