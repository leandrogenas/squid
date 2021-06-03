import Link from 'next/link'
import { useRouter } from 'next/router'
import Layout from '../components/Layout'
import List from '../components/List'
import { Site, User } from '../types'

type Props = {
  items: User[]
  pathname: string
}

const WithInitialProps = ({ items }: Props) => {
  const router = useRouter()
  return (
    <Layout title="List Example (as Function Component) | Next.js + TypeScript + Electron Example">
      <h1>List Example (as Function Component)</h1>
      <p>You are currently on: {router.pathname}</p>
      <List items={items} />
      <p>
        <Link href="/">
          <a>Go home</a>
        </Link>
      </p>
    </Layout>
  )
}

export async function getStaticProps() {
  const items: Site[] = [
    { 
      status: 'sincronizado',
      uuid: '9666e8bb-c801-431f-9d9c-94443f3ccd91',
      nome: 'baixarseriesmp4.net', 
      baixaveis: 132, 
      baixados: 30, 
      usaApi: false, 
      usaWeb: false, 
      usaWordpress: true,
      sincronizando: false,
    }
  ]

  return { props: { items } }
}

export default WithInitialProps
