'use strict';

/**
 * Boilerplate for simple javascript "modular" approach
 * Makes shit tidy and nicer to look at and easily accesible from elsewhere
 * 
 * Author: Jan Dočekal (jan.docekal@bold-interactive.com)
 */

var NAME = NAME || (function () {
    var self; // this, reference to the global scope

    /** Scope for private functions 
     *  (anything sensitive, not accesible through the NAME scope)
    */
    var _calculate = function(){};

    /** Scope for public functions */
    return {
        // Init function for starting up anything
        init: function(params){
            self = this; // assign this

            // Call public functions
            this.fireEvents();
        },
        // Public function
        fireEvents: function(){
            _calculate(); // Call private function
        }
    }
}());

NAME.init(); // init right away