import React, { Component } from 'react';
import './Turno.css';


class Turno extends Component {


  // constructor(props) {
  //   super(props);
  //
  // }


  render() {

      const el =
      <div className="turno">
        <div className="col-50"> { this.props.turno } </div>
        <div className="col-50"> { this.props.box }  </div>
      </div>;

      return (
          el
      );
  };
}

export default Turno;
