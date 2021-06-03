const fs = require('fs')
const join = require('path').join;

const tsd = `
declare function _exports(glob: string | string[], options?: {
    persistent?: boolean | undefined;
    ignored?: any;
    ignoreInitial?: boolean | undefined;
    followSymlinks?: boolean | undefined;
    cwd?: string | undefined;
    disableGlobbing?: boolean | undefined;
    usePolling?: boolean | undefined;
    useFsEvents?: boolean | undefined;
    alwaysStat?: boolean | undefined;
    depth?: number | undefined;
    interval?: number | undefined;
    binaryInterval?: number | undefined;
    ignorePermissionErrors?: boolean | undefined;
    atomic?: number | boolean | undefined;
    awaitWriteFinish?: any;
} | undefined): void;
export = _exports;
`

fs.writeFileSync(join(__dirname, 'node_modules', 'electron-reload', 'index.d.ts'), tsd)
console.log('arquivo de tipagem do electron-reload criado');