var env = require('node-env-file');
var app = require('express')();
var server = require('http').Server(app);

var io = require('socket.io')(server);
var redis = require("redis");

env(__dirname + '/.env');

var config = {
    url: process.env.REDIS_DSN
};

var client = redis.createClient(config);

client.on("error", function(err) {
    console.log("Error " + err);
});

client.lrange('cola', 0, -1, function(error, items) {
    if (error) throw error
    items.forEach(function(item) {
        console.log(item);
    })
})


client.on("message", function(channel, message) {
    var mensaje = JSON.parse(message);
    console.log(mensaje)

    if(mensaje){
      io.emit(mensaje.channel,mensaje.data);
    }

    //console.log("sub channel " + channel + ": " + message);
});

client.subscribe("nuevo_turno");
//
// app.get('/', function(req, res) {
//     res.sendfile(__dirname + '/index.html');
// });
//
// io.on('connection', function(socket) {
//     socket.emit('news', {
//         hello: 'world'
//     });
//     socket.on('my other event', function(data) {
//         console.log(data);
//     });
// });


server.listen(3380);
