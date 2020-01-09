(function(instance) {
    // My sucky query library
    // v0.1

    var events = [];


    function dickQuery(selector) {
        if (typeof selector !== 'string') {
            return new dickQueryResult(selector);
        }

        return new dickQueryResult(Array.prototype.slice.call(document.querySelectorAll(selector)));
    }

    function findEventObject(element) {
        var i;
        for (i=0; i<events.length; ++i) {
            if (events[i].element === element) {
                return events[i];
            }
        }

        return null;
    }

    function findEventListener(event_type, event_obj) {
        if (event_obj) {
            var i;
            for (i=0; i<event_obj.listeners.length; ++i) {
                if (event_obj.listeners[i].type === event_type) {
                    return event_obj.listeners[i];
                }
            }
        }

        return null;
    }

    function dickQueryResult(elements) {
        if (!Array.isArray(elements)) {
            this.elements = [elements];
        }
        else if (elements.length <= 0) {
            this.elements = [];
        }
        else {
            this.elements = elements;
        }
    }

    dickQueryResult.prototype.find = function(selector) {
        if (this.elements.length > 0) {
            return new dickQueryResult(this.elements[0].querySelectorAll(selector));
        }

        return new dickQueryResult(null);
    };

    dickQueryResult.prototype.first = function() {
        if (this.elements.length > 0) {
            return new dickQueryResult(this.elements[0]);
        }

        return null;
    };

    dickQueryResult.prototype.last = function() {
        if (this.elements.length > 0) {
            return new dickQueryResult(this.elements[this.elements.length - 1]);
        }

        return null;
    };

    dickQueryResult.prototype.get = function(index) {
        if (index === undefined) {
            index = 0;
        }

        if (this.elements.length > index) {
            return this.elements[index];
        }

        return null;
    };

    dickQueryResult.prototype.each = function(callback) {
        var i;
        for (i=0; i<this.elements.length; ++i) {
            callback(this.elements[i], i);
        }
    };

    dickQueryResult.prototype.on = function(event_type, handler) {
        var i;
        for (i=0; i<this.elements.length; ++i) {
            var element = this.elements[i];
            var event_obj = findEventObject(element);
            if (event_obj === null) {
                events.push({
                    element: element,
                    listeners: [{
                        type: event_type,
                        handlers: [handler]
                    }]
                });

                element.addEventListener(event_type, handler);
            }
            else {
                var event_listener = findEventListener(event_type, event_obj);
                if (event_listener.length === 0) {
                    event_obj.listeners.push({
                        type: event_type,
                        handlers: [handler]
                    });

                    element.addEventListener(event_type, handler);
                }
                else {
                    var handler_index = event_listener.handlers.indexOf(handler);
                    if (handler_index === -1) {
                        event_listener.handlers.push(handler);
                        element.addEventListener(event_type, handler);
                    }
                }
            }
        }
    };

    dickQueryResult.prototype.off = function(event_type, handler) {
        var i;
        for (i=0; i<this.elements.length; ++i) {
            var element = this.elements[i];
            var event_listener = findEventListener(event_type, findEventObject(element));
            if (event_listener) {
                if (handler !== undefined) {
                    var handler_index = event_listener.handlers.indexOf(handler);
                    if (handler_index > -1) {
                        element.removeEventListener(event_type, event_listener.handlers[handler_index]);
                        event_listener.handlers.splice(handler_index, 1);
                    }
                }
                else {
                    var j;
                    for (j=0; j<event_listener.handlers.length; ++j) {
                        element.removeEventListener(event_type, event_listener.handlers[j]);
                    }

                    event_listener.handlers = [];
                }
            }
        }
    };

    dickQueryResult.prototype.one = function(event_type, handler) {
        var that = this;
        var new_handler = function(event) {
            handler(event);
            that.off(event_type, new_handler);
        };

        var i;
        for (i=0; i<this.elements.length; ++i) {
            var element = this.elements[i];
            this.on(event_type, new_handler);
        }
    };

    instance.$$ = dickQuery;
}(window));
