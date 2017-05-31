import React, { Component } from 'react';
import ListadoTurnos from './components/listado-turnos/ListadoTurnos';
import Clock from './components/clock/Clock';

const io = require('socket.io-client');
const socket = io('10.0.0.7:3380');

import logo from './logo.svg';
import './App.css';

class App extends Component {

  constructor(props) {
    super(props);

    // this.state = { turnos:['C99J','C99J','C99J','C99J']};
    this.state = { turnos:['C99J']};

    this.agregarTurno = this.agregarTurno.bind(this);

    socket.on('sede', (payload) => {
      this.agregarTurno(payload);
    });

  }

  agregarTurno(message){

    this.setState((prevState, props) => {

      let turnos = prevState.turnos;

      console.log(message)

      turnos.push(message.turno);

      return {
        turnos: turnos
      }

    });

  }

  render() {
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

        <div className="col-xs-6">
            <div className="col-xs-6 encabezadoTexto"> Turno </div>
            <div className="col-xs-6 encabezadoTexto"> Box  </div>
            <div class="row">
              <div className="col-xs-12">
                <ListadoTurnos turnos={this.state.turnos}/>
              </div>
            </div>
        </div>

        <div className="col-xs-6">

          
        </div>

        <div className="App-footer">
              <p>Mensaje</p>
        </div>

      </div>
    );
  }
}

export default App;
