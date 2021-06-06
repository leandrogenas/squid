//import axios from "axios";
import SquidDB, { idb } from "../../model/SquidDB"
import { Download, Site } from "../../types"

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
  return idb.downloads.toArray();
}

export async function novoDownload(download: Download)
{
  return await idb.downloads.put(download) as unknown as string;
}