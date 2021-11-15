import { PayloadAction } from "@reduxjs/toolkit";
import Site from "../../model/Site";
import { LSDB_SITES } from "../../model/SquidLSDB";

export default {
	listarSites: (state) =>
	{
		const ls = window.localStorage;
		const sitesStr = ls.getItem('sites');
		if(sitesStr == null)
			return;

		console.log(sitesStr);
		state.values = JSON.parse(sitesStr);
	},
	adicionarSite: (state, action: PayloadAction<Site>) =>
	{
		const ls = window.localStorage;
		if(!ls)
			throw 'ls não definido'

		if(!ls.getItem(LSDB_SITES))
			ls.setItem(LSDB_SITES, '[]');

		ls.setItem(LSDB_SITES, JSON.stringify(action.payload));
		state.values.unshift(action.payload);
		console.debug(`site ${action.payload.uuid} adicionado`);
	},
	excluirSite: state => 
	{
		console.log('excluir', state);
	},	
}