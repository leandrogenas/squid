import axios from "axios";


function sleep(milliseconds) {
  const date = Date.now();
  let currentDate: number | null = null;
  do {
    currentDate = Date.now();
  } while (currentDate - date < milliseconds);
}

export const fetchSeriesFromWordpress = async (pages: number): Promise<any[]> => 
 {
  const api = axios.create({
    baseURL: `https://www.baixarseriesmp4.net/wp-json/wp/v2`,
    params: {
      // categories: 1915
        categories: 2249
    }
  })

  let pageNo = 1;
  let totalPages;
  const posts: any[] = []
  do{
    try{
      
      const response = await api.get('/posts', { params: { page: pageNo, per_page: 100 } });
      totalPages = parseInt(response.headers['x-wp-totalpages']);
      console.log(`pagina ${pageNo} de ${totalPages}: ${response.data.length} series`);
      response.data.forEach(post => {
        posts.push({
          tipo: 'serie',
          uuid: 'uuidv4()',
          titulo: post?.title.rendered.replace('Baixar ', ''),
          html: post?.content.rendered,
          slug: post?.slug
        })
      })
    }catch(e){
      console.log(e);
      // Espera entre 1 a 5 segundos, as vezes o problema é a proteção a ddos
      sleep(Math.floor(Math.random() * 5) + 1000)
    }

  // }while(pageNo++ < 2)
  }while(pageNo++ < totalPages)

  return posts;
}