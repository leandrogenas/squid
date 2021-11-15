import Baixavel from "./Baixavel";
import InfosPostMP4Series from "./InfosPostMP4Series";
import Link from "./Link";

export default interface Serie extends Baixavel {
    tipo: 'SERIE'
	slug: string
	tituloShow: string
	wpCategory: number
	modificado: string
	post: string
	sinopse: string
	html: string
	infos?: InfosPostMP4Series
	linksConvertidos?: Link[]
}