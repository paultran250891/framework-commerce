const HtmlWebpackPlugin = require('html-webpack-plugin')
const path = require('path')
// const HtmlWebpackPlugin = require('html-webpack-plugin')


module.exports = {
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    // Creates `style` nodes from JS strings
                    "style-loader",
                    // Translates CSS into CommonJS
                    "css-loader",
                    // Compiles Sass to CSS
                    "sass-loader",
                ],
            },
            {
                test: /\.html$/i,
                loader: 'html-loader',
            },
        ],
    },
    entry: './src/index.js',
    output: {
        filename: 'show.js',
        // clean: true,
        path: path.resolve(__dirname, 'dist')
    },
    devtool: 'eval-source-map',
    devServer: {
        open: true,
        contentBase: './dist'
    },
    mode: 'development',
    plugins: [

    ]


}