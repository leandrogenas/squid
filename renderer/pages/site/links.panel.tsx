import { Button, Classes, Intent, NumericInput, PanelProps, Position, Pre, Tab, Tabs, Toaster } from "@blueprintjs/core";
import { Cell, Column, ICellProps, Table } from "@blueprintjs/table";
import { AxiosInstance } from "axios";
import React, { createRef, ReactElement, useRef, useState } from "react";
import { Download, LinkMega, SerieWordpress, Site } from "../../types";
import { Props } from "./[uuid]";
import * as CSSselect from 'css-select'

import axios from 'axios';
import store, { useAppDispatch } from "../../store";
import { novoDownloadThunk } from "../../reducers/Downloads";
import { connect } from "react-redux";
import { useEffect } from "react";
import { htmlToDOM, Element } from "html-react-parser";
//import { adicionar as novoDownload } from "../../reducers/Downloads";

export interface PainelLinksInfo {
    site: any
    serie: any
}

type LinkCellRenderer = (rowIndex: number, columnIndex: number) => ReactElement<ICellProps>

type Tabela = (data: LinkMega[]) => {
  coluna: {
    titulo: LinkCellRenderer
    link: LinkCellRenderer
    acoes: LinkCellRenderer
  },
  larguras: number[]
}

type LinkPainel = {
    link: LinkMega,
    aguardando: boolean
}

function adicionarDownloadTeste(download: Download) {
    
}

// Meet thunks.
// A thunk in this context is a function that can be dispatched to perform async
// activity and can dispatch actions and read state.
// This is an action creator that returns a thunk:
// function makeASandwichWithSecretSauce(forPerson) {
//     // We can invert control here by returning a function - the "thunk".
//     // When this function is passed to `dispatch`, the thunk middleware will intercept it,
//     // and call it with `dispatch` and `getState` as arguments.
//     // This gives the thunk function the ability to run some logic, and still interact with the store.
//     return function(dispatch) {
//       return fetchSecretSauce().then(
//         (sauce) => dispatch(makeASandwich(forPerson, sauce)),
//         (error) => dispatch(apologize('The Sandwich Shop', forPerson, error)),
//       );
//     };
//   }

function novoDownload(download: Download) {
    return function(dispatch, getState) {
    //   if (getState().downloads.count > 0) {
    //     // You don’t have to return Promises, but it’s a handy convention
    //     // so the caller can always call .then() on async dispatch result.
  
    //     return Promise.resolve();
    //   }
  
      // We can dispatch both plain object actions and other thunks,
      // which lets us compose the asynchronous actions in a single flow.
  
      return dispatch(adicionarDownloadTeste(download))
        .then((data) => {
            console.log(data);
        }
        //   Promise.all([
        //     dispatch(makeASandwichWithSecretSauce('Me')),
        //     dispatch(makeASandwichWithSecretSauce('My wife')),
        //   ]),
        
        // .then(() => dispatch(makeASandwichWithSecretSauce('Our kids')))
        // .then(() =>
        //   dispatch(
        //     getState().myMoney > 42
        //       ? withdrawMoney(42)
        //       : apologize('Me', 'The Sandwich Shop'),
        //   ),
        );
    };
  }
  
  // This is very useful for server side rendering, because I can wait
  // until data is available, then synchronously render the app.
  
//   store
//     .dispatch(makeSandwichesForEverybody())
//     .then(() =>
//       response.send(ReactDOMServer.renderToString(<MyApp store={store} />)),
//     );

const Toast = (typeof window !== 'undefined') ?
        Toaster.create({
            className: 'my-toaster',
            position: 'bottom',
            maxToasts: 3,
            canEscapeKeyClear: true
        })
        : null;

