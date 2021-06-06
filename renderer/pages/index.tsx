import React, { useEffect, useState } from 'react'
import Link from 'next/link'
import Layout from '../components/Layout'
import { Button, Pre } from '@blueprintjs/core'
import {useAppDispatch, useAppSelector} from '../store'
import { sincronizarSiteThunk } from '../reducers/Sites/sitesSlice'
import {selectSquid, statusMegaThunk} from "../reducers/Squid/squidSlice";
import { listarSeriesThunk, selectSeries } from '../reducers/Series/seriesSlice'

const IndexPage: React.FC = () => {

  const [megaCmdStdout, setMegaCmdStdout] = useState<any[]>([]);
  const [syncando, estaSyncando] = useState<boolean>(false);

  const dispatch = useAppDispatch();
  const squid = useAppSelector(selectSquid);
  const series = useAppSelector(selectSeries);

  useEffect(() => {

    // add a listener to 'message' channel
    global.ipcRenderer.addListener('megacmd-stdout', (_event, args) => {
      megaCmdStdout.push(args);
      setMegaCmdStdout(megaCmdStdout);
    })
  }, [])

  const testeClick = () => {
    estaSyncando(true)
    dispatch(sincronizarSiteThunk('seriesmp4'));
  }

  const outroTesteClick = () => {
    dispatch(listarSeriesThunk()).then(data => {
      console.log(data);
    })
    // console.log(squid);
    // dispatch(statusMegaThunk()).then(data => {
    //   console.log(data);
    // })
  }

  return (
    <Layout title="Home | Next.js + TypeScript + Electron Example">
      <Button loading={syncando} onClick={testeClick}>Syncar</Button>
      <Button onClick={outroTesteClick}>Teste</Button>
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
