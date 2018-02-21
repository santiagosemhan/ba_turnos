import React, {Component} from 'react';

class Tramite extends Component {

    constructor(props) {
        super(props);
        this.state = { tipoTramite: '', submitEnabled: false, documentos: []};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidUpdate(prevProps, prevState){

      if(this.state.tipoTramite != prevState.tipoTramite){

        for (let tramite of this.props.tramites) {

            if(tramite.id == this.state.tipoTramite){

              this.setState({infoHtml: tramite.texto,submitEnabled:true});
            }
        }
      }
    }


    handleChange(event) {

        let documentos = this.props.documentosList;

        documentos.forEach((item)=>{
          if(item.tramite == event.target.value){
            this.setState({documentos: item.documentos });
          }
        })

        this.setState({tipoTramite: event.target.value});

    }

    handleSubmit(event) {

        let tipoTramite = this.state.tipoTramite;

        let submitUrl = this.props.submitUrl;

        if(tipoTramite != ""){

          window.location.href = submitUrl + "?tipoTramite=" + tipoTramite;

        }

        event.preventDefault();
    }


    getTramites(){

      const tramites = this.props.tramites;

      const optionsTramites = tramites ? tramites.map((tramite,i) => <option key={i} value={tramite.id}> { tramite.descripcion } </option> ) : null;

      return optionsTramites;

    }

    getDocumentos(){

      const documentos = this.state.documentos;

      const  linksDocumentos = documentos ? documentos.map((documento,i) =>  <a key={i} href={documento.link} target="_blank" className="list-group-item">{documento.nombre}</a>
 ) : null;

      return linksDocumentos;

    }

    render() {
        return <div className="container-fluid">
            <form role="form" onSubmit={this.handleSubmit} >
                <div className="row">
                    <div className="col-md-12">
                        <div className="form-group">
                            <label htmlFor="tipo-de-tramite">Tipo de Tramite</label>
                            <select className="form-control" id="tipo-de-tramite" value={this.state.tipoTramite} onChange={this.handleChange}>
                                <option value="" disabled>Escriba el nombre del trámite o despliegue la lista para seleccionar uno</option>
                                { this.getTramites() }
                            </select>
                        </div>
                    </div>
                </div>

                <div className="row">
                  <div className=" col-12 col-md-8 col-sm-8">
                    <div className="panel panel-primary">
                      <div className="panel-heading">
                        <h3 className="panel-title">Información</h3>
                      </div>
                      <div className="panel-body">
                        <div className="" dangerouslySetInnerHTML={{__html: this.state.infoHtml}}></div>
                      </div>
                    </div>
                  </div>
                  <div className="col-12 col-md-4 col-sm-4">
                    <div className="bs-component">
                      <div className="list-group">
                        <a href="#" className="list-group-item active">
                          Documentación
                        </a>
                        { this.getDocumentos() }
                      </div>
                    </div>
                  </div>
                </div>


                <ul className="list-inline pull-right">
                    <li>
                        <a className="btn btn-danger btn-text" role="button" onClick={()=>history.back()} style={{marginRight: '10px'}}>Atrás</a>
                        <button type="submit" className="btn btn-success next-step" disabled={!this.state.submitEnabled}>Continuar</button>
                    </li>
                </ul>

            </form>
        </div>;
    };
}

export default Tramite;
