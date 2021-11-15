import React from "react";

import {
  Alignment,
  Button,
  Classes,
  IconName,
  MaybeElement,
  Navbar,
  NavbarDivider,
  NavbarGroup,
  NavbarHeading,
} from "@blueprintjs/core";
import { 
  GiCloudDownload,
  GiSquidHead,
  GiWorld
} from "react-icons/gi";

import {
  RiInboxArchiveLine
} from "react-icons/ri";

import Link from "next/link";

type MenuLink = {
  slug: string
  icon: IconName | MaybeElement
  text: string
}

const Menu: React.FC = () => {

  const links = [
    { slug: '/dashboard', icon: <GiSquidHead />, text: 'Dashboard' },
    { slug: '/sites', icon: <GiWorld />, text: 'Sites' },
    { slug: '/downloads', icon: <GiCloudDownload />, text: 'Downloads' },
  ]

  const obterClassesBotao = (link: string) =>
  {
    if(link == window.location.href)
      return [Classes.MINIMAL, Classes.ACTIVE];

    return [Classes.MINIMAL];
  }

  return (
    <Navbar>
        <NavbarGroup align={Alignment.LEFT}>
            <NavbarHeading>
              <GiSquidHead style={{ marginTop: '10px' }} fontSize="2em" />
            </NavbarHeading>
        </NavbarGroup>
        <NavbarGroup align={Alignment.RIGHT}>
          <NavbarDivider />
            { links.map((link, i) => {
              return (
                <>
                  <Link key={i} href={link.slug}>
                    <Button 
                      className={obterClassesBotao(link.slug).join(' ')} 
                      icon={link.icon} 
                      text={link.text}
                    />
                  </Link>
                  <NavbarDivider />
                </>
              )
            }) }
            <Button className={Classes.MINIMAL} icon="cog" />
        </NavbarGroup>
    </Navbar>
  )
}

export default Menu