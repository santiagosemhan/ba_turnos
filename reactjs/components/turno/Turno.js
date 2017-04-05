import React, {Component} from 'react';
import Calendario from '../calendario/Calendario';
import Horarios from '../horarios/Horarios';
import axios from 'axios';

class Turno extends Component {

    constructor(props) {
      super(props);

      this.state = { horarios: [], horario: '', submitEnabled: false };

      this.handleSubmit = this.handleSubmit.bind(this);

    }

    handleSubmit(event) {

      const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

      var params = new URLSearchParams();

      params.append('tipoTramite', this.props.tipoTramite);
      params.append('sede', this.props.sede);
      params.append('dia',this.state.fecha.dia );
      params.append('mes', this.state.fecha.mes);
      params.append('anio',this.state.fecha.anio);
      params.append('horario',this.state.horario);


      axios.post(this.props.preReservaUrl, params,config)
      .then(({ data }) => {
          window.location.href = this.props.ingresoDatosUrl;
      })
      .catch(function (error) {
        console.log("error",error);
      });

      event.preventDefault();
    }

    onSelect(date){

      let fecha = new Date(date);

      this.setState({
        fecha:{
          dia:  fecha.getDate() ,
          mes:  fecha.getMonth() + 1,
          anio: fecha.getFullYear()
        },
        horario: '',
        submitEnabled: false
      }, this.getHorarios);

    }

    handleClick(value){
        this.setState({ horario: value, submitEnabled: true  });
    }

    getHorarios() {
      axios.get(this.props.getHorariosUrl, {
        params: {
          tipoTramite: this.props.tipoTramite,
          sede: this.props.sede,
          dia: this.state.fecha.dia,
          mes: this.state.fecha.mes,
          anio: this.state.fecha.anio
        }
      })
      .then(({ data }) => {

        this.setState({ horarios: data.horasHabiles });
         //
        //  this.setState({submitEnabled:true});
      })
      .catch(function (error) {
        console.log("error",error);
      });
    }

    render() {
        return <div className="container-fluid">
            <form role="form" onSubmit={this.handleSubmit}>
                <div className="panel panel-primary">
                    <div className="panel-heading">
                        <h3 className="panel-title">Elija el turno</h3>
                    </div>
                    <div className="panel-body">
                        <div className="col-md-6 calendar">
                            <Calendario onSelect={this.onSelect.bind(this)} disabledDates={this.props.diasNoDisponibles}/>
                        </div>
                        <div className="col-md-6">
                            <h4>Horarios</h4>
                            <Horarios horarios={this.state.horarios} handleClick={this.handleClick.bind(this)} horarioSeleccionado={this.state.horario}/>
                        </div>
                    </div>
                </div>

                <ul className="list-inline pull-right">
                    <li>
                        <button disabled={!this.state.submitEnabled} type="submit" className="btn btn-success next-step">Continuar</button>
                    </li>
                </ul>

            </form>
        </div>;
    };
}

export default Turno;
