import InfosPostMP4Series from "../renderer/model/InfosPostMP4Series";
import Serie from "../renderer/model/Serie";
import { extrairLinks } from '../renderer/reducers/Series/mp4seriesAPI'
import { RequestOptions } from "http"
import * as CSSselect from 'css-select'
import { htmlToDOM, Element, Text } from "html-react-parser"
import axios from "axios";
import * as uuid from 'uuid';

const http = require('http');

const LINK = 'https://pandafiles.com/users/9149/91356';

function limparInfosTexto(info: string)
{
    return info.split(': ')[1]
        ?? info.split(':')[1]
        ?? info;
}

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
        // console.log(res.headers);
        res.on("data", function(chunk) {
          response += chunk.toString();
        });
      }).on('close', () => {
        resolve(response)
      })
    }catch(e){
      reject(e);
    }
  })

  
}

const limparString = str => 
{
  return 
}

describe('Extração de informações da série', () => {
  const api = axios.create({
    baseURL: `https://www.baixarseriesmp4.net/wp-json/wp/v2`,
    params: {
      categories: 1915
    }
  });


  let posts = [];
  beforeAll(async () => {
    const pag1 = await api.get('posts');
    expect(pag1.status).toBe(200)
    expect(posts = pag1.data).toBeTruthy()
    expect(posts.length).toBeGreaterThan(0)
  })

  test('titulo da série', () => {
    var titulo = posts[0].slug;

    titulo = (items => {
      let idxs = [];

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

      var result = [];

      if(idxs.length > 0){
        result = items.filter((_item, idx) => {
          return !idxs.includes(idx);
        })
      }else{
        result = items;
      }

      return result.join(' ');
    })(titulo.split('-'));


    // console.log(titulo);
    expect(titulo).toBeTruthy();
  })

  test('carregar informações da primeira serie encontrada', async () => {
    const post = posts[0];
    // console.log(post);
    const html = htmlToDOM(post.content.rendered);
    
    const infos = {
        
    } as InfosPostMP4Series;

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

    })

  });

  test('extrair blocos de links do site mp4series', () => {
    const serie = {
      tipo: 'serie',
      uuid: uuid.v4(),
      titulo: 'teste',
      html: posts[3].content.rendered,
      slug: posts[3].slug
    } as Serie;
    console.log(posts[3].slug);
    const links = extrairLinks(serie)

    console.log(links.filter(l => l.provedor === 'MEGA'))

  })

  // console.log(pag1);

})

describe('Extração de links do panda', () => {

  test('buscar links da pasta com 3 episódios', async () => {
    const body = await getRequest(new URL(LINK));
    const html = htmlToDOM(body);

    const pasta: Element | null = (CSSselect.selectOne('#xfiles', html) as unknown as Element)
    
    expect(pasta).not.toBeNull()

    CSSselect.selectAll('tr td a', pasta).forEach(elmLink => {
      const link = elmLink.attribs?.href;
      expect(link).not.toBeUndefined();
    })

  });
});
