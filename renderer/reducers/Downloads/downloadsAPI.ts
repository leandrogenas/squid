//import axios from "axios";
import Download from "../../model/Download";
import SquidDB, { idb } from "../../model/SquidIDB"

// export async function sincronizarSite(id: number)
//     : Promise<{ data: number }> 
// {
//   console.log(id)
//     const response = await fetch('/api/counter', {
//       method: 'POST',
//       headers: {
//         'Content-Type': 'application/json',
//       }
//       // body: JSON.stringify({ amount }),
//     })
//     const result = await response.json()
  
//     return result
// }

// export function listarSites(count: number = 1)
//     : Site[]
// {
//   console.log(count);
//     return []
// }

export async function listarDownloads()
{
  return idb.download.toArray();
}

export async function novoDownload(download: Download)
{
  return await idb.download.put(download) as unknown as string;
}

export function obterPerc(str: string, tipo: 'Mega' | 'Panda')
{
  return (tipo == 'Mega')
    ? parseFloat(str.substr(-11, 5))
    : (([baixado, total]) => parseFloat(baixado) / parseFloat(total))(str.split('/'))
}