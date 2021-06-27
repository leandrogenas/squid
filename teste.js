const parser = require('html-react-parser');
const { readFileSync } = require("fs");
const CSSselect = require("css-select");

const html = parser.htmlToDOM(readFileSync('./saida.html').toString());

const q = CSSselect.selectOne('[name="method_premium"]', html);

const getLink = (e) => {
    if(!e.next.hasOwnProperty('next'))
        return false;

    if(!e.next.next.name == 'a')
        return false;

    return e.next.next.attribs.href;
}

function antiAnti (e) {
    const link = getLink(e);
    if(!link){
        return parser
            .htmlToDOM(
                q.nextSibling.next.next
                    .next.data
            ).filter(sibling => {
                if(sibling.name != 'a')
                    return;
                
                return sibling
            })[0].attribs.href;
        
    }

    return link;

};


console.log(antiAnti(q))

// console.log(q.nextSibling.next.next.next);
//console.log(q.next.next.attribs.href);