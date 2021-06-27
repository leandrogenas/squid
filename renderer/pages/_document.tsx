import Document, { Html, Head, Main, NextScript } from 'next/document'

class SquidDocument extends Document {
  static async getInitialProps(ctx) {
    const initialProps = await Document.getInitialProps(ctx)
    return { ...initialProps }
  }

  render() {
    return (
      <Html>
        <Head>
          <meta charSet="utf-8" />
          {/*<meta name="viewport" content="initial-scale=1.0, width=device-width" />*/}
          <link rel="preconnect" href="https://fonts.gstatic.com"/>
          <link href="https://fonts.googleapis.com/css2?family=KoHo:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
        </Head>
        <body >
          <Main />
          <NextScript />
        </body>
      </Html>
    )
  }
}

export default SquidDocument