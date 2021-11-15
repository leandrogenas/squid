import { createAsyncThunk } from "@reduxjs/toolkit";
import Link from "../../model/Link";
import Serie from "../../model/Serie";
import { converterLinks } from "../Site/mp4seriesAPI";
import { listarSeriesDexie } from "./serieAPI";

export const listarSeriesThunk = createAsyncThunk(
    'squid/serie/listar',
    (qtd: number = 1)
        : Promise<Serie[]> =>
    {
		return listarSeriesDexie(qtd) as Promise<Serie[]>;
    }
)

export const converterLinksThunk = createAsyncThunk(
	'squid/serie/converter', 
	(serie: Serie) =>
	{
		return {
			serie: serie,
			links: converterLinks(serie.links as Link[]),
		}
	}
)

const extraReducers = builder => 
{
    builder
        .addCase(listarSeriesThunk.pending, (state, action) => {
            state.status = 'carregando'
        })
        .addCase(listarSeriesThunk.fulfilled, (state, action) => 
        {
            state.status = 'pronto'
            state.values = action.payload as Serie[];	
        })
    builder
        .addCase(converterLinksThunk.pending, state => {
            state.status = 'carregando'
        })
        .addCase(converterLinksThunk.fulfilled, (state, action) => 
        {
            state.status = 'aguardando';
            
            state.values = state.values.map(v => {
                if(v.uuid != action.payload.serie.uuid)
                    return v;
                
                return {
                    ...v,
                    linksConvertidos: action.payload.links
                } as Serie;
            })
        })
}

export default extraReducers;