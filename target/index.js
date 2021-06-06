"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
    __setModuleDefault(result, mod);
    return result;
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
// Native
const path_1 = require("path");
const url = __importStar(require("url"));
//import readline from 'readline'
const http_1 = __importDefault(require("http"));
const https_1 = __importDefault(require("https"));
const child_process_1 = require("child_process");
const uuid_1 = require("uuid");
const CSSselect = __importStar(require("css-select"));
const html_react_parser_1 = require("html-react-parser");
const DownloadManager = __importStar(require("electron-download-manager"));
DownloadManager.register();
// Packages
const electron_1 = require("electron");
const electron_is_dev_1 = __importDefault(require("electron-is-dev"));
const electron_next_1 = __importDefault(require("electron-next"));
const fs_1 = require("fs");
//import reload from 'electron-reload'
// if(isDev)
//   reload(join(__dirname, '..', 'renderer'))
let mainWindow;
// Prepare the renderer once the app is ready
electron_1.app.on('ready', async () => {
    await electron_next_1.default('./renderer');
    mainWindow = new electron_1.BrowserWindow({
        width: 800,
        height: 600,
        titleBarStyle: 'customButtonsOnHover',
        frame: false,
        webPreferences: {
            nodeIntegration: false,
            preload: path_1.join(__dirname, 'preload.js'),
        },
    });
    mainWindow.megaServer = child_process_1.spawn('MEGAcmdServer.exe');
    mainWindow.megaServer.on('data', data => {
        console.log(`megacli-server: ${data}`);
    });
    mainWindow.megaShell = child_process_1.spawn('MEGAcmdShell.exe');
    const appUrl = electron_is_dev_1.default
        ? 'http://localhost:8000/'
        : url.format({
            pathname: path_1.join(__dirname, 'renderer/index.html'),
            protocol: 'file:',
            slashes: true,
        });
    mainWindow.loadURL(appUrl);
    mainWindow.webContents.openDevTools();
    mainWindow.webContents.session.on('will-download', (_event, item, _webContents) => {
        // Set the save path, making Electron not to prompt a save dialog.
        console.log('download');
        item.setSavePath('D:\\');
        item.on('updated', (_event, state) => {
            if (state === 'interrupted') {
                console.log('Download is interrupted but can be resumed');
            }
            else if (state === 'progressing') {
                if (item.isPaused()) {
                    console.log('Download is paused');
                }
                else {
                    console.log(`Received bytes: ${item.getReceivedBytes()}`);
                }
            }
        });
        item.once('done', (_event, state) => {
            if (state === 'completed') {
                console.log('Download successfully');
            }
            else {
                console.log(`Download failed: ${state}`);
            }
        });
    });
    mainWindow.webContents.on('did-finish-load', () => {
        mainWindow?.megaServer.on('data', (data) => {
            mainWindow?.webContents.send('mega-stdout', data + "");
        });
        mainWindow?.megaShell.on('data', (data) => {
            mainWindow?.webContents.send('mega-stdout', data + "");
        });
    });
});
// Quit the app once all windows are closed
electron_1.app.on('window-all-closed', electron_1.app.quit);
electron_1.ipcMain.on('mega', (event, comando) => {
    console.log(comando);
    event.sender.send('mega', {
        shell: mainWindow?.megaShell.pid.toString(),
        server: mainWindow?.megaServer.pid.toString()
    });
});
electron_1.ipcMain.on('janela', (_event, comando) => {
    if (comando == 'fechar') {
        mainWindow?.webContents.closeDevTools();
        mainWindow?.close();
    }
    if (comando == 'maximizar') {
        if (mainWindow?.isMaximized())
            mainWindow?.restore();
        else
            mainWindow?.maximize();
    }
    if (comando == 'minimizar')
        mainWindow?.minimize();
});
// listen the channel `message` and resend the received message to the renderer process
electron_1.ipcMain.on('converter-link', (event, link) => {
    const url = new URL(link);
    const opts = {
        host: url.hostname,
        port: url.port,
        path: url.pathname,
        headers: {
            'Referer': 'https://www.baixarseriesmp4.net/'
        }
    };
    try {
        const req = http_1.default.get(opts, res => {
            if (!res.headers.location)
                throw `Não voltou uma URL de redirecionamento (Status ${res.statusCode})`;
            const urlRedir = new URL(res.headers.location);
            const linkB64 = urlRedir.searchParams.get('url');
            if (!linkB64)
                throw `Url ${res.headers.location} inválida`;
            const b64 = Buffer.from(linkB64, 'base64');
            event.returnValue = b64.toString();
        });
        req.on('error', function (e) {
            throw e.message;
        });
    }
    catch (e) {
        event.returnValue = `Problema na conversão: ${e}`;
    }
});
electron_1.ipcMain.on('download-mega', (event, link) => {
    console.log(`começando o download de ${link}`);
    const uuid = uuid_1.v4();
    console.log(`${uuid}`);
    const downloader = child_process_1.spawn('MEGAclient.exe', ['get', link, path_1.join(electron_1.app.getPath('downloads'))]);
    downloader.stdout.on('data', (data) => {
        mainWindow?.webContents.send(uuid, data.toString());
        process.stdout.write(`${data} \r`);
    });
    downloader.stderr.on('data', (data) => {
        mainWindow?.webContents.send(uuid, data.toString());
        console.log(data.toString());
    });
    downloader.on('exit', (code) => {
        console.log(`saiu, codigo: ${code}`);
    });
    event.returnValue = uuid;
});
const getRequest = async (url) => {
    const opts = {
        host: url.hostname,
        port: url.port,
        path: url.pathname
    };
    return new Promise((resolve, reject) => {
        let response = "";
        try {
            http_1.default.get(opts, res => {
                console.log(res.headers);
                res.on("data", function (chunk) {
                    response += chunk.toString();
                });
            }).on('close', () => {
                fs_1.writeFileSync('saida.html', response);
                resolve(response);
            });
        }
        catch (e) {
            reject(e);
        }
    });
};
const readableSizeBytes = (fileSizeInBytes) => {
    var i = -1;
    var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
    do {
        fileSizeInBytes = fileSizeInBytes / 1024;
        i++;
    } while (fileSizeInBytes > 1024);
    return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
};
electron_1.ipcMain.on('download-panda', (event, link) => {
    const url = new URL(link);
    const uuid = uuid_1.v4();
    console.log(`[${uuid}] buscando link direto de ${link}...`);
    getRequest(url).then((data) => {
        console.log(`[${uuid}] dados recebidos, tamanho: ${data.length} bytes`);
        const html = html_react_parser_1.htmlToDOM(data);
        const pasta = CSSselect.selectOne('#xfiles', html);
        if (pasta) {
            console.log(pasta);
        }
        const elmLink = CSSselect.selectOne('[name="method_premium"]', html);
        let link;
        // const elmLink: Element | null = CSSselect.selectOne('.downb + a', html);
        if (!elmLink?.attribs || elmLink?.name != 'a') {
            // meh
            const elmComment = elmLink
                ?.nextSibling?.next
                ?.next?.next;
            if (!elmComment) {
                console.log(elmLink);
                console.error('meeeh');
                return;
            }
            const elmLinkComment = html_react_parser_1.htmlToDOM(elmComment.data).filter((sibling) => {
                if (!sibling.hasOwnProperty('name') || sibling.name != 'a')
                    return;
                return sibling;
            })[0];
            if (!elmLinkComment) {
                console.error('mehzissimo');
                return;
            }
            link = elmLinkComment.attribs.href;
        }
        else {
            if (elmLink.next && elmLink.next.hasOwnProperty('next')) {
                console.log('não tem link');
                return;
            }
            const elmA = elmLink.next?.next;
            if (elmA.name != 'a') {
                console.log('não tem link 2');
                return;
            }
            link = elmA.attribs.href;
        }
        const elmTitulo = CSSselect.selectOne('[name="fname"]', html);
        if (!elmTitulo?.attribs || elmTitulo?.name != 'input') {
            console.error('Sem link no elemento');
            return;
        }
        const linkDireto = new URL(link);
        const titulo = elmTitulo.attribs.value;
        console.log(`[${uuid}] titulo do arquivo: ${titulo}`);
        console.log(`[${uuid}] link encontrado: ${linkDireto.toString()}`);
        console.log(`[${uuid}] iniciando download...`);
        const arquivo = fs_1.createWriteStream(path_1.join(electron_1.app.getPath('downloads'), titulo));
        https_1.default.get(linkDireto, (response) => {
            console.log(`[${uuid}] recebendo dados... ${response.statusCode}`);
            console.log(response.headers);
            response.pipe(arquivo);
            let len = 0;
            if (!response.headers['content-length']) {
                console.log(`[${uuid}] não sei o tamanho do arquivo`);
                return;
            }
            len = parseInt(response.headers['content-length'], 10);
            let cur = 0;
            response.on('data', (chunk) => {
                cur += chunk.length;
                event.sender.send(uuid, `${readableSizeBytes(cur)} / ${readableSizeBytes(len)}`);
                process.stdout.write(`[${uuid}] ${readableSizeBytes(cur)} / ${readableSizeBytes(len)} \r `);
            });
            response.on('end', () => {
                console.log('caboo');
            });
        });
    });
    event.returnValue = uuid;
});
