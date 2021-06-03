import axios from "axios";
import { SerieWordpress, Site } from "../../types"
import parse from 'html-react-parser';
import { v4 as uuidv4 } from 'uuid';

function sleep(milliseconds) {
  const date = Date.now();
  let currentDate: number | null = null;
  do {
    currentDate = Date.now();
  } while (currentDate - date < milliseconds);
}

export async function salvarSeriesSite(series: any[]): Promise<boolean>
{
  return new Promise<boolean>((reject, resolve) => {
    let open = indexedDB.open('squid', 1)

    open.onupgradeneeded = () => {
      console.info('Foi preciso recriar o BD')
      let db = open.result
      db.createObjectStore('series', { autoIncrement: true, keyPath: 'uuid' })
      db.createObjectStore('downloads', { autoIncrement: true, keyPath: 'uuid' })
    }

    open.onsuccess = () => {
      var transaction = open.result.transaction("series", 'readwrite');
      var objectStore = transaction.objectStore("series");


      series.forEach(serie => {
        objectStore.put(serie);
      })
      

      transaction.onerror = function() {
          reject()
      };
      transaction.oncomplete = function(event) {
          console.log('terminou o sync' + event);
          resolve(true);
      };
    }
  });
}

export const fetchSeriesFromWordpress = async (pages: number): Promise<any[]> => 
 {
  const api = axios.create({
    baseURL: `https://www.baixarseriesmp4.net/wp-json/wp/v2`,
    params: {
      categories: 1915
    }
  })

  let pageNo = 1;
  let totalPages;
  const posts: any[] = []
  do{
    try{
      
      const response = await api.get('/posts', { params: { page: pageNo } });
      totalPages = parseInt(response.headers['x-wp-totalpages']);
      console.log(`pagina ${pageNo} de ${totalPages}: ${response.data.length} series`);
      response.data.forEach(post => {
        posts.push({
          tipo: 'serie',
          uuid: uuidv4(),
          titulo: post?.title.rendered.replace('Baixar ', ''),
          html: post?.content.rendered
        })
      })
    }catch(e){
      console.log(e);
      // Espera entre 1 a 5 segundos, as vezes o problema é a proteção a ddos
      sleep(Math.floor(Math.random() * 5) + 1000)
    }

  }while(pageNo++ < totalPages)

  return posts;
}

//   return posts;

// }

//   const teste = async () => {
//     return new Promise(
//         (resolve, reject) => {
//             let open = indexedDB.open('sync', 1)

//             open.onsuccess = () => {
//                 var transaction = open.result.transaction("people");
//                 var objectStore = transaction.objectStore("people");
//                 var request = objectStore.count();
    
//                 request.onerror = function(event) {
//                     reject(event)
//                 };
//                 request.onsuccess = function(event) {
//                     resolve(request.result);
//                 };
//             }
            
//         }
//     );
// }

//   let open = indexedDB.open('sync', 1)

//   open.onupgradeneeded = () => {
//       console.info('Foi preciso recriar o BD')
//       let db = open.result
//       db.createObjectStore('people', { autoIncrement: true, keyPath: 'uuid' })
//   }

//   open.onsuccess = () => {
//       console.info('Conexão com o IndexedDB estabelecida')
//       let db = open.result
//       let tx = db.transaction('people', 'readwrite')
//       let store = tx.objectStore('people')

//       console.info(`Sincronizando ${data.data.length} registros...`)
//       data.data.forEach(person => {
//           store.put(person)
//       })
//       console.info(`Concluído!`)
      

//       tx.oncomplete = () => {
//           db.close()
//           console.info(`Finalizando conexão com o IndexedDB...`)
//           console.groupEnd();
//       }
//   }

//   open.onerror = e => {
//       console.error(e);
//       console.debug('Erro no IndexedDB');
//       console.groupEnd();
//   }


//   return posts;

export async function sincronizarSite(id: number)
    : Promise<{ data: number }> 
{
  return new Promise<{ data: number }>((resolve, reject) => {

    
    try{
      fetchSeriesFromWordpress(1)
        .then(data => {
          console.log('2', data);
        })
        .catch(e => {
          throw e;
        })
    }catch(e){
      reject({ data: e });
    }


  })
  



    const response = await fetch('/api/counter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
      // body: JSON.stringify({ amount }),
    })
    const result = await response.json()
  
    return result
}

export function listarSites(count: number = 1)
    : Site[]
{
  console.log(count);
    return []
}