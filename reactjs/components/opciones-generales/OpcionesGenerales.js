import React, {Component} from 'react';
import Dropdown from '../dropdown/Dropdown';

class OpcionesGenerales extends Component {

    constructor(props) {
      super(props);
    }

    opciones() {
        const opciones = this.props.opciones;

        const listOpciones = opciones.map(
          (opcion,i) =>
              <div key={i} id={opcion.id}>
                  <Dropdown text={opcion.descripcion}  opciones={opcion.acciones}/>
              </div>
        );

        return ( listOpciones );
    };

    render() {
        return (
          <div className="opciones-generales">
           { this.opciones() }
          </div>
      );
    };
}

export default OpcionesGenerales;
