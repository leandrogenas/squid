import Dexie, { Table } from 'dexie'
import { Download, LinkMega, SerieWordpress } from '../types'

class SquidDB extends Dexie {
    downloads!: Table<Download, number>;
    series!: Table<SerieWordpress, number>;
    links!: Table<LinkMega, number>;

    constructor()
    {
        super('squid')
        this.version(1).stores({
            download: '&uuid',
            serie: '&uuid,&site',
            links: '&uuid'
        });
    }

}

export const idb = new SquidDB();

export default SquidDB