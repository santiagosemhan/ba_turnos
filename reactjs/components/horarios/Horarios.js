import React, { Component } from 'react';

class Horarios extends Component {


  constructor(props) {
    super(props);

    this.handleClick = this.handleClick.bind(this);
  }

  handleClick(event) {

      return this.props.handleClick ? this.props.handleClick(event.target.value) : false;

  }

  listar() {
      let horarios = this.props.horarios;

      let horarioSeleccionado = this.props.horarioSeleccionado;

      let listHorarios = <li><p>Seleccione una fecha, para obtener el listado de horarios...</p></li>;

      if(horarios.length != 0){

        listHorarios = horarios.map((horario,i) =>{

          let btnClass = horarioSeleccionado == horario ? "btn btn-success btn-sm" : "btn btn-default btn-sm";

          return (
            <li key={i}>
              <button type="button" value={horario} className={btnClass} onClick={this.handleClick}>{horario}</button>
            </li>
          );

        });

      }


      return ( <ul className="horarios-list"> { listHorarios } </ul> );
  };

  render() {

      //et listadoHorarios = ;

      return (
        <div> { this.listar() } </div>
      );
  };
}

export default Horarios;
