import React from 'react'
import Link from 'next/link'

import { User } from '../types'

type Props = {
  data: User
}

const ListItem = ({ data }: Props) => (
  <Link href="/detail/[id]" as={`/detail/${data.id}`}>
    <a>
      {data.id}: {data.name}
    </a>
  </Link>
)

export default ListItem
