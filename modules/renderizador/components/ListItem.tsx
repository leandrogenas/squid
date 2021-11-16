import React from 'react'
import Link from 'next/link'

import { Tabela } from '../types'

type Props = {
  data: Tabela
}

const ListItem = ({ data }: Props) => (
  <Link href="/detail/[id]" as={`/detail/${data.name}`}>
    <a>
      {data.name}: {data.name}
    </a>
  </Link>
)

export default ListItem
