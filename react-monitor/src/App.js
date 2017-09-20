import React, { Component } from 'react';
import ListadoTurnos from './components/listado-turnos/ListadoTurnos';
import Clock from './components/clock/Clock';

const io = require('socket.io-client');

const server_ip = process.env.REACT_APP_SERVER_IP;
const server_port = process.env.REACT_APP_SERVER_PORT;

const socket = io(`${server_ip}:${server_port}`);


import logo from './logo.svg';
import './App.css';

class App extends Component {

  constructor(props) {
    super(props);

    // this.state = { turnos:[{ turno: 'C99J',box:4 }]};
    this.state = { turnos:[]};

    this.agregarTurno = this.agregarTurno.bind(this);

    socket.on(window.LISTEN_CHANNEL, (payload) => {

      payload.key = Math.floor(Math.random()*100000);

      this.agregarTurno(payload);
    });

  }

  agregarTurno(payload){

    this.setState((prevState, props) => {

      let turnos = prevState.turnos;

      turnos.unshift(payload);

      return {
        turnos: turnos
      }

    });

  }

  render() {

    let encabezado = '';

    if(this.state.turnos.length !== 0) {
      encabezado = (
        <div>
          <div className="col-xs-6 encabezadoTexto"> Turno </div>
          <div className="col-xs-6 encabezadoTexto"> Box  </div>
        </div>
      );
    }


    return (
      <div className="App">
        <div className="App-header">
          <img src={logo} className="App-logo" alt="logo" />
          <h2 className="App-title">Espere su turno, en breve será atendido</h2>
          <div className="App-time"><Clock></Clock></div>
        </div>
        {/*
          <p className="App-intro">
            To get started, edit <code>src/App.js</code> and save to reload.
          </p>
        */}

        <div className="col-xs-12">
            {encabezado}
            <div className="row">
              <div className="col-xs-12">
                <ListadoTurnos turnos={this.state.turnos}/>
              </div>
            </div>
        </div>

        /*<div className="col-xs-6">

        </div>*/

        <div className="App-footer">
              <p>Mensaje</p>
        </div>

      </div>
    );
  }
}

export default App;
