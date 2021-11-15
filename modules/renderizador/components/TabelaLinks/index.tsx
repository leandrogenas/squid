import React from "react";
import {Cell, Column, Table} from "@blueprintjs/table";
import {Button, Classes} from "@blueprintjs/core";
import Link from "../../model/Link";

export type TabelaLinkProps = {
  links: Link[]
}

const TabelaLinks: React.FC<TabelaLinkProps> = props => {

  const { links } = props;

  return (
    <Table
      numRows={(links) ? links.length : 0}
      defaultRowHeight={40}
      columnWidths={[200, 350, 200]}
    >
      <Column key={0} name="Episódio" cellRenderer={(rowIndex, columnIndex) => {
        return (
          <Cell>
            {links[rowIndex].titulo}
          </Cell>
        )
      }} />
      <Column key={1} name="Link" cellRenderer={(rowIndex, columnIndex) => {
        return (
          <Cell>
            {links[rowIndex].url}
          </Cell>
        )
      }} />
      <Column key={3} name="Ações" cellRenderer={(rowIndex, columnIndex) => {
        return (
          <Cell style={{ justifyContent: 'center' }}>
            <Button
              className={Classes.MINIMAL}
              // loading={isWaiting}
              // onClick={() => downloadLink(tp, rowIndex)}
            >
              Baixar
            </Button>
            <Button
              className={Classes.MINIMAL}
              // onClick={() => gerarLink(tp, rowIndex)}
            >
              Gerar link
            </Button>
          </Cell>
        )
      }} />
    </Table>
  );

};

export default TabelaLinks;