/*
 *  Document   : app.js
 *  Author     : pixelcave
 *  Description: Main entry point
 *
 */

// Import required modules
import Template from './modules/template';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// App extends Template
export default class App extends Template {
  /*
   * Auto called when creating a new instance
   *
   */
  constructor() {
    super();
  }
  /*
   *  Here you can override or extend any function you want from Template class
   *  if you would like to change/extend/remove the default functionality.
   *
   *  This way it will be easier for you to update the module files if a new update
   *  is released since all your changes will be in here overriding the original ones.
   *
   *  Let's have a look at the _uiInit() function, the one that runs the first time
   *  we create an instance of Template class or App class which extends it. This function
   *  inits all vital functionality but you can change it to fit your own needs.
   *
   */

  /*
   * EXAMPLE #1 - Removing default functionality by making it empty
   *
   */

  //  _uiInit() {}


  /*
   * EXAMPLE #2 - Extending default functionality with additional code
   *
   */

   _uiInit() {
       super._uiInit();

       this._initLaravelEcho();
   }

   _initLaravelEcho() {
      window.Pusher = Pusher;
      window.Echo = new Echo({
          broadcaster: 'pusher',
          key: import.meta.env.VITE_PUSHER_APP_KEY,
          cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
          encrypted: true
      });

      window.Echo.join(`orders`)
      .here((users) => {
          console.log(users);
      })
      .joining((user) => {
          console.log(user.name);
      })
      .leaving((user) => {
          console.log(user.name);
      })
      .listen('YourEventName', (e) => {
          console.log(e);
      });

      window.Echo.channel('orders')
        .listen('OrderStepUpdated', (e) => {
            console.log(e);
        });
   }

  /*
   * EXAMPLE #3 - Replacing default functionality by writing your own code
   *
   */

  //  _uiInit() {
       // Your own JS code without ever calling the original function's code
  //  }
}

// Create a new instance of App
window.Codebase = new App();
