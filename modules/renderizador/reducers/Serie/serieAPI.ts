//import axios from "axios";
import Dexie from 'dexie'
import Serie from '../../model/Serie';

import SquidDB, { idb } from '../../model/SquidIDB'

export async function listarSeriesDexie(count: number = 1)
{

    return idb.serie.toArray() as Promise<Serie[]>;
}

export async function salvarSeries(series: Serie[])
{
  return idb.serie.bulkPut(series);
}