import { createAsyncThunk, createSlice } from "@reduxjs/toolkit"
import ListagemSeries from "../../model/ListagemSeries";
import { AppState } from "../../store";
import { listarSeriesDexie as listarSeries } from "./seriesAPI";

const initialState: ListagemSeries = {
	count: 0,
	status: 'aguardando',
	values: []
}


export const listarSeriesThunk = createAsyncThunk(
    'series/listar',
    async (qtd: number = 1) => 
    {
		return listarSeries(qtd);
    }
)

export const seriesSlice = createSlice({
	name: 'series',
	initialState,
	reducers: {
		listar: state => 
		{
			console.log('listar', state);
		},
		adicionar: state => 
		{
			console.log('adicionar', state);
		},
		excluir: state => 
		{
			console.log('excluir', state);
		},	
	},
	extraReducers: builder => 
	{
		builder
			.addCase(listarSeriesThunk.pending, (state, action) => {
				state.status = 'carregando'
			})
			.addCase(listarSeriesThunk.fulfilled, (state, action) => 
			{
				state.status = 'carregado'
				state.values = action.payload				
			})
	}
})

export const { listar, adicionar, excluir } = seriesSlice.actions

export const selectSeries = (state: AppState) => state.series

export default seriesSlice.reducer