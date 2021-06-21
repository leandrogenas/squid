import { createAsyncThunk } from "@reduxjs/toolkit";
import Download from "../../model/Download";
import { listarDownloads, novoDownload } from "./downloadAPI";

export const pararDownloadThunk = createAsyncThunk(
	'downloads/parar',
	async (uuid: number) => 
	{
		console.log('downloads/parar: ', uuid);

		return 'wat';
	}
)

export const listarDownloadsThunk = createAsyncThunk(
    'downloads/listar',
    (qtd: number = 1): Promise<Download[]> =>
    {
		return listarDownloads();
		// return new Promise<Download[]>((resolve, reject) => {
		// 	let open = indexedDB.open('squid', 1)
	
		// 	open.onsuccess = () => {
		// 		var transaction = open.result.transaction('downloads', 'readwrite');
		// 		var objectStore = transaction.objectStore('downloads');
		// 		var request = objectStore.getAll();
	
		// 		request.onerror = function(event) {
		// 			reject(event)
		// 		};
		// 		request.onsuccess = function(event) {
		// 			resolve(request.result);
		// 		};
		// 	}
	
		// 	open.onupgradeneeded = () => {
		// 		console.info('Foi preciso recriar o BD')
		// 		let db = open.result
		// 		db.createObjectStore('downloads', { autoIncrement: true, keyPath: 'uuid' })
		// 		db.createObjectStore('series', { autoIncrement: true, keyPath: 'uuid' })
		// 	}
				
		// })
    }
)

export const novoDownloadThunk = createAsyncThunk(
	'downloads/novo',
	(download: Download): Promise<string> =>
	{
		return novoDownload(download);
		// return new Promise((resolve, reject) => {
		// 	let open = indexedDB.open('squid', 1)
			
		// 	open.onsuccess = () => {
		// 		var transaction = open.result.transaction('downloads', 'readwrite');
		// 		var objectStore = transaction.objectStore('downloads');
		// 		var request = objectStore.put(download);
	
		// 		request.onerror = function(event) {
		// 			reject(event)
		// 		};
		// 		request.onsuccess = function(_event) {
		// 			resolve(request.result.toString());
		// 		};
		// 	}
	
		// 	open.onupgradeneeded = () => {
		// 		console.info('Foi preciso recriar o BD')
		// 		let db = open.result
		// 		db.createObjectStore('downloads', { autoIncrement: true, keyPath: 'uuid' })
		// 		db.createObjectStore('series', { autoIncrement: true, keyPath: 'uuid' })
				
		// 	}
				
		// })
	}
)

const extraReducers = builder => 
{
    builder
        .addCase(novoDownloadThunk.pending, (state, _action) => 
        {
            state.status = 'adicionando'
        })
        .addCase(novoDownloadThunk.fulfilled, (state, action: any) => 
        {
            console.log(state, action)
            state.status = 'aguardando'
            state.values = action.payload.payload
        });
    builder
        .addCase(listarDownloadsThunk.pending, (state, _action) => 
        {
            state.status = 'buscando'
        })
        .addCase(listarDownloadsThunk.fulfilled, (state, action: any) => 
        {
            console.log(state, action)
            state.status = 'aguardando'
            state.values = action.payload.payload
        })
}

export default extraReducers;