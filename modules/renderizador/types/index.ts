// You can include shared interfaces/types in a separate file
// and then use them in any component by importing them. For
// example, to import the interface below do:
//
// import User from 'path/to/interfaces';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { BrowserWindow, IpcRenderer } from 'electron'
import Site from '../model/Site';


declare global {
  // eslint-disable-next-line @typescript-eslint/no-namespace
  namespace NodeJS {
    interface Global {
      ipcRenderer: IpcRenderer,
      mainWindow: BrowserWindow
    }
  }
}

export type Tabela = (data: any[]) => {
  coluna: any
  larguras: number[]
};

interface PainelSeriesInfo {
  site: Site
  series: any[]
}

export type { PainelSeriesInfo };