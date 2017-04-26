import React, {Component} from 'react';

class Dropdown extends Component {

    constructor(props) {
      super(props);

      this.state = { selected : [] };
    }

    opciones() {
        const opciones = this.props.opciones;

        const listOpciones = opciones.map((opcion,i) => <li key={i}><a href={opcion.url}> { opcion.nombre } </a></li> );

        return ( <ul className="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1"> { listOpciones } < /ul> );
    };

    render() {
        return (
          <div className="dropdown">

          <div className="btn-group">
            <button className="btn btn-success btn-text dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              {this.props.text}
              <span className="caret"></span>
            </button>
            {this.opciones()}
          </div>

        </div>
      );
    };
}

export default Dropdown;
