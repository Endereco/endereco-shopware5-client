const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  mode: process.env.NODE_ENV || 'development',
  entry: {
    endereco: './endereco.js',
  },
  output: {
    path: path.resolve(__dirname, './Resources/views/frontend/_public/src/js/'),
    publicPath: '/',
    filename: 'endereco.min.js',
    clean: true, // Ensures the output directory is cleaned before each build
  },
  optimization: {
    minimize: true,
    minimizer: [new TerserPlugin({
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
          'css-loader'
        ],
      },
      {
        test: /\.scss$/,
        use: [
          'css-loader', 
          'sass-loader',
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
        use: {
          loader: 'html-loader',
          options: {
            sources: false,
            minimize: false
          },
        },
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.svg$/,
        use: 'html-loader',
      },
      {
        test: /\.(png|jpg|gif)$/,
        type: 'asset/resource',
        generator: {
          filename: '[name][ext]?[hash]',
        },
      },
    ],
  },
  devServer: {
    static: {
      directory: path.join(__dirname, 'public'),
    },
    historyApiFallback: true,
    compress: true,
    port: 9000,
    open: true,
  },
  performance: {
    hints: false,
  },
  devtool: false,
  plugins: [
  ],
};
