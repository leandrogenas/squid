import { createAsyncThunk, createSlice } from "@reduxjs/toolkit"
import { ListagemSeries, SerieWordpress } from "../../types";
import { AppState } from "../../store";

const initialState: ListagemSeries = {
	count: 0,
	status: 'aguardando',
	values: []
}


export const listarSeriesThunk = createAsyncThunk(
    'series/listar',
    async (qtd: number = 1) => 
    {
        return await new Promise<SerieWordpress[]>(
			(resolve, reject) => {
				let open = indexedDB.open('squid', 1)

				open.onupgradeneeded = () => {
					console.info('Foi preciso recriar o BD')
					let db = open.result
					db.createObjectStore('series', { autoIncrement: true, keyPath: 'uuid' })
					db.createObjectStore('downloads', { autoIncrement: true, keyPath: 'uuid' })
				}

				open.onsuccess = () => {
					var transaction = open.result.transaction("series", 'readwrite');
					var objectStore = transaction.objectStore("series");
					var request = objectStore.getAll();
		
					request.onerror = function(event) {
						reject(event)
					};
					request.onsuccess = function(event) {
						resolve(request.result as SerieWordpress[]);
					};
				}
				
			}
		);
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