import { Alignment, Breadcrumb, BreadcrumbProps, Breadcrumbs, Button, ButtonGroup, Classes, Divider, Drawer, Menu, MenuDivider, MenuItem, Navbar, NavbarDivider, NavbarGroup, NavbarHeading, Popover, Position, Tooltip } from '@blueprintjs/core';
import React, { useState } from 'react';
import Layout from '../components/Layout'
import { render } from 'react-dom';
import { useRouter } from 'next/router'
import Link from 'next/link'

import { 
    GiSquidHead,
    GiWorld
} from "react-icons/gi";

import {
    RiHome2Fill,
    RiInboxArchiveFill
} from "react-icons/ri";

import {
    FaWindowMaximize,
    FaWindowMinimize,
    FaWindowRestore,
    FaWindowClose,
    FaRegWindowClose,
    FaRegWindowMaximize,
    FaRegWindowMinimize,
    FaTrash
} from "react-icons/fa";

import Downloads from './Downloads';
import { connect } from 'react-redux';
import { AppState } from '../store';
import { toggleDownloadsAberto } from '../reducers/Squid';

type Props = {
    downloadsAberto: boolean,
    toggleDownloads: () => void
}

const SquidTopBar = (props: Props) => {

    const router = useRouter();

    const base = [
        { slug: '/index', icon: <GiSquidHead style={{ marginTop: '10px' }} fontSize="2em" />   }
      ]
    
      const links = [
        { slug: '/sites', icon: <GiWorld />, text: 'Sites' },
        { slug: '/downloads', icon: <RiInboxArchiveFill />, text: 'Downloads' },
        // { slug: '/downloads', icon: 'cloud-download', text: 'Downloads' },
      ]
    
      const obterClassesBotao = (link: string) =>
      {
        if(link == router.asPath)
          return [Classes.MINIMAL, Classes.ACTIVE];
    
        return [Classes.MINIMAL];
      }
    
      const BREADCRUMBS: BreadcrumbProps[] = [
        { text: 'Squid Tasker' }
        // { href: "/sites", icon: <GiWorld />, text: "Sites" },
        // { href: "/downloads", icon: <RiInboxArchiveFill />, text: 'Downloads' },
    ];
    
    const menu = (
        <Menu>
          <MenuItem icon="home" text="Dashboard" onClick={() => router.push('/index')} />
          <MenuItem icon="globe" text="Sites" onClick={() => router.push('/sites')} />
          <MenuDivider />
          <MenuItem icon="cog" text="Configurações" onClick={() => router.push('/configs')} />
        </Menu>
    )

    const renderCurrentBreadcrumb = ({ text, ...restProps }: BreadcrumbProps) => {
        // customize rendering of last breadcrumb
        return (
          <>
            
            <Breadcrumb {...restProps}>
              <ButtonGroup>
                <Popover content={menu} position={Position.BOTTOM_RIGHT}>
                  <Button className={Classes.MINIMAL} icon={<GiSquidHead fontSize="2em" />} />
                </Popover>
                <Divider style={{ marginRight: '10px' }} />
              </ButtonGroup>
              {text} 
            </Breadcrumb>
          </>
        );
      };


    const ControleJanela = (() => {
        
        return {
            fechar: () => {
                global.ipcRenderer.send('janela', 'fechar')
            },
            maximizar: () => {
                global.ipcRenderer.send('janela', 'maximizar')
            },
            minimizar: () => {
                global.ipcRenderer.send('janela', 'minimizar')
            }
        }
    })()

    return (
        <>
            <Downloads />
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
                    <NavbarDivider />
                    
                        <Tooltip content="Downloads" position={Position.LEFT}>
                            <Button className={Classes.MINIMAL} icon="inbox-geo" onClick={() => { props.toggleDownloads() }} />
                        </Tooltip>
                        <NavbarDivider />
                        <Button 
                            className={Classes.MINIMAL} 
                            onClick={() => ControleJanela.minimizar()} 
                            icon={<FaRegWindowMinimize />} 
                         />
                        <Button 
                            className={Classes.MINIMAL} 
                            onClick={() => ControleJanela.maximizar()}
                            icon={<FaRegWindowMaximize />} 
                         />
                        <Button 
                            className={Classes.MINIMAL} 
                            onClick={() => ControleJanela.fechar()}
                            icon={<FaWindowClose color="#E62C3A" />} 
                         />
                </NavbarGroup>
            </Navbar>
        </>
    );
    

}

function mapStateToProps(state: AppState) {
    return {
        downloadsAberto: state.configs.downloadsAberto
    }
  }

export default connect(
    mapStateToProps,
    {
        toggleDownloads: toggleDownloadsAberto
    }
)(SquidTopBar)