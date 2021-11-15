/* eslint-disable @typescript-eslint/no-namespace */
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ipcRenderer, IpcRenderer } from 'electron'
import {ChildProcess} from "child_process";

declare global {

  namespace Electron {
    interface BrowserWindow {
      megaServer: ChildProcess
      megaShell: ChildProcess
    }
  }

  namespace NodeJS {
    interface Global {
      ipcRenderer: IpcRenderer,

    }
  }
}

// Since we disabled nodeIntegration we can reintroduce
// needed node functionality here
process.once('loaded', () => {
  global.ipcRenderer = ipcRenderer
})
