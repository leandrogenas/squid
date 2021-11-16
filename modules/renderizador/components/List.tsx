import React from 'react'
import ListItem from './ListItem'
import { Tabela } from '../types'

type Props = {
  items: Tabela[]
}

const List = ({ items }: Props) => (
  <ul>
    {items.map((item) => (
      <li key={item.name}>
        <ListItem data={item} />
      </li>
    ))}
  </ul>
)

export default List
