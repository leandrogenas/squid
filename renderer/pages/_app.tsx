import React, { useState } from 'react'
import { PageTransition } from 'next-page-transitions'
import { Provider } from 'react-redux'
import store from '../store'
import { AppProps } from 'next/app'
import Link from 'next/link'
import { useRouter } from 'next/router'
import Loader from '../components/Loader'

import {
  Alignment,
  Breadcrumb,
  BreadcrumbProps,
  Breadcrumbs,
  Button,
  ButtonGroup,
  Classes,
  Divider,
  Drawer,
  
  Menu,
  
  MenuDivider,
  
  MenuItem,
  
  Navbar,
  NavbarDivider,
  NavbarGroup,
  NavbarHeading,
  Popover,
  Position,
} from "@blueprintjs/core";
import { 
  GiSquidHead,
  GiWorld
} from "react-icons/gi";

import {
  RiHome2Fill,
  RiInboxArchiveFill
} from "react-icons/ri";

import "normalize.css";
import '@blueprintjs/core/lib/css/blueprint.css'
import '@blueprintjs/icons/lib/css/blueprint-icons.css'
import '@blueprintjs/table/lib/css/table.css'
import SquidTopBar from '../components/SquidTopBar'

const TIMEOUT = 400

function MyApp({ Component, pageProps }: AppProps) {
  const router = useRouter();

  const [downloadsAberto, setDownloadsAberto] = useState(false);

  

const menu = (
  <Menu>
    <MenuItem icon="home" text="Dashboard" />
    <MenuItem icon="globe" text="Sites" />
    <MenuItem icon="inbox-geo" text="Downloads" onClick={() => { setDownloadsAberto(true) }} />
    <MenuDivider />
    <MenuItem icon="cog" text="Configurações" />
  </Menu>
)

  const renderCurrentBreadcrumb = ({ text, ...restProps }: BreadcrumbProps) => {
    // customize rendering of last breadcrumb
    return (
      <>
        
        <Breadcrumb {...restProps}>
          <ButtonGroup>
            <Popover content={menu} position={Position.RIGHT_TOP}>
              <Button className={Classes.MINIMAL} icon={<GiSquidHead fontSize="2em" />} />
            </Popover>
            <Divider style={{ marginRight: '10px' }} />
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
        loadingComponent={<Loader />}
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

        button:focus,
        table:focus {outline:0;}

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

export default MyApp
