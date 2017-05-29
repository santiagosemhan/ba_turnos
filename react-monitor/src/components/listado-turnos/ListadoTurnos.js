import React, { Component } from 'react';
import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup';
import Turno from '../../components/turno/Turno';
import "animate.css/animate.min.css";
import './ListadoTurnos.css';


class ListadoTurnos extends Component {


  constructor(props) {
    super(props);

    this.handleClick = this.handleClick.bind(this);
  }

  handleClick(event) {

      //return this.props.handleClick ? this.props.handleClick(event.target.value) : false;

  }

  listar() {
      let turnos = this.props.turnos;

      //let horarioSeleccionado = this.props.horarioSeleccionado;

      let listTurnos = <li><p>Sin turnos</p></li>;

      if(turnos.length !== 0){

        listTurnos = turnos.map((turno,i) =>{

          return (
            <li key={i}>
              <Turno turno={turno} box="4" />
            </li>
          );

        });

      }

      return listTurnos;
  };

  render() {

      //et listadoHorarios = ;

      return (
        <div className="turnos-container">
            <ul className="turnos-list">
              <CSSTransitionGroup
                transitionName="pulse"
                transitionEnter={true}
                transitionEnterTimeout={20500}
                transitionLeave={false}>
                { this.listar() }
              </CSSTransitionGroup>
            </ul>
        </div>
      );
  };
}

export default ListadoTurnos;