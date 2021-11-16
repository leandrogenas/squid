import { 
    Card, 
    H5
} from "@blueprintjs/core";

import { 
    MdMoveToInbox,
    MdCloudQueue
} from "react-icons/md";

import { 
    CgArrowsExchangeAlt
} from "react-icons/cg";

import styled from "styled-components";
import { useRouter } from "next/router";
import Site from "../model/Site";

const Secao = styled.div`
  display: flex;
  margin: 1em;
  width: 310px;
  height: 200px;
  background: linear-gradient(to right, #dae2f8, #d6a4a4);
`

const CardSite: React.FC<Site> = (props: Site) => {

    const router = useRouter();

    const openSite = e => {
        router.push(`/site/${props.uuid}`);
    }

    return (
        <Secao>
            <style jsx>{`
                .site{
                    display: flex;
                    flex-direction: row;
                    margin-top: 2em;
                    width: 100%;
                }
        
                .site div {
                    text-align: center;
                    display: flex;
                    width: 33%;
                    flex-direction: column;
                }

                .site div .metrica,
                .site div.separador {
                    font-size: 2em;
                    width: 100%;
                }

                .site div.separador {
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
            `}</style>

        <Card onClick={openSite} style={{backgroundColor: 'transparent', width: '100%'}} interactive elevation={2}>
            <H5>
                <a href="#">{props.nome ?? ''}</a>
            </H5>
            <div className="site">
                <div >
                    <span className="metrica">
                        <MdCloudQueue />
                    </span>
                    <span className="bp3-monospace-text">
                        {props.baixaveis} baixáveis
                    </span>
                </div>
                <div className="separador">
                    <CgArrowsExchangeAlt color="#0A6640" />
                </div>
                <div >
                    <span className="metrica">
                        <MdMoveToInbox />
                    </span>
                    <span className="bp3-monospace-text">
                        {props.baixados} baixados
                    </span>
                </div>
            </div>
            
        </Card>
        </Secao>
    );

}

export default CardSite;