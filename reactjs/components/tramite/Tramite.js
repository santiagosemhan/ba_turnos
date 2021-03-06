import React, {Component} from 'react';

class Tramite extends Component {

    constructor(props) {
        super(props);
        this.state = { tipoTramite: '', submitEnabled: false};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidUpdate(prevProps, prevState){

      if(this.state.tipoTramite != prevState.tipoTramite){

        // axios.get(this.props.onChangeUrl, {
        //   params: {
        //     tipoTramiteId: this.state.tipoTramite
        //   }
        // })
        // .then(({ data }) => {
        //    this.setState({infoHtml: data.texto});
        //
        //    this.setState({submitEnabled:true});
        // })
        // .catch(function (error) {
        //   console.log("error",error);
        // });
        for (let tramite of this.props.tramites) {

            if(tramite.id == this.state.tipoTramite){

              this.setState({infoHtml: tramite.texto,submitEnabled:true});
            }
        }
      }
    }


    handleChange(event) {

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

                <div className="panel panel-primary">
                    <div className="panel-heading">
                        <h3 className="panel-title">Información</h3>
                    </div>
                    <div className="panel-body" dangerouslySetInnerHTML={{__html: this.state.infoHtml}}></div>
                </div>


                <ul className="list-inline pull-right">
                    <li>
                        <button type="submit" className="btn btn-success next-step" disabled={!this.state.submitEnabled}>Continuar</button>
                    </li>
                </ul>

            </form>
        </div>;
    };
}

export default Tramite;
