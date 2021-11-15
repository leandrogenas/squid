
const path = require('path');
const webpack = require('webpack');
const cwd = process.cwd();

module.exports = {
  rendererSrcDir: '../renderizador',
  mainSrcDir: './src',

  webpack: (defaultConfig, env) => Object.assign(defaultConfig, {
    // ...defaultConfig
    // plugins: [
    //   new webpack.IgnorePlugin({
    //     checkResource: function(resource) {
    //       console.log('alow,', resource)
    //       const lazyImports = [
    //           '@nestjs/microservices',
    //       ];
        
    //       for(var lazy in lazyImports)
    //         if(resource.includes(lazy))
    //           return true
      
    //       try { require.resolve(resource) } catch (err) {
    //         return true;
    //       }

    //       return false;
    //     }
    //   }),
    // ],
    // resolve: {
    //   ...defaultConfig.resolve,
    //   alias: {
    //     '@erpcs/common': path.resolve(__dirname, '../common/dist'),
    //     '@erpcs/service': path.resolve(__dirname, '../service/dist'),
    //   },
    //   // extensions: [".js", ".jsx", ".json", ".ts", ".tsx"],
    //   // modules: [
    //   //   './src',
    //   //   './src/helpers',
    //   //   './app',
    //   //   '../common/dist',
    //   //   'node_modules'
    //   // ]
    // },
    // entry: {
    //   ...defaultConfig.resolve.entry,
    //   background: './src/background.ts',
    //   helpers: './src/helpers/index.ts'
    // },
    output: {
      path: path.resolve(cwd, 'dist'),
      library: 'squid',
      libraryTarget: "commonjs2",
      filename: '[name].js',
      auxiliaryComment: 'Pacote principal',
    },
  }),
};