const PainelLinks: React.FC<PanelProps<PainelLinksInfo>> = props => {
    

    const [links, setLinks] = useState<any[]>([]);
    const [isWaiting, setIsWaiting] = useState(false);
    const dispatch = useAppDispatch()

    const [megaCmdStdout, setMegaCmdStdout] = useState();

    

    const downloadLink = (tipo: 'Mega' | 'PandaFiles', rowIdx: number) => {
        setIsWaiting(true);

        const linksNovo = Object.assign(links, {});
        const linkFilter = linksNovo.filter(link => link.tipo == tipo)[rowIdx];

        linkFilter.linkConvertido = global.ipcRenderer.sendSync('converter-link', linkFilter.linkOriginal);
        //console.log(linksNovo)
    
        const tipoDownload = () => {
            return linkFilter.linkConvertido.includes('panda')
                ? 'download-panda'
                : 'download-mega';
        }

        const downloadUUID: string = global.ipcRenderer.sendSync(tipoDownload(), linkFilter.linkConvertido)
    

        dispatch(novoDownloadThunk({
            uuid: downloadUUID,
            nome: linkFilter.episodio,
            status: 'baixando',
            progresso: 0.0,
            site: props.site,
            tipo: linkFilter.tipo,
            serie: props.serie
        })).then(() => {
            setLinks(linksNovo);
            setIsWaiting(false);
        })
        // global.ipcRenderer.on(downloadUUID, (event, args) => {
        //     console.log(args);
        //     setIsWaiting(false);
        //     setMegaCmdStdout(args);
        // })

        
    };


    useEffect(
        () => {
            if(links.length > 0)
                return;

                const html = htmlToDOM(props.serie.html);
                
                const elmLinks: Element[] | null = CSSselect
                    .selectAll('[href^="http://url.baixarseriesmp4.com"]', html);
        
                const linkList = elmLinks.map(elmLink => {
                    if(!elmLink.attribs.hasOwnProperty('href'))
                        return;


                    const elmEpi = elmLink.parent?.children[0] as unknown as Text;
                    const elmTipo = elmLink.children[0] as unknown as Text;
                    if(!elmTipo.hasOwnProperty('data') || !['Mega', 'PandaFiles'].includes(elmTipo.data))
                        return;

                    return { 
                        tipo: elmTipo.data,
                        episodio: elmEpi.data ?? '??',
                        linkOriginal: elmLink.attribs.href 
                    };
                })
                
                setLinks(linkList);
            
        }, 
        [links]
    )

    const gerarLink = (tipo: 'Mega' | 'PandaFiles', rowIdx: number) => {
        const linksNovo = Object.assign(links, {});
        const linkFilter = linksNovo.filter(link => link.tipo == tipo)[rowIdx];
        
        const linkConvertido = global.ipcRenderer.sendSync('converter-link', linkFilter.linkOriginal);

        navigator.clipboard.writeText(linkConvertido);
        Toast?.show({ 
            intent: 'success',
            timeout: 1000,
            message: 'link copiado'
        });

        setLinks(linksNovo.map(link => {
            if(link.episodio == linkFilter.episodio && link.tipo == linkFilter.tipo){
                link.linkConvertido = linkConvertido;
                return link;
            }

            return link;
        }));

        
    }

    const [gerandoLink, estaGerandoLink] = useState(false);
    const [abaSelecionada, setAbaSelecionada] = useState('mega')


    const abaTipo = (tp: 'Mega' | 'PandaFiles') => {
        const linksFilter = links.filter(link => link.tipo == tp);
        return (
            <>
                <Table 
                    key={tp}
                    numRows={(linksFilter) ? linksFilter.length : 0}
                    defaultRowHeight={40}
                    columnWidths={[200, 350, 200]}
                    >
                    <Column key={0} name="Episódio" cellRenderer={(rowIndex, columnIndex) => {
                        return (
                            <Cell>
                            {linksFilter[rowIndex].episodio}
                            </Cell>
                        )
                        }} />
                    <Column key={1} name="Link" cellRenderer={(rowIndex, columnIndex) => {
                        return (
                            <Cell>
                                {linksFilter[rowIndex].linkConvertido}
                            </Cell>
                        )
                        }} />
                    <Column key={3} name="Ações" cellRenderer={(rowIndex, columnIndex) => {
                        return (
                            <Cell style={{ justifyContent: 'center' }}>
                                <Button 
                                    className={Classes.MINIMAL} 
                                    loading={isWaiting} 
                                    onClick={() => downloadLink(tp, rowIndex)}
                                >
                                    Baixar
                                </Button>
                                <Button 
                                    className={Classes.MINIMAL} 
                                    onClick={() => gerarLink(tp, rowIndex)}   
                                >
                                    Gerar link
                                </Button>
                            </Cell>
                        )
                    }} />
                </Table>
           
            </>
        );
    }

    const mudarAbaTipo = (tabId) => {
        setAbaSelecionada(tabId);
    }

    return (
        <>
            <Tabs
                animate={true}
                id="navbar"
                large={true}
                onChange={mudarAbaTipo}
                selectedTabId={abaSelecionada}
             >
                 <Tab id="mega" title="Mega" panel={abaTipo('Mega')} />
                 <Tab id="panda" title="Panda" panel={abaTipo('PandaFiles')} />
            </Tabs>
           
            <Pre>
                {megaCmdStdout}
            </Pre>
        </>
    );
};

export default PainelLinks