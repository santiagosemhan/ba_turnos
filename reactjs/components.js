import React from 'react';
import ReactDOM from 'react-dom';


//Incluir todos los componentes a continuación.
import Tramite from './components/tramite/Tramite';
import Sede from './components/sede/Sede';
import Turno from './components/turno/Turno';
import Link from './components/link/Link';
import OpcionesGenerales from './components/opciones-generales/OpcionesGenerales';


class Components {

  constructor() {

    this.components = new Map();

    var customComponents = [
      {name: 'Tramite', value: Tramite},
      {name: 'Sede', value: Sede},
      {name: 'Turno', value: Turno},
      {name: 'Link', value: Link},
      {name: 'OpcionesGenerales', value: OpcionesGenerales},
    ]

    this.registerComponents(customComponents);

    this.exposeGlobal();
  }

  registerComponents(components){
    for (let cmp of components) {
      this.components.set(cmp.name,cmp.value);
    }
  }

  getComponent(key){
    return this.components.get(key);
  }

  exposeGlobal(){
    window.components  = this;
  }
}

export default Components = new Components();
//require("expose-loader?Tramite!./components/tramite/Tramite");

// window.Tramite = Tramite;
// window.Sede = Sede;
// window.Turno = Turno;
