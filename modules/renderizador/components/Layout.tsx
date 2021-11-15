import React, { ReactNode } from 'react'
import Head from 'next/head'



type Props = {
  children: ReactNode
  title?: string
}

const Layout = ({
  children,
  title = 'This is the default title'
}: Props) => (
  <div style={{ marginTop: '10vh', height: '90vh' }}>
  
  {children}
  {/* <footer>
    <hr />
    <span>I'm here to stay (Footer)</span>
  </footer> */}
  </div>
);

export default Layout
