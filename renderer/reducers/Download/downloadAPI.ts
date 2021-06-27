import Download from "../../model/Download";
import SquidDB, { idb } from "../../model/SquidIDB"
import {ProvedorLink} from "../../model/Link";


export async function listarDownloads()
{
  return idb.download.toArray();
}

export async function novoDownload(download: Download)
{
  return await idb.download.put(download) as unknown as string;
}

export function obterPercentagem(str: string, tipo: ProvedorLink)
{
  return (tipo == 'MEGA')
    ? parseFloat(str.substr(-11, 5))
    : (([baixado, total]) => parseFloat(baixado) / parseFloat(total))(str.split('/'))
}