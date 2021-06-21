import { createAsyncThunk } from "@reduxjs/toolkit";
import { statusMega } from "./squidAPI";

export type StatusMega = {
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
//
// export const abrirChromeThunk = createAsyncThunk(
// 	'squid/chrome/abrir',
// 	(): ThenableWebDriver =>
// 	{
// 		return new webdriver.Builder()
// 			.usingServer('http://localhost:9515')
// 			.forBrowser(Browser.CHROME)
// 			.build();
// 	}
// )

export default builder => 
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
    // builder
    // 	.addCase(abrirChromeThunk.fulfilled, (state, action) =>
    // 	{
    // 		state.chrome = action.payload;
    // 		state.chrome.get('https://gmail.com');
    // 	});
}