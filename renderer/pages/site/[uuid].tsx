import React, { createRef, useRef, useState, useEffect, RefObject, EffectCallback, ReactElement } from 'react'

// import { NextPageContext } from 'next'
import Layout from '../../components/Layout'

import { GetStaticPaths, GetStaticProps } from 'next'
import { Button, H5, Panel, PanelStack2, Pre, ProgressBar, Switch, UL } from '@blueprintjs/core'
import PainelSeries from './series.panel'
import { connect } from 'react-redux'
import { AppDispatch, AppState, makeStore, useAppDispatch, useAppSelector } from '../../store'
import parse, { htmlToDOM, Element, Text } from 'html-react-parser';
import * as CSSselect from 'css-select';
import ListagemSites from '../../model/ListagemSites'
import Site from '../../model/Site'
import SeriesHandler from '../../model/SeriesHandler'
import { selectSquid } from '../../reducers/squidSlice'
import { listarSeriesThunk } from '../../reducers/Serie/serieThunks'
import SerieMP4Series from '../../model/SerieMP4Series'





export type Params = {
  uuid?: string
}

export type Props = {
  siteUUID: string
  errors?: string
}

export const getStaticPaths: GetStaticPaths = async () => {
  const items: Site[] = [
    { 
      status: 'sincronizado',
      uuid: '9666e8bb-c801-431f-9d9c-94443f3ccd91',
      nome: 'baixarseriesmp4.net', 
      baixaveis: 132, 
      baixados: 30, 
      usaApi: false, 
      usaWeb: false, 
      usaWordpress: true,
      sincronizando: false
    }
  ]
  const paths = items.map((item) => `/site/${item.uuid}`)
  return { paths, fallback: false }
}

export const getStaticProps: GetStaticProps = async ({ params }) => {
  const { uuid } = params as Params;
  const { dispatch, getState } = makeStore();

  const site = getState();

  return {
    props: {
      siteUUID: uuid
    }
  }
}

// export const getStaticProps: GetStaticProps = async ({ params }) => {
//   const { uuid } = params as Params
//   const { dispatch, getState } = makeStore();

//   console.log(dispatch(listar('denga')));

//   // const site = sites.values.filter(site => site.uuid == uuid);

//   // console.log(site);

//   try {
//     const series = await fetchSeriesFromWordpress()
//     return {
//       props: {
//         series: series.map(post => {
//           const html = htmlToDOM(post.html);
//           const elmLinks: Element[] | null = CSSselect.selectAll('[href^="http://url.baixarseriesmp4.com"]', html);
    
//           post.links = elmLinks.map(elmLink => {
//             if(!elmLink.attribs.hasOwnProperty('href'))
//               return;


//             const elmEpi = elmLink.parent?.children[0] as Text;
//             const elmTipo = elmLink.children[0] as Text;
//             if(!elmTipo.hasOwnProperty('data') || !['Mega', 'PandaFiles'].includes(elmTipo.data))
//               return;

//             return { 
//               tipo: elmTipo.data,
//               episodio: elmEpi.data ?? '??',
//               linkOriginal: elmLink.attribs.href 
//             };
//           })
          
//           return post
//         })
//       },
//     }
//   } catch (err) {
//     return {
//       props: {
//         errors: err.message,
//       },
//     }
//   }
// }


const PaginaSite = ({ siteUUID, errors }: Props) => {
  if (errors) {
    return (
      <Layout title={`Error | Next.js + TypeScript + Electron Example`}>
        <p>
          <span style={{ color: 'red' }}>Error:</span> {errors}
        </p>
      </Layout>
    )
  }  

  const dispatch = useAppDispatch()
  const squid = useAppSelector(selectSquid);
  // const site = useAppSelector(selectSites);

  const [series, setSeries] = useState<SerieMP4Series[]>(squid.series);

  useEffect(() => {
    if(series.length > 0)
      return;
    
    dispatch(listarSeriesThunk()).then(data => {
      setSeries(data.payload as SerieMP4Series[]);
    })
  }, [series])

  // console.log(squid.status);
  // if(squid.status !== 'pronto'){
  //   return (
  //     <h1>Carregando...</h1>
  //   )
  // }

  console.log(series);

  const initialPanel: Panel<any> = {
    props: {
        site: siteUUID,
        series: series
    },
    renderPanel: PainelSeries,
    title: "Séries",
  };

  return (
    <Layout
      title={`${siteUUID ? siteUUID : 'Detail'} | Next.js + TypeScript Example`}
    >
      
      <style jsx global>{`
        .bp3-panel-stack-view{
          position: initial !important;
        }

        .bp3-panel-stack2{
          height: 100%;
          overflow-y: auto;
        }

        .bp3-tab-list{
          justify-content: space-evenly;
        }

        .bp3-table-row-name,
        .bp3-table-cell > * {
        display: flex;
        height: 100%;
        align-items: center;
        }
      `}</style>

      <PanelStack2 
        initialPanel={initialPanel}
        showPanelHeader={true}
       />

       
    </Layout>
      
  )
}

export default PaginaSite;