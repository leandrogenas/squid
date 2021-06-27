import React from 'react';
import {useRouter} from 'next/router'
import * as uuid from 'uuid';
import {
  Alignment,
  Breadcrumb,
  BreadcrumbProps,
  Breadcrumbs,
  Button,
  ButtonGroup,
  Classes,
  Divider,
  Menu,
  MenuDivider,
  MenuItem,
  Navbar,
  NavbarDivider,
  NavbarGroup,
  NavbarHeading,
  Popover,
  Position,
  Tooltip
} from '@blueprintjs/core';

import {
  GiSquidHead,
  GiWorld
} from "react-icons/gi";

import {
  GrInbox,
  GrUserWorker,
  GrCloudDownload,
  GrCloudUpload
} from "react-icons/gr";

import {BsInboxesFill} from 'react-icons/bs'

import {
  FaWindowClose,
  FaRegWindowMaximize,
  FaRegWindowMinimize,
} from "react-icons/fa";

import Downloads from '../Downloads';
import {connect} from 'react-redux';
import {AppDispatch, AppState, useAppDispatch, useAppSelector} from '../../store';
import ControleJanela from "./ControleJanela";
import { selectSquid, toggleDownloadsAberto } from '../../reducers/squidSlice';

export type SquidTopBarProps = {
  paginaAtual: string
  downloadsAberto: boolean,
  toggleDownloads: () => void
}

const SquidTopBar = (props: SquidTopBarProps) => {

  const router = useRouter();

  const dispatch = useAppDispatch();

  const squid = useAppSelector(selectSquid);

  console.log(squid.paginaAtual);

  const base = [
    {slug: '/index', icon: <GiSquidHead style={{marginTop: '10px'}} fontSize="2em"/>}
  ]

  const links = [
    {slug: '/sites', icon: <GiWorld/>, text: 'Sites'},
    {slug: '/downloads', icon: <GrCloudDownload/>, text: 'Downloads'},
    // { slug: '/downloads', icon: 'cloud-download', text: 'Downloads' },
  ]

  const obterClassesBotao = (link: string) => {
    if (link == router.asPath)
      return [Classes.MINIMAL, Classes.ACTIVE];

    return [Classes.MINIMAL];
  }

  const BREADCRUMBS: BreadcrumbProps[] = [
    {text: props.paginaAtual}
    // { href: "/sites", icon: <GiWorld />, text: "Sites" },
    // { href: "/downloads", icon: <RiInboxArchiveFill />, text: 'Downloads' },
  ];
  const menu = (
    <Menu>
      <MenuItem icon="home" text="Dashboard" onClick={() => router.push('/index')}/>
      <MenuItem icon="globe" text="Sites" onClick={() => router.push('/sites')}/>
      <MenuDivider/>
      <MenuItem icon="cog" text="Configurações" onClick={() => router.push('/configs')}/>
    </Menu>
  )

  const renderCurrentBreadcrumb = ({text, ...restProps}: BreadcrumbProps) => {
    // customize rendering of last breadcrumb
    return (
      <>

        <Breadcrumb {...restProps}>
          <ButtonGroup>
            <Popover content={menu} position={Position.BOTTOM_RIGHT}>
              <Button className={Classes.MINIMAL} icon={<GiSquidHead fontSize="2em"/>}/>
            </Popover>
            <Divider style={{marginRight: '10px'}}/>
          </ButtonGroup>
          {text}
        </Breadcrumb>
      </>
    );
  };


  return (
    <>
      <Downloads/>
      <Navbar fixedToTop={true} className="drag">
        <NavbarGroup align={Alignment.LEFT}>
          <NavbarHeading>

            <Breadcrumbs

              currentBreadcrumbRenderer={renderCurrentBreadcrumb}
              overflowListProps={{
                alwaysRenderOverflow: false

              }}
              items={BREADCRUMBS}
            />


          </NavbarHeading>
        </NavbarGroup>
        <NavbarGroup align={Alignment.RIGHT}>
          <NavbarDivider/>

          <Tooltip content="Downloads" position={Position.LEFT}>
            <Button className={Classes.MINIMAL} icon={<GrCloudDownload />} onClick={() => {
              props.toggleDownloads()
            }}/>
          </Tooltip>
          <NavbarDivider/>
          <Button
            className={Classes.MINIMAL}
            onClick={() => ControleJanela.minimizar()}
            icon={<FaRegWindowMinimize/>}
          />
          <Button
            className={Classes.MINIMAL}
            onClick={() => ControleJanela.maximizar()}
            icon={<FaRegWindowMaximize/>}
          />
          <Button
            className={Classes.MINIMAL}
            onClick={() => ControleJanela.fechar()}
            icon={<FaWindowClose color="#E62C3A"/>}
          />
        </NavbarGroup>
      </Navbar>
    </>
  );
}

function mapStateToProps(state: AppState) {
  return {
    downloadsAberto: state.downloadsAberto,
    paginaAtual: state.paginaAtual
  }
}

export default connect(
  mapStateToProps,
  {
    toggleDownloads: toggleDownloadsAberto,
  }
)(SquidTopBar)