import React, {useState} from 'react'
import {Provider, useDispatch} from 'react-redux'
import {PageTransition} from 'next-page-transitions'
import {AppProps} from 'next/app'
import {useRouter} from 'next/router'

import "normalize.css";
import '@blueprintjs/core/lib/css/blueprint.css'
import '@blueprintjs/icons/lib/css/blueprint-icons.css'
import '@blueprintjs/table/lib/css/table.css'

import store from '../store'
import Loader from '../components/Loader'

import {GiSquidHead} from "react-icons/gi";
import {
  Breadcrumb,
  BreadcrumbProps,
  Button,
  ButtonGroup,
  Classes,
  Divider,
  Menu,
  MenuDivider,
  MenuItem,
  Popover,
  Position,
} from "@blueprintjs/core";
import SquidTopBar from '../components/SquidTopBar';
import { setPaginaAtual } from '../reducers/squidSlice'

const TIMEOUT = 400

function SquidApp({Component, pageProps}: AppProps) {

  const menu = (
    <Menu>
      <MenuItem icon="home" text="Dashboard"/>
      <MenuItem icon="globe" text="Sites"/>
      <MenuDivider/>
      <MenuItem icon="cog" text="Configurações"/>
    </Menu>
  )

  const router = useRouter();

  store.dispatch(setPaginaAtual(router.asPath));

  const renderCurrentBreadcrumb = ({text, ...restProps}: BreadcrumbProps) => {
    return (
      <>
        <Breadcrumb {...restProps}>
          <ButtonGroup>
            <Popover content={menu} position={Position.RIGHT_TOP}>
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
      <Provider store={store}>
        <SquidTopBar />
        <PageTransition
          timeout={TIMEOUT}
          classNames="page-transition"
          loadingComponent={<Loader/>}
          loadingDelay={500}
          loadingTimeout={{
            enter: TIMEOUT,
            exit: 0,
          }}
          loadingClassNames="loading-indicator"
        >

          <Component {...pageProps} />

        </PageTransition>

        <style jsx global>{`
          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          }

          body {
            font-family: 'KoHo', sans-serif;
            font-size: 16px;
            background-color: #191622;
          }

          .bp3-navbar {
            -webkit-user-select: none;
            -webkit-app-region: drag;
            height: 10vh;
          }

          .bp3-button {
            -webkit-app-region: no-drag;
          }

          .bp3-tag {
            margin: 0.1em;
          }

          button:focus,
          table:focus {
            outline: 0;
          }

          .page-transition-enter {
            opacity: 0;
            transform: translate3d(0, 20px, 0);
          }

          .page-transition-enter-active {
            opacity: 1;
            transform: translate3d(0, 0, 0);
            transition: opacity ${TIMEOUT}ms, transform ${TIMEOUT}ms;
          }

          .page-transition-exit {
            opacity: 1;
          }

          .page-transition-exit-active {
            opacity: 0;
            transition: opacity ${TIMEOUT}ms;
          }

          .loading-indicator-appear,
          .loading-indicator-enter {
            opacity: 0;
          }

          .loading-indicator-appear-active,
          .loading-indicator-enter-active {
            opacity: 1;
            transition: opacity ${TIMEOUT}ms;
          }
        `}</style>
      </Provider>
    </>
  )
}

export default SquidApp
