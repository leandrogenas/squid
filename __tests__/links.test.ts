import { RequestOptions } from "http"
import * as CSSselect from 'css-select'
import { htmlToDOM, Element } from "html-react-parser"
import axios from "axios";

const http = require('http');

const LINK = 'https://pandafiles.com/users/9149/91356';

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


    console.log(titulo);
    expect(titulo).toBeTruthy();
  })

  test('carregar informações da primeira serie encontrada', async () => {
    const post = posts[0];
    // console.log(post);
    const html = htmlToDOM(post.content.rendered);
    
    html.forEach(item => {
      if(!CSSselect.is(item, 'p'))
        return;
      const elm = item as Element;
      // console.log(elm.children);
    })

  });


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

    // links.children.forEach(elmTR => {
    //   const elm = elmTR as Element;
    //   console.log(elm.children);
    // })
    

  // Assert if setTimeout was called properly
  // it('testando a criação do objeto', () => {
  //   expect(ormConn).toBeCalledWith(expect.any(Object))
  // });

  // Assert greeter result
  // it('greets a user with `Hello, {name}` message', () => {
  //   expect(hello).toBe(`Hello, ${name}`);
  // });
  });
});
