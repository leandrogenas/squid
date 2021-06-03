// Native
import { join } from 'path'
import * as url from 'url'
//import readline from 'readline'
import http, { IncomingMessage, RequestOptions } from 'http';
import https from 'https';
import { spawn } from 'child_process'
import { v4 as uuidv4 } from 'uuid';
import * as CSSselect from 'css-select';
import { Comment, Element, htmlToDOM } from 'html-react-parser';
import * as DownloadManager from 'electron-download-manager';

DownloadManager.register();

// Packages
import { BrowserWindow, app, ipcMain, IpcMainEvent } from 'electron'
import isDev from 'electron-is-dev'
import prepareNext from 'electron-next'
import { createWriteStream, writeFileSync } from 'fs';
//import reload from 'electron-reload'

// if(isDev)
//   reload(join(__dirname, '..', 'renderer'))
let mainWindow: Electron.BrowserWindow | null


// Prepare the renderer once the app is ready
app.on('ready', async () => {
  await prepareNext('./renderer')

  mainWindow = new BrowserWindow({
    width: 800,
    height: 600,
    titleBarStyle: 'customButtonsOnHover',
    frame: false,
    webPreferences: {
      nodeIntegration: false,
      preload: join(__dirname, 'preload.js'),
    },
  })

  const appUrl = isDev
    ? 'http://localhost:8000/'
    : url.format({
        pathname: join(__dirname, 'renderer/index.html'),
        protocol: 'file:',
        slashes: true,
      })

  mainWindow.loadURL(appUrl)
  mainWindow.webContents.openDevTools();

  mainWindow.webContents.session.on('will-download', (_event, item, _webContents) => {
    // Set the save path, making Electron not to prompt a save dialog.
    console.log('download');
    item.setSavePath('D:\\')
  
    item.on('updated', (_event, state) => {
      if (state === 'interrupted') {
        console.log('Download is interrupted but can be resumed')
      } else if (state === 'progressing') {
        if (item.isPaused()) {
          console.log('Download is paused')
        } else {
          console.log(`Received bytes: ${item.getReceivedBytes()}`)
        }
      }
    })
    item.once('done', (_event, state) => {
      if (state === 'completed') {
        console.log('Download successfully')
      } else {
        console.log(`Download failed: ${state}`)
      }
    })
  })

  mainWindow.webContents.on('did-finish-load', () => {
    console.log('pronto?');
    let megaCliServer = spawn('MEGAcmdServer.exe')


    megaCliServer.stdout.on('data', (data) => {
      mainWindow?.webContents.send('megacmd-stdout', data + "");
      console.log(`megacli-server: ${data}`);
    });
  })
  

})

// Quit the app once all windows are closed
app.on('window-all-closed', app.quit)

ipcMain.on('janela', (_event: IpcMainEvent, comando: 'maximizar' | 'minimizar' | 'fechar') => {

  if(comando == 'fechar'){
    mainWindow?.webContents.closeDevTools();
    mainWindow?.close()
  }if(comando == 'maximizar'){
    if(mainWindow?.isMaximized())
      mainWindow?.restore()
    else
      mainWindow?.maximize()
  }if(comando == 'minimizar')
    mainWindow?.minimize()

})

// listen the channel `message` and resend the received message to the renderer process
ipcMain.on('converter-link', (event: IpcMainEvent, link: string) => {

  const url = new URL(link);
  const opts: RequestOptions = {
    host: url.hostname,
    port: url.port,
    path: url.pathname,
    headers: {
      'Referer': 'https://www.baixarseriesmp4.net/'
    }
  }

  try{
    const req = http.get(opts, res => {
      if(!res.headers.location)
        throw `Não voltou uma URL de redirecionamento (Status ${res.statusCode})`;

      const urlRedir = new URL(res.headers.location);
      
      const linkB64 = urlRedir.searchParams.get('url')

      if(!linkB64)
        throw `Url ${res.headers.location} inválida`;
      
      const b64 = Buffer.from(linkB64, 'base64');
      event.returnValue = b64.toString();
      
        
    })

    req.on('error', function(e) {
      throw e.message;
    });
  }catch(e){
    event.returnValue = `Problema na conversão: ${e}`;
  }

})

