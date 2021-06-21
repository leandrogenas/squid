//import axios from "axios";

import Site from "../model/Site";

export async function statusMega()
{
  return new Promise(
      (resolve, reject) => {
        const TIMEOUT = 1500;
        setTimeout(() => {
          reject()
        }, TIMEOUT);

        global.ipcRenderer.send('mega', 'status')
        global.ipcRenderer.once('mega', (_event, pids) => {
            resolve(pids);
        })
      }
  )

}

export async function sincronizarSite(id: number)
    : Promise<{ data: number }> 
{
  console.log(id)
    const response = await fetch('/api/counter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
      // body: JSON.stringify({ amount }),
    })
    const result = await response.json()
  
    return result
}

export function obterTituloPagina(pathname: string)
{
  switch(pathname){
    case '/index':
    case '/': return 'Dashboard';
    case '/sites': return 'Sites';
    default: return ':uuid';
  }
}

export function listarSites(count: number = 1)
    : Site[]
{
  console.log(count);
    return []
}