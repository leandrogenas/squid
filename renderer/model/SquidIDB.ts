import Dexie, { Table } from 'dexie'
import Download from './Download';
import Link from './Link';
import Serie from './Serie';

export const IDB_NAME = 'squid';
export const IDB_VERSION = 1;

class SquidIDB extends Dexie {
    download!: Table<Download, number>;
    serie!: Table<Serie, number>;
    link!: Table<Link, number>;

    constructor()
    {
        super(IDB_NAME);
        this.version(IDB_VERSION).stores({
            site: '&uuid',
            serie: '&uuid,site',
            download: '&uuid',
        });
    }

}

export const idb = new SquidIDB;

export default SquidIDB;