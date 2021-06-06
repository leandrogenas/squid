import React, { createRef, useEffect, useState } from 'react'

// import { NextPageContext } from 'next'
import Layout from '../components/Layout'
import { Cell, Column, RenderMode, Table, TableLoadingOption } from "@blueprintjs/table";
import { Button, ButtonGroup, Classes, Drawer, ProgressBar } from '@blueprintjs/core'
import { AppDispatch, AppState, useAppDispatch, useAppSelector } from '../store'
import { limparDownloads, listarDownloadsThunk, selectDownloads } from '../reducers/Downloads/downloadsSlice'
import { Download, ListagemDownloads, Tabela } from '../types';
import { connect, useSelector } from 'react-redux';
import { IMenuContext } from '@blueprintjs/table';
import { Menu } from '@blueprintjs/core';
import { MenuItem } from '@blueprintjs/core';
import { MenuDivider } from '@blueprintjs/core';
import { FaTrash } from 'react-icons/fa';
import { toggleDownloadsAberto } from '../reducers/Squid/squidSlice';

type Params = {
  uuid?: string
}

type Progresso = {
  downloadUUID: string,
  downloadTipo: string,
  perc: number
}

const Downloads = (props) => 
{
  
    const [carregando, setCarregando] = useState(false);
    const [downloads, setDownloads] = useState<Download[]>([]);
    const [progressos, setProgressos] = useState<Progresso[]>([]);

  
    useEffect(() => 
    {
      
      if((!downloads || downloads.length == 0) && !carregando){
        setCarregando(true);
        props.getDownloads().then(data => {
          setDownloads(data.payload);
          
          const progressosRaw: Progresso[] = data.payload.map(download => {
            return {
              downloadUUID: download.uuid,
              downloadTipo: download.tipo,
              perc: 0
            }
          })
          setProgressos(progressosRaw);

          

          setCarregando(false);
        })      
      }
  
  
    }, [downloads])

    useEffect(() => 
    {
      if(progressos.length > 0 && carregando){
        progressos.forEach((progresso: Progresso) => {
          global.ipcRenderer.on(progresso.downloadUUID, (event, args: string) => {
            console.log(args);
            const copy = Object.assign(progressos);
            setProgressos(copy.map((p) => {
              if(p.downloadUUID != progresso.downloadUUID)
                return p;
              return {
                downloadUUID: p.downloadUUID,
                downloadTipo: p.downloadTipo,
                perc: (p.downloadTipo == 'Mega')
                  ? parseFloat(args.substr(-11, 5))
                  : (([baixado, total]) => parseFloat(baixado) / parseFloat(total))(args.split('/'))
              }

              
            }))

          })

          setCarregando(false);
        })
      }
    }, [progressos]);
  
    if(carregando)
      return (
        <h1>Carregando</h1>
      )

    const tableRef = createRef<Table>();
  
  
    const getLoadingOptions = () => {
      const loadingOptions: TableLoadingOption[] = [];
  
      if(downloads.length == 0 || carregando){
        loadingOptions.push(TableLoadingOption.CELLS);
        loadingOptions.push(TableLoadingOption.ROW_HEADERS);
      }
      
      return loadingOptions;
  }
  
  const renderColProgresso = (rowIdx: number) => 
  {
    return (
      <Cell>
        <ProgressBar intent="success" value={progressos[rowIdx].perc} />
      </Cell>
    )
  }

  const renderColDownload = (rowIdx: number) => 
  { 
    return (
      <Cell>
          {downloads[rowIdx].nome}
          {downloads[rowIdx]?.serie?.titulo}
      </Cell>
    )
  }

  const renderContextMenu = (context: IMenuContext) => {
    context
    return (
        <Menu>
            <MenuItem icon="select" text="Select all" />
            <MenuItem icon="insert" text="Insert...">
                <MenuItem icon="new-object" text="Object" />
                <MenuItem icon="new-text-box" text="Text box" />
                <MenuItem icon="star" text="Astral body" />
            </MenuItem>
            <MenuItem icon="layout" text="Layout...">
                <MenuItem icon="layout-auto" text="Auto" />
                <MenuItem icon="layout-circle" text="Circle" />
                <MenuItem icon="layout-grid" text="Grid" />
            </MenuItem>
            <MenuDivider />
            <MenuItem disabled={true} text={`Clicked at`} />
        </Menu>
    );
  }
  
    return (
      <Drawer
        icon="inbox-geo"
        onClose={(e) => { 
            e.preventDefault()
            e.stopPropagation();
            props.toggleAberto() 
            return false;
        }}
        title="Downloads"
        isOpen={props.aberto}
        autoFocus={true}
        hasBackdrop={true}
        position="right"
        enforceFocus={true}
        canOutsideClickClose={true}
        canEscapeKeyClose={true}
        usePortal={false}
       >
        <div className={Classes.DRAWER_BODY}>
            <div className={Classes.DIALOG_BODY}>
                <style jsx global>{`
                
                .bp3-table-row-name,
                .bp3-table-cell > * {
                    display: flex;
                    height: 100%;
                    align-items: center;
                }
                .bp3-table-cell,
                .bp3-table-container {
                    box-shadow: none
                }
                .bp3-table-top-container,
                .bp3-table-quadrant-top {
                    display: none
                }
                `}</style>
                <Table 
                  bodyContextMenuRenderer={renderContextMenu}
                  numRows={downloads.length}
                  enableRowHeader={false}
                  columnWidths={[200, 150]}
                  enableGhostCells={true}
                  loadingOptions={getLoadingOptions()}
                 >
                  <Column key={0} name="Download"  cellRenderer={renderColDownload} />
                  <Column key={2} name="Progresso" cellRenderer={renderColProgresso} />
                </Table>

            </div>
        </div>
        <div className={Classes.DRAWER_FOOTER}>
            <ButtonGroup minimal={true}>
                <Button 
                  icon={<FaTrash />}
                  onClick={() => {
                    props.limpar()
                  }}
                 >
                  Limpar
                </Button>
            </ButtonGroup>
        </div>
      </Drawer>

    )
  }

type Props = {
  aberto: boolean
  downloadsState: ListagemDownloads
  getDownloads: Promise<Download[]>
  limpar: () => void
  toggleAberto: () => void
}
  
function mapStateToProps(state: AppState) {
  return {
      downloadsState: state.downloads,
      aberto: state.squid.downloadsAberto
  }
}

export default connect(mapStateToProps, {
  getDownloads: () => 
  {
    return function (dispatch: AppDispatch) 
    {
      return dispatch(listarDownloadsThunk(0))
    }
  },
  limpar: limparDownloads,
  toggleAberto: toggleDownloadsAberto
})(Downloads)