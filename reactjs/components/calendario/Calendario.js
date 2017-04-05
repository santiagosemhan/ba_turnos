import React, { Component } from 'react';
import { render } from 'react-dom';
import InfiniteCalendar from 'react-infinite-calendar';

class Calendario extends Component {

  constructor(props) {
    super(props);

    let today = new Date();

    this.state = {
      lastWeek: new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7),
      sixMonth: new Date(today.getFullYear(), today.getMonth() + 6, today.getDate()),
      today: today
    };

    this.handleOnSelect = this.handleOnSelect.bind(this);


  }

  handleOnSelect(date){
    return this.props.onSelect ? this.props.onSelect(date) : false;
  }

  render() {
    return (

      <InfiniteCalendar
          height={250}
          selected={null}
          onSelect={this.handleOnSelect}
          locale={{
              locale: require('date-fns/locale/es'),
              headerFormat: 'dddd, D MMM',
              weekdays: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
              blank: 'Seleccione una fecha...',
              todayLabel: {
               long: 'Hoy',
               short: 'Hoy'
              }
          }}
          disabledDates={this.props.disabledDates ? this.props.disabledDates : []}
          disabledDays={[0]}
          minDate={this.state.today}
          maxDate={this.state.sixMonth}
          width='100%'
        />
    );
  }
}

export default Calendario;
