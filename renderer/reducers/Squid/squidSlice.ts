import { createAsyncThunk, createSlice, PayloadAction, ThunkAction } from "@reduxjs/toolkit"
import SquidState from "../../model/SquidState";
import { AppState } from "../../store";
import { sincronizarSite, statusMega } from "./squidAPI";

const initialState: SquidState = {
	downloadsAberto: false,
	count: 0,
	configMega: {
		pidServer: undefined,
		pidShell: undefined,
		status: 'desconhecido',
		stdout: []
	}
}

type StatusMega = {
	pidServer: string
	pidShell: string
}

export const statusMegaThunk = createAsyncThunk(
	'squid/mega/status',
	async (): Promise<StatusMega> =>
	{
		return await statusMega() as unknown as StatusMega;
	}
)

export const squidSlice = createSlice({
	name: 'squid',
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
			.addCase(statusMegaThunk.fulfilled, (state, action) =>
			{
				state.configMega.status = 'rodando'
				state.configMega.pidServer = parseInt(action.payload.pidServer);
				state.configMega.pidShell = parseInt(action.payload.pidShell);
			})
			.addCase(statusMegaThunk.rejected, (state, action: any) =>
			{
				state.configMega.status = 'parado'
				state.configMega.pidServer = undefined;
			});

	}
})

export const { listar, adicionar, excluir, toggleDownloadsAberto } = squidSlice.actions

export const selectSquid = (state: AppState) => state.squid

export default squidSlice.reducer