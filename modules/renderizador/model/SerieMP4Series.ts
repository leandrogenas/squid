import Baixavel, { Baixaveis, BaixavelTipo } from "./Baixavel";
import Link from "./Link";
import SeriesHandler from "./SeriesHandler";

export default class SerieMP4Series implements SeriesHandler {
    tipo = Baixaveis.SERIE 
    site = ''
    uuid = ''
    titulo = ''
    links = []
    infos
  
    async obterLinks()
    {
      return new Promise<Link[]>(
        (resolve, reject) => 
        {
          resolve([])
        }
      );
    }
  }