module.exports = {
    entry: [
        './components.js'
    ],
    output: {
        path: '../web/dist/',
        publicPath: '/',
        filename: 'components.js'
    },
    module: {
        loaders: [{
            test: /\.jsx?$/,
            exclude: /node_modules/,
            loader: 'babel-loader'
        },
        {
          test: require.resolve("react"),
          loader: "expose-loader?React"
        },
        {
          test: require.resolve("react-dom"),
          loader: "expose-loader?ReactDOM"
        }
      ]
    },
    resolve: {
        extensions: ['*', '.js', '.jsx']
    },
    devServer: {
        contentBase: './dist'
    }
};
