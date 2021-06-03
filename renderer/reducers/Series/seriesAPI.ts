//import axios from "axios";
import { SerieWordpress, Site } from "../../types"

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

export async function listarSeries(count: number = 1)
    : Promise<SerieWordpress[]>
{
    return new Promise(
      (resolve, reject) => {
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
              var request = objectStore.getAll();
  
              request.onerror = function(event) {
                  reject(event)
              };
              request.onsuccess = function(event) {
                  resolve(request.result);
              };
          }
          
      }
  );
}