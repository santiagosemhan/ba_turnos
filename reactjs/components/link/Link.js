import React, {Component} from 'react';

class Link extends Component {

    constructor(props) {
      super(props);

      this.state = { selected : [] };
    }


    render() {
        return (
          <div className="dropdown">

          <div className="btn-group">
            <a href={this.props.link} className="btn btn-success btn-text" role="button">{this.props.text}</a>
          </div>

        </div>
      );
    };
}

export default Link;
