declare module 'electron-download-manager' {

    export const register: (opts?: {}) => void
    export const download: (options: any, callback: any) => void
    export const bulkDownload: (options: any, callback: any) => void 

}