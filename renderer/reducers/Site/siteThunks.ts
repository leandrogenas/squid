import { createAsyncThunk } from "@reduxjs/toolkit";
import { salvarSeries } from "../Serie/serieAPI";
import { fetchSeriesFromWordpress } from "./siteAPI";
import * as mp4seriesAPI from "./mp4seriesAPI";
import Serie from "../../model/Serie";

export const sincronizarSiteThunk = createAsyncThunk(
	'squid/site/sincronizar',
	async (siteUUID: string) => 
	{
		const series = await fetchSeriesFromWordpress(1);
		console.log('series obtidas: ' + series.length);
		console.groupCollapsed('gravando no indexed db');
		const ok = await salvarSeries(series.map(s => {
			try{
				console.log(`serie ${s.titulo}`)
				const links = mp4seriesAPI.extrairLinks(s);
				// console.log(`convertendo links...`);
				// const linksConvertidos = api.converterLinks(links);
				// console.log(`links convertidos!`);
				return {
					...s,
					siteUUID: siteUUID,
					tituloShow: mp4seriesAPI.limparTituloSlug(s.slug),
					links: links,
					infos: mp4seriesAPI.extrairInfosPost(s),
					// linksConvertidos: linksConvertidos
				} as Serie;
			}catch(e){
				console.error(e);
				return {
					...s,
					siteUUID: siteUUID,
				}
			}
		}));
		console.groupEnd();
		console.log('resultado da gravação: ' + ok);

		return series;
	}
)

export const listarSitesThunk = createAsyncThunk(
    'squid/site/listar',
    async (qtd: number = 1) => 
    {
		
        //const series = await fetchSeriesFromWordpress(qtd);

        return [];
    }
)

const extraReducers = builder => 
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

export default extraReducers