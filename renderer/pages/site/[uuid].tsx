import React, { createRef, useRef, useState, useEffect, RefObject, EffectCallback, ReactElement } from 'react'

// import { NextPageContext } from 'next'
import Layout from '../../components/Layout'
import { ListagemSeries, ListagemSites, SerieWordpress, Site, User } from '../../types'

import { GetStaticPaths, GetStaticProps } from 'next'
import { Button, H5, Panel, PanelStack2, Pre, ProgressBar, Switch, UL } from '@blueprintjs/core'
import PainelSeries, { PainelSeriesInfo } from './series.panel'
import { connect } from 'react-redux'
import { AppDispatch, AppState, makeStore, useAppDispatch, useAppSelector } from '../../store'
import { listarSeriesThunk, selectSeries } from '../../reducers/Series'
import { listar, selectSites, sincronizarSiteThunk } from '../../reducers/Sites'
import { fetchSeriesFromWordpress } from '../../reducers/Sites/sitesAPI'
import parse, { htmlToDOM, Element, Text } from 'html-react-parser';
import * as CSSselect from 'css-select';

enum BaixavelTipo {
  SERIE, FILME, ANIME
}

enum LinkTipo {
  MEGA, PANDA
}

type BaixavelLink = {
  tipo: LinkTipo
  url: string
}

type Baixavel = {
	tipo: BaixavelTipo
	nome: string
	links: BaixavelLink[]
}

interface SerieHandler extends Baixavel {
  obterLinks(): Promise<BaixavelLink[]>
}

class SerieMP4Series implements SerieHandler {
  tipo = BaixavelTipo.SERIE
  nome = ''
  links = []

  async obterLinks()
  {
    return new Promise<BaixavelLink[]>(
      (resolve, reject) => 
      {
        resolve([])
      }
    );
  }
}

export type Params = {
  uuid?: string
}

export type Props = {
  siteUUID: string
  errors?: string
  sites?: ListagemSites
  listarSeries: () => any,
  sincronizarSite: (uuid: string) => any
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

  // const series = await dispatch(listarSeriesThunk(1));

  // console.log(series);

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


const InitialPropsDetail = ({ siteUUID, errors, sites, listarSeries, sincronizarSite }: Props) => {
  if (errors) {
    return (
      <Layout title={`Error | Next.js + TypeScript + Electron Example`}>
        <p>
          <span style={{ color: 'red' }}>Error:</span> {errors}
        </p>
      </Layout>
    )
  }  

  // const [sincronizando, estaSincronizando] = useState(site.sincronizando);
  const [megaCmdStdout, setMegaCmdStdout] = useState();

  const dispatch = useAppDispatch()
  const series = useAppSelector(selectSeries);


  useEffect(() => {
    if(series.status != 'aguardando')
      return;
    
    listarSeries()
  }, [series])


  if(series.status != 'carregado'){
    return (
      <h1>Carregando...</h1>
    )
  }


  const initialPanel: Panel<any> = {
    props: {
        site: siteUUID,
        series: series.values
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

function mapStateToProps(state: AppState) {
  return {
    sites: state.sites
  }
}

// export default InitialPropsDetail;

export default connect(
  mapStateToProps,
  {
    listarSeries: () => {
      return function (dispatch: AppDispatch) {
        return dispatch(listarSeriesThunk(1));
      }
    },
    sincronizarSite: (uuid: string) => {
      return function (dispatch: AppDispatch) {
        return dispatch(sincronizarSiteThunk(uuid));
      }
    }
  }
)(InitialPropsDetail)