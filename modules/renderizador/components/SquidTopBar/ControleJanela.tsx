const ControleJanela = (() => {

  return {
    fechar: () => {
      global.ipcRenderer.send('janela', 'fechar')
    },
    maximizar: () => {
      global.ipcRenderer.send('janela', 'maximizar')
    },
    minimizar: () => {
      global.ipcRenderer.send('janela', 'minimizar')
    }
  }
})()

export default ControleJanela;