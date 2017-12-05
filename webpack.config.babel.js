import path from "path"
import HtmlWebpackPlugin from 'html-webpack-plugin'

const exludedFolders = [
  path.join(__dirname, "node_modules"),
  path.join(__dirname, "vendor")
]

module.exports = {
  entry: "./src/front/index.js",
  output: {
    filename: "assets/scripts/bundle.js",
    path: path.resolve(__dirname, "dist")
  },
  devServer: {
    contentBase: path.join(__dirname, "dist"),
    compress: true,
    port: 9000,
    host: "0.0.0.0",
    open: true,
  },
  module: {
      rules: [
        {
            test: /\.js$/,
            exclude: exludedFolders,
            use: "babel-loader",
        }
      ]
  },
    plugins: [
        new HtmlWebpackPlugin({
            template: "./src/front/index.html",
            filename: "index.html",
        })
    ],
}
