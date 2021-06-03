//import axios from "axios";
import { Site } from "../../types"

export const fetchSeriesFromWordpress = async () => 
 {
//   const api = axios.create({
//     baseURL: 'https://www.baixarseriesmp4.net/wp-json/wp/v2',
//     params: {
//       categories: 1915
//     }
//   })

//   let pageNo = 1;
//   let totalPages = 1;
//   const posts: any[] = []
//   do{
//     const response = await api.get('/posts', { params: { page: pageNo } });
    
//     totalPages = parseInt(response.headers['x-wp-totalpages']);
//     posts.concat(response.data)
//   }while(pageNo++ <= totalPages)

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
 }

export async function sincronizarSite(id: number)
    : Promise<{ data: number }> 
{
  console.log(id)
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