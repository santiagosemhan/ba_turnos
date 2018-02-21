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

      let listTurnos = <li><p>Sin turnos</p></li>;

      if(turnos.length !== 0){

        listTurnos = turnos.map((turno,i) =>{

          console.log(turno.key);

          return (
            <li className ="tuno-item" key={turno.key}>
              <Turno turno={turno.turno} box={turno.box} />
            </li>
          );

        });

      }

      return listTurnos;
  };

  render() {

      //et listadoHorarios = ;

      return (

            <ul className="turnos-list">
              <CSSTransitionGroup
                transitionName="pulse"
                transitionEnter={true}
                transitionLeave={false}
                transitionEnterTimeout={20500}
                >
                { this.listar() }
              </CSSTransitionGroup>
            </ul>

      );
  };
}

export default ListadoTurnos;