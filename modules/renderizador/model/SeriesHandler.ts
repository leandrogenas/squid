import Baixavel from "./Baixavel";
import Link from "./Link";
import Serie from "./Serie";

export default interface SeriesHandler extends Serie {
    obterLinks(): Promise<Link[]>
}