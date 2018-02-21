import React, {Component} from 'react';

class Sede extends Component {

    constructor(props) {
        super(props);


        let initialState = {
          sede: '',
          submitEnabled: false,
          infoHtml: ''
        };

        if (props.sedes.length == 1){
          initialState = {
            sede: props.sedes[0].id,
            submitEnabled: true,
            infoHtml: props.sedes[0].direccion
          };
        }

        this.state = initialState;

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);

    }

    componentDidUpdate(prevProps, prevState){

      if(this.state.sede != prevState.sede){

        for (let sede of this.props.sedes) {

            if(sede.id == this.state.sede){

              this.setState({infoHtml: sede.direccion,submitEnabled:true});
            }
        }
      }
    }

    getSedes(){

      const sedes = this.props.sedes;

      const optionsSedes = sedes ? sedes.map((sede,i) => <option key={i} value={sede.id}> { sede.sede } </option> ) : null;

      return optionsSedes;

    }

    handleChange(event) {

        this.setState({ sede: event.target.value });

    }

    handleSubmit(event) {

        let sede = this.state.sede;

        let submitUrl = this.props.submitUrl;

        if(sede != ""){

          window.location.href = submitUrl + "&sede=" + sede;

        }

        event.preventDefault();
    }

    render() {
        return <div className="container-fluid">
            <form role="form" onSubmit={this.handleSubmit}>
                <div className="row">
                    <div className="col-md-12">
                        <div className="form-group">
                            <label htmlFor="sede">Seleccione una Sede</label>
                            <select className="form-control" id="tipo-de-tramite" value={this.state.sede} onChange={this.handleChange}>
                                  <option value="" disabled>Despliegue la lista para seleccionar una sede</option>
                                  { this.getSedes() }
                            </select>
                        </div>
                    </div>
                </div>

                <div className="panel panel-primary">
                    <div className="panel-heading">
                        <h3 className="panel-title">Dirección</h3>
                    </div>
                    <div className="panel-body" dangerouslySetInnerHTML={{__html: this.state.infoHtml}}></div>
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

export default Sede;
