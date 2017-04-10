const path = require('path');

// path.resolve(__dirname, 'dist'),

var distPath = path.resolve(__dirname.replace('reactjs', 'web'), 'dist');

module.exports = {
    entry: [
        './components.js'
    ],
    output: {
        path: distPath,
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
