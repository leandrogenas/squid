import {
    Button,
    Callout,
    Classes,
    Intent,
    NumericInput,
    PanelProps,
    Position,
    Pre,
    Tab,
    Tabs, Tag,
    Toaster
} from "@blueprintjs/core";
import { Cell, Column, ICellProps, Table } from "@blueprintjs/table";
import { AxiosInstance } from "axios";
import React, { createRef, ReactElement, useRef, useState } from "react";
import { Props } from "./[uuid]";
import * as CSSselect from 'css-select'
import axios from 'axios';
import store, { useAppDispatch } from "../../store";
import { connect } from "react-redux";
import { useEffect } from "react";
import { htmlToDOM, Element } from "html-react-parser";
import Link, { ProvedorLink } from "../../model/Link";
import Download from "../../model/Download";
import styled from "styled-components";
import TabelaLinks from "../../components/TabelaLinks";
import { novoDownloadThunk } from "../../reducers/Download/downloadThunks";
//import { adicionar as novoDownload } from "../../reducers/Downloads";

export interface PainelLinksInfo {
    site: any
    serie: any
}

const Container = styled(Callout)`
        width: 90vw;
        margin: 1em auto;
        display: flex;
        flex-direction: row;
    `;

const Conteudo = styled.div`
      display: flex;
      flex-direction: row;
      padding: 0 1.75em;
    `;

const ConteudoItem = styled.div`
    
    `

const Capa = styled.img`
      width: 250px;
      height: 330px;
    `

type LinkCellRenderer = (rowIndex: number, columnIndex: number) => ReactElement<ICellProps>

type Tabela = (data: Link[]) => {
  coluna: {
    titulo: LinkCellRenderer
    link: LinkCellRenderer
    acoes: LinkCellRenderer
  },
  larguras: number[]
}

type LinkPainel = {
    link: Link,
    aguardando: boolean
}

const Toast = (typeof window !== 'undefined')
    ? Toaster.create({
        className: 'my-toaster',
        position: 'bottom',
        maxToasts: 3,
        canEscapeKeyClear: true
    })
    : null;

const PainelLinks: React.FC<PanelProps<PainelLinksInfo>> = props => {
    const [links, setLinks] = useState<Link[]>(props.serie.links);
    const [linksConvertidos] = useState<Link[]>([]);
    const [isWaiting, setIsWaiting] = useState(false);
    const [convertendo, setConvertendo] = useState(false);
    const dispatch = useAppDispatch()

    const downloadLink = (tipo: ProvedorLink, rowIdx: number) => {
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

    const gerarLink = (tipo: ProvedorLink, rowIdx: number) => {
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

    const episodios: string[] = [];
    links.forEach((link) => {
        if(episodios.includes(link.titulo))
            return;
        episodios.push(link.titulo);
    });

    const abaTipo = (tp: ProvedorLink) => {

        const linksFilter = links.filter(link => (link.provedor == tp));
        return (
            <>

           
            </>
        );
    }

    const mudarAbaTipo = (tabId) => {
        setAbaSelecionada(tabId);
    }


    const {
        imgURL,
        tituloOriginal,
        sinopse,
        formato,
        duracao,
        lancamento,
        legenda,
        tamanho,
        criador,
    } = props.serie.infos;

    return (
        <>
            <Container>
                <Capa src={imgURL} />
                <Conteudo>
                    <ConteudoItem>
                        <h2>{tituloOriginal}</h2>
                        <div>
                            <Tag>{formato}</Tag>
                            <Tag>{duracao}</Tag>
                            <Tag>{lancamento}</Tag>
                            <Tag>{legenda}</Tag>
                            <Tag>{tamanho}</Tag>
                            <Tag>{criador}</Tag>
                        </div>
                        <p className="bp3-text-muted">{sinopse}</p>
                    </ConteudoItem>
                </Conteudo>
            </Container>
            <Tabs
                animate={true}
                id="navbar"
                large={true}
                onChange={mudarAbaTipo}
                selectedTabId={abaSelecionada}
            >
                <Tab id="mega" title="Mega" panel={<TabelaLinks links={links} />} />
                {/*<Tab id="panda" title="Panda" panel={abaTipo('PANDA')} />*/}
            </Tabs>
           
            {/* <Pre>
                {megaCmdStdout}
            </Pre> */}
        </>
    );
};

export default PainelLinks