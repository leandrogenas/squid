import { Button, Classes, InputGroup, Intent, NumericInput, PanelProps } from "@blueprintjs/core";
import { Cell, Column, ICellProps, RenderMode, Table } from "@blueprintjs/table";
import React, { createRef, ReactElement, useState } from "react";
import { useSelector } from "react-redux";
import Site from "../../model/Site";
import { useAppSelector } from "../../store";
import { PainelSeriesInfo } from "../../types";
import PainelLinks from "./links.panel";
import { Props } from "./[uuid]";


const PainelSeries: React.FC<PanelProps<PainelSeriesInfo>> = props => {
    
    const { site } = props;

    const tblRef = createRef<Table>();

    const [series, setSeries] = useState(props.series);

    const handleBuscar = e => 
    {
      const buscaStr = e.target.value;
      const busca = props.series.filter(serie => {
        const titulo = serie.titulo.toUpperCase();
        if(titulo.includes(buscaStr.toUpperCase()))
          return serie;
      });
      setSeries(Object.assign(busca, {}))
      // tblRef.current?.forceUpdate();

    }

    const openLinks = (serieUUID) => 
    {
        const serie = series.find(serie => serie.uuid == serieUUID);

        console.log(serie);
        props.openPanel({
            props: { 
                serie: serie,
                site: site
            },
            renderPanel: PainelLinks,
            title: `Links de ${serie.titulo}`,
        });
        
    };

    return (
        <div style={{ height: '85vh' }}>
            <InputGroup
              leftIcon="search"
              onChange={handleBuscar}
              placeholder="buscar série"
            />
            
            <Table 
                // renderMode={RenderMode.NONE}
                ref={tblRef}
                numRows={(series) ? series.length : 0}
                defaultRowHeight={40}
                columnWidths={[690, 55]}
                  
             >
                <Column key={0} name="Post" cellRenderer={(rowIndex, columnIndex) => {
                  return (
                    <Cell>
                      {series[rowIndex].titulo}
                    </Cell>
                  )
                }} />
                <Column key={3} name="Links" cellRenderer={(rowIndex, columnIndex) => {
                  return (
                    <Cell>
                      <Button style={{ margin: '0 auto' }} className={Classes.MINIMAL} icon='chevron-right' onClick={() => openLinks(series[rowIndex].uuid)} />
                    </Cell>
                  )
                }} />
            </Table>
        </div>
    );
};

export default PainelSeries