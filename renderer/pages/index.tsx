import React, { useEffect, useState } from 'react'
import Link from 'next/link'
import Layout from '../components/Layout'
import { Button, Pre } from '@blueprintjs/core'
import { useAppDispatch } from '../store'
import { sincronizarSiteThunk } from '../reducers/Sites'

const IndexPage: React.FC = () => {

  const [megaCmdStdout, setMegaCmdStdout] = useState<any[]>([]);
  const [syncando, estaSyncando] = useState<boolean>(false);

  const dispatch = useAppDispatch();

  useEffect(() => {
    // add a listener to 'message' channel
    global.ipcRenderer.addListener('megacmd-stdout', (_event, args) => {
      megaCmdStdout.push(args);
      setMegaCmdStdout(megaCmdStdout);
    })
  }, [])

  const testeClick = () => {
    estaSyncando(true)
    dispatch(sincronizarSiteThunk('uuid')).then(data => {
      console.log(data);
      estaSyncando(false);
    })
  }

  return (
    <Layout title="Home | Next.js + TypeScript + Electron Example">
      <Button loading={syncando} onClick={testeClick}>Syncar</Button>
      <p>
        <Link href="/about">
          <a>About</a>
        </Link>
      </p>
      <Pre>
        {megaCmdStdout}
      </Pre>
    </Layout>
  )
}

export default IndexPage
