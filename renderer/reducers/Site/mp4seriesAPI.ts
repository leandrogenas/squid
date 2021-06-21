import InfosPostMP4Series from "../../model/InfosPostMP4Series";
import Serie from "../../model/Serie";
import { Text, Element, htmlToDOM } from "html-react-parser";
import * as CSSselect from 'css-select';
import Link, { FormatoLink, ProvedorLink, QualidadeLink, TipoIdiomaLink } from "../../model/Link";

type SecaoPostMP4Series = 'infos' | 'sinopse' | 'links' | 'n/a'

export function limparTituloSlug(slug: string)
{
    const items = slug.split('-');
    let idxs: any[] = [];
    items.forEach((str, i) => {
    if(str.includes('temporada')){
        idxs.push(i);
        idxs.push(i-1);
    }
    if(str.includes('baixar'))
        idxs.push(i);
    if(str.includes('leg'))
        idxs.push(i);
    if(str.includes('dub'))
        idxs.push(i);
    if(str.includes('mp4'))
        idxs.push(i);
    });

    var result: any[] = [];

    if(idxs.length > 0){
        result = items.filter((_item, idx) => {
            return !idxs.includes(idx);
        })
    }else{
        result = items;
    }

    return result.join(' ');
}

function limparInfosTexto(info: string)
{
    return info.split(': ')[1]
        ?? info.split(':')[1]
        ?? info;
}

export function extrairInfosPost(serie: Serie)
    : InfosPostMP4Series
{
    const html = htmlToDOM(serie.html);

    const infos = { } as InfosPostMP4Series;

    html.forEach(item => {
        if(!CSSselect.is(item, 'p'))
            return;
    
        const elm = item as Element;
        elm.children.forEach(e => {
            const elm = e as Element;

            if(elm.name === 'img')
                infos.imgURL = elm.attribs.src
            
            const textElm = e as Text;
            if(!textElm.data)
                return;

            const texto = textElm.data.toLocaleLowerCase();
            if(texto.includes('original'))
                infos.tituloOriginal = limparInfosTexto(textElm.data);
            if(texto.includes('brasil'))
                infos.tituloBrasil = limparInfosTexto(textElm.data);
            if(texto.includes('lançamento'))
                infos.lancamento = limparInfosTexto(textElm.data);
            if(texto.includes('duração'))
                infos.duracao = limparInfosTexto(textElm.data);
            if(texto.includes('formato'))
                infos.formato = limparInfosTexto(textElm.data);
            if(texto.includes('genero'))
                infos.genero = limparInfosTexto(textElm.data);
            if(texto.includes('criador'))
                infos.criador = limparInfosTexto(textElm.data);
            if(texto.includes('tamanho'))
                infos.tamanho = limparInfosTexto(textElm.data);
            if(texto.includes('idioma'))
                infos.idioma = limparInfosTexto(textElm.data);
            if(texto.includes('legenda'))
                infos.legenda = limparInfosTexto(textElm.data);
            if(texto.includes('sinopse'))
            infos.sinopse = limparInfosTexto(textElm.data);            
            
        })
    });

    return infos;
}

type PropriedadesBlocoDownload = {
    formato: FormatoLink
    qualidade: QualidadeLink
    tipoIdioma: TipoIdiomaLink
}

function quebrarTituloDownload(titulo: string)
    : PropriedadesBlocoDownload
{
    titulo = titulo.toUpperCase();
    return {
        formato: titulo.includes('MP4') ? 'MP4' : 'OUTRO',
        qualidade: titulo.includes('480P') 
            ? '480P'
            : titulo.includes('720P')
                ? '720P'
                : titulo.includes('1080P')
                    ? '1080P'
                    : 'OUTRO',
        tipoIdioma: titulo.includes('DUBLADO')
            ? 'DUBLADO'
            : titulo.includes('LEGENDADO')
                ? 'LEGENDADO'
                : 'OUTRO',
    };
}

function obterProvedorLink(tipo: string): ProvedorLink
{
    tipo = tipo.toLocaleUpperCase();
    return tipo.includes('MEGA')
        ? 'MEGA'
        : tipo.includes('PANDA')
            ? 'PANDA'
            : 'OUTRO'
}

export function extrairLinks(serie: Serie)
    : Link[]
{
    const links: Link[] = [];

    const html = htmlToDOM(serie.html);
    
    const select = CSSselect.selectAll('p>span>strong', html) as Element[];
    select.forEach(bloco => {
        const texto = bloco.children[0] as Text;
        if(!texto || !texto.data)
            return;

        

        const elmsLink = bloco.parent?.parent?.children;
        if(!elmsLink)
            return;
            
        const selectLinks = CSSselect.selectAll('a', elmsLink);
        selectLinks.forEach(l => {
            const elm = l as Element;
            const elmTitulo = elm.prev?.prev?.prev as Text;
            const elmProvedor = elm.children[0] as Text

            const link = {
                ...quebrarTituloDownload(texto.data)
            } as Link;

            let provedor: ProvedorLink = 'OUTRO'
            provedor = obterProvedorLink(elmProvedor.data ?? '')
            link.url = elm.attribs.href; 
            link.provedor = provedor;
            link.titulo = elmTitulo?.data 
                ?? (elm.prev as Text).data
                ?? '?';
            link.titulo = link.titulo
                .replace(': ', '')
                .replace(':', '')
                .trim();

            links.push(link);
        })

    })

    return links;

}

export function extrairTodosLinks(serie: Serie)
{
  const html = htmlToDOM(serie.html);
                
  const elmLinks: Element[] | null = CSSselect
    .selectAll(`[href^="http://url.baixarseriesmp4.com"]`, html);

  if(!elmLinks)
    return [];

  const linkList = elmLinks.map(elmLink => {
    if(!elmLink.attribs.hasOwnProperty('href'))
      return;


    const elmEpi = elmLink.parent?.children[0] as Text;
    const elmTipo = elmLink.children[0] as Text;
    if(!elmTipo.hasOwnProperty('data') || !['Mega', 'PandaFiles'].includes(elmTipo.data))
      return;

    return { 
      tipo: elmTipo.data,
      episodio: elmEpi.data ?? '??',
      linkOriginal: elmLink.attribs.href 
    };
  })

  return linkList;
}

export function converterLink(link: Link): Link
{
    return {
        ...link,
        url: global.ipcRenderer.sendSync('converter-link', link.url),
    }
}

export function converterLinks(links: Link[])
    : Link[]
{
    const result: Link[] = Object.assign(links, {});
    return result.map(converterLink);
}