import React, { useEffect, useState } from "react"

import CardSite from "../components/Site";


const Series: React.FC = () => {

  const [loadingWebsite, setLoadingWebsite] = useState(false);

  useEffect(() => {
    if(loadingWebsite)
      return;

    setLoadingWebsite(true);
    // fetchSeriesFromWordpress().then(data => {
    //   console.log(data);
    // })
  });

  return (
    <>
      <style jsx global>{`
        

      `}</style>

      <CardSite {...{ 
        status: 'sincronizado',
        uuid: '9666e8bb-c801-431f-9d9c-94443f3ccd91',
        nome: 'baixarseriesmp4.net', 
        baixaveis: 132, 
        baixados: 30, 
        usaApi: false, 
        usaWeb: false, 
        usaWordpress: true,
        sincronizando: false
      }}
     />
      
    </>
  )
}

export default Series