ipcMain.on('download-mega', (event: IpcMainEvent, link: string) => {
  console.log(`começando o download de ${link}`)

  const uuid = uuidv4();
  console.log(`${uuid}`)
  const downloader = spawn('MEGAclient.exe', ['get', link, join(app.getPath('downloads'))]); 

  downloader.stdout.on('data', (data) => {
    mainWindow?.webContents.send(uuid, data.toString());
    process.stdout.write(`${data} \r`)
  });

  downloader.stderr.on('data', (data) => {
    mainWindow?.webContents.send(uuid, data.toString());
    console.log(data.toString())
  })

  downloader.on('exit', (code) => {
    console.log(`saiu, codigo: ${code}`);
  })

  event.returnValue = uuid

})

const getRequest = async (url: URL) => {
  const opts: RequestOptions = {
    host: url.hostname,
    port: url.port,
    path: url.pathname
  }

  return new Promise<string>((resolve, reject) => {
    let response: string = "";
    try{
      http.get(opts, res => {
        console.log(res.headers);
        res.on("data", function(chunk) {
          response += chunk.toString();
        });
      }).on('close', () => {
        writeFileSync('saida.html', response);
        resolve(response)
      })
    }catch(e){
      reject(e);
    }
  })

  
}

const readableSizeBytes = (fileSizeInBytes: number) => {
  var i = -1;
  var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
  do {
      fileSizeInBytes = fileSizeInBytes / 1024;
      i++;
  } while (fileSizeInBytes > 1024);

  return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
};

ipcMain.on('download-panda', (event: IpcMainEvent, link: string) => {
  const url = new URL(link);
  
  const uuid = uuidv4();
  console.log(`[${uuid}] buscando link direto de ${link}...`)
  getRequest(url).then((data: string) => {
    console.log(`[${uuid}] dados recebidos, tamanho: ${data.length} bytes`)
  
    const html = htmlToDOM(data);

    const pasta: Element | null = CSSselect.selectOne('#xfiles', html);

    if(pasta){
      console.log(pasta);
    }


    const elmLink: Element | null = CSSselect.selectOne('[name="method_premium"]', html);
    let link;
    // const elmLink: Element | null = CSSselect.selectOne('.downb + a', html);

    if(!elmLink?.attribs || elmLink?.name != 'a'){
      // meh
      const elmComment = elmLink
        ?.nextSibling?.next
        ?.next?.next as Comment;

      if(!elmComment){
        console.log(elmLink);
        console.error('meeeh');
        return;
      }

      const elmLinkComment = htmlToDOM(elmComment.data).filter((sibling: any) => {
          if(!sibling.hasOwnProperty('name') || sibling.name != 'a')
              return;
          
          return sibling
        })[0] as Element;

      if(!elmLinkComment){
        console.error('mehzissimo');
        return;
      }
      
      link = elmLinkComment.attribs.href;
    }else{
      if(elmLink.next && elmLink.next.hasOwnProperty('next')){
        console.log('não tem link')
        return;
      }

      const elmA = elmLink.next?.next as Element;

      if(elmA.name != 'a'){
        console.log('não tem link 2')
          return;
      }

      link = elmA.attribs.href;
    }

    const elmTitulo: Element | null = CSSselect.selectOne('[name="fname"]', html);
    if(!elmTitulo?.attribs || elmTitulo?.name != 'input'){
    console.error('Sem link no elemento')
      return
    }

    const linkDireto: URL = new URL(link);
    const titulo: string = elmTitulo.attribs.value;

    console.log(`[${uuid}] titulo do arquivo: ${titulo}`);
    console.log(`[${uuid}] link encontrado: ${linkDireto.toString()}`);
    console.log(`[${uuid}] iniciando download...`);
    const arquivo = createWriteStream(join(app.getPath('downloads'), titulo));
    https.get(linkDireto, (response: IncomingMessage) => {
      console.log(`[${uuid}] recebendo dados... ${response.statusCode}`)
      console.log(response.headers);

      response.pipe(arquivo);

      let len = 0;
      if(!response.headers['content-length']){
        console.log(`[${uuid}] não sei o tamanho do arquivo`);
        return;
      }

      len = parseInt(response.headers['content-length'], 10);
      let cur = 0;
      response.on('data', (chunk: string) => {
        cur += chunk.length
        event.sender.send(uuid, `${readableSizeBytes(cur)} / ${readableSizeBytes(len)}`)
        process.stdout.write(`[${uuid}] ${readableSizeBytes(cur)} / ${readableSizeBytes(len)} \r `);
      })

      response.on('end', () => {
        console.log('caboo');
      })
    });
  });

  event.returnValue = uuid;

})