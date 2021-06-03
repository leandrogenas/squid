import React, { createRef, useEffect, useState } from 'react'

// import { NextPageContext } from 'next'
import Layout from '../components/Layout'
import { Cell, Column, Table, TableLoadingOption } from "@blueprintjs/table";
import { Button, ProgressBar } from '@blueprintjs/core'
import { AppDispatch, AppState, useAppDispatch, useAppSelector } from '../store'
import { listarDownloadsThunk, selectDownloads } from '../reducers/Downloads'
import { Download, ListagemDownloads, Tabela } from '../types';
import { connect, useSelector } from 'react-redux';
import { IMenuContext } from '@blueprintjs/table';
import { Menu } from '@blueprintjs/core';
import { MenuItem } from '@blueprintjs/core';
import { MenuDivider } from '@blueprintjs/core';

type Params = {
  uuid?: string
}

type Props = {
  downloadsState: ListagemDownloads
  getDownloads: Promise<Download[]>
}

const Downloads = (props) => {
  
  const [carregando, setCarregando] = useState(false);
  const [downloads, setDownloads] = useState<Download[]>([]);

  useEffect(() => {
    
    if(downloads.length == 0 && !carregando){
      setCarregando(true);
      props.getDownloads().then(data => {
        setDownloads(data.payload);
        setCarregando(false);
      })      
    }


  })

  const tableRef = createRef<Table>();


  const getLoadingOptions = () => {
    const loadingOptions: TableLoadingOption[] = [];

    if(downloads.length == 0 || carregando){
      loadingOptions.push(TableLoadingOption.CELLS);
      loadingOptions.push(TableLoadingOption.ROW_HEADERS);
    }
    
    return loadingOptions;
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
    <Layout
      title={`Downloads`}
    >
      <style jsx global>{`
        .bp3-table-row-name,
        .bp3-table-cell > * {
          display: flex;
          height: 100%;
          align-items: center;
        }
      `}</style>

        <Table 
          bodyContextMenuRenderer={renderContextMenu}
          numRows={downloads.length}
          defaultRowHeight={40}
          columnWidths={[313, 68, 253, 120]}
          loadingOptions={getLoadingOptions()}
        >
          <Column key={0} name="Nome" cellRenderer={(rowIdx: number) => {
          return (
            <Cell >
              {downloads[rowIdx].nome}
            </Cell>
          )
        }} />
          <Column key={1} name="Tipo" cellRenderer={(rowIdx: number) => {
          return (
            <Cell>
              {downloads[rowIdx].tipo}
            </Cell>
          )
        }} />
          <Column key={2} name="Progresso" cellRenderer={(rowIdx: number) => {
          return (
            <Cell>
              <ProgressBar intent="success" value={downloads[rowIdx].progresso} />
            </Cell>
          )
        }} />
          <Column key={3} name="Ações" cellRenderer={(_rowIdx: number) => {
          return (
            <Cell style={{ justifyContent: 'center' }}>
              <Button>opa</Button>
            </Cell>
          )
        }} />
        </Table>
    </Layout>
  )
}

function mapStateToProps(state) {
  return {
    downloadsState: state.downloads
  }
}

export default connect(
  mapStateToProps,
  {
    getDownloads: () => {
      return function (dispatch: AppDispatch) {
        return dispatch(listarDownloadsThunk())
      }
    }
  }
)(Downloads)
