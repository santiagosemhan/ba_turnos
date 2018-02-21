import React, {Component} from 'react';
import Link from '../link/Link';

class OpcionesGenerales extends Component {

    constructor(props) {
      super(props);
    }

    opciones() {
        const opciones = this.props.opciones;

        const listOpciones = opciones.map(
          (opcion,i) =>
              <div key={i} id={opcion.id}>
                  <Link text={opcion.descripcion}  link={opcion.link}/>
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
