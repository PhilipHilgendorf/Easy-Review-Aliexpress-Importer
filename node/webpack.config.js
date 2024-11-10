const path = require('path');
const CompressionPlugin = require('compression-webpack-plugin'); // Import CompressionPlugin
const HtmlWebpackPlugin = require('html-webpack-plugin'); // Import HtmlWebpackPlugin
const webpack = require('webpack');

module.exports = {
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
        },
      },
    ],
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  plugins: [
    new HtmlWebpackPlugin({
      template: './src/index.html',
    }),
    new CompressionPlugin({  // Add CompressionPlugin here
      filename: '[path][base].gz', // Define the output compressed file format
      algorithm: 'gzip', // Use gzip compression
      test: /\.(js|css|html|svg)$/, // Match file types to compress
      threshold: 8192, // Only compress files larger than 8kB
      minRatio: 0.8, // Only compress files with compression ratio below this threshold
      deleteOriginalAssets: false, // Set to true if you want to remove the original uncompressed files
    }),
  ],
  devServer: {
    static: {
      directory: path.join(__dirname, 'dist'),
    },
    compress: true,
    port: 3000,
  },
};