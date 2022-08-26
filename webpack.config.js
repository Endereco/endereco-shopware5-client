var path = require('path');
var TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  mode: process.env.NODE_ENV,
  entry: {
    'endereco': './endereco.js',
  },
  output: {
    path: path.resolve(__dirname, './Resources/views/frontend/_public/src/js/'),
    publicPath: '/',
    filename: 'endereco.min.js'
  },
  optimization: {
    minimize: true,
    minimizer: [new TerserPlugin({
      sourceMap: false,
      terserOptions: {
        output: {
          comments: false,
        },
      },
      extractComments: false,
    })],
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          'css-loader',
        ],
      },
      {
        test: /\.scss$/,
        use: [
          'css-loader',
          'sass-loader'
        ],
      },
      {
        test: /\.sass$/,
        use: [
          'sass-loader?indentedSyntax'
        ],
      },
      {
        test: /\.html$/,
        use: {loader: 'html-loader'}
      },
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        test: /\.svg$/,
        use: {loader: 'html-loader'}
      },
      {
        test: /\.(png|jpg|gif)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]?[hash]'
        }
      }
    ]
  },
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    overlay: true
  },
  performance: {
    hints: false
  },
  devtool: '(none)',
  plugins: [
  ]
};
