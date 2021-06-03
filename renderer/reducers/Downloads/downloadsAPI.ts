//import axios from "axios";
import { Site } from "../../types"

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