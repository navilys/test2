/**
 * @class Base
 * Base Class
 *
 *
 * @constructor
 * Create a new instance of the class
 * @param {Object} options
 */
var Base = function (options) {
    var defaults = {
        id : (options && options.id) || jCore.Utils.generateUniqueId()
    };
    $.extend(true, defaults, options);
    /**
     * Unique Identifier
     * @type {String}
     */
    this.id = defaults.id;
};

/**
 * Sets the id property
 * @return {String}
 */
Base.prototype.setId = function (value) {
    this.id = value;
    return this;
};

/**
 * Object Type
 * @type {String}
 * @private
 */
Base.prototype.type = "Core";

/**
 * Object Family
 * @type {String}
 * @private
 */
Base.prototype.family = "Core";

/**
 * Returns the object type
 * @return {String}
 */
Base.prototype.getType = function () {
    return this.type;
};

/**
 * Returns the object family
 * @return {String}
 */
Base.prototype.getFamily = function () {
    return this.family;
};

/**
 * Destroys the fields ob the object
 */
Base.prototype.dispose = function () {
    var key;
    for (key in this) {
        this[key] = null;
    }
};
/**
 * @class Modal
 * Handle modal divs
 * @extend Base
 *
 * @constructor
 * Creates a new instance of the object
 * @param {Object} options
 */
var Modal = function (options) {
    Base.call(this, options);
    /**
     * Defines the state of the modal object
     * @type {Boolean}
     */
    this.visible = null;
    /**
     * Defines the property of loading
     * @type {Boolean}
     */
    this.loaded = false;
    /**
     * Defines the HTML Element Pointer
     * @type {HTMLElement}
     */
    this.html = null;
    /**
     * Defines the click handler
     * @type {Function}
     */
    this.clickHander = null;
    Modal.prototype.initObject.call(this, options);
};

Modal.prototype = new Base();

/**
 * Defines the object's type
 * @type {String}
 */
Modal.prototype.type = "Modal";

/**
 * Initializes the object with default values
 * @param {Object} options
 */
Modal.prototype.initObject = function (options) {
    var defaults = {
        visible: false,
        clickHander: function () {}
    };
    $.extend(true, defaults, options);
    this.setVisible(defaults.visible)
        .setClickHandler(defaults.clickHander);
};

/**
 * Sets the visible property
 * @param {Boolean} value
 * @return {*}
 */
Modal.prototype.setVisible = function (value) {
    this.visible = value;
    return this;
};

/**
 * Sets the click handler
 * @param {Function} fn
 * @return {*}
 */
Modal.prototype.setClickHandler = function (fn) {
    this.clickHander = fn;
    return this;
};

/**
 * Shows the modal object
 */
Modal.prototype.show = function () {
    var modalDiv;
    if (!this.html) {
        modalDiv = document.createElement('div');
        modalDiv.className = 'adam-modal';
        modalDiv.id = this.id;
        this.html = modalDiv;
    }
    document.body.appendChild(this.html);
    this.setVisible(true);
    if (!this.loaded) {
        this.attachListeners();
        this.loaded = true;
    }
};

/**
 * Hide the modal object
 */
Modal.prototype.hide = function () {
    if (this.visible) {
        document.body.removeChild(this.html);
        this.setVisible(false);
    }
};

/**
 * Initializes the modal listeners
 */
Modal.prototype.attachListeners = function () {
    var self = this;
    if (this.html) {
        $(this.html)
            .click(function (e) {
                e.stopPropagation();
                if (self.clickHander) {
                    self.clickHander();
                }
            })
            .mouseover(function (e) {
                e.stopPropagation();
            })
            .mouseout(function (e) {
                e.stopPropagation();
            })
            .mouseup(function (e) {
                e.stopPropagation();
            })
            .mousedown(function (e) {
                e.stopPropagation();
            });
    }
};

/**
 * @class Proxy
 * Handles the proxy connections
 * @extend Base
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 */
var Proxy = function (options) {
    Base.call(this, options);
    /**
     * Defines the URL to connect
     * @type {String}
     */
    this.url = null;
    this.callback = null;
    Proxy.prototype.initObject.call(this, options);
};
Proxy.prototype = new Base();

/**
 * Defines the object's type
 * @type {String}
 */
Proxy.prototype.type = 'Proxy';

/**
 * Defines the object's family
 * @type {String}
 */
Proxy.prototype.family = 'Proxy';

/**
 * Initializes the object with default values
 * @param {Object} options
 */
Proxy.prototype.initObject = function (options) {
    var defaults = {
        url: null,
        callback: null
    };
    $.extend(true, defaults, options);
    this.setUrl(defaults.url)
        .setCallback(defaults.callback);
};

/**
 * Sets the URL property
 * @param {String} url
 * @return {*}
 */
Proxy.prototype.setUrl = function (url) {
    this.url = url;
    return this;
};

Proxy.prototype.setCallback = function (callback) {
    this.callback = callback;
    return this;
};

/**
 * Obtains the data
 */
Proxy.prototype.getData = function () {
    console.log('Getting Data from: ' + this.url);
};

/**
 * Sends the data
 * @param {Object} data
 * @param {Object} [callback]
 */
Proxy.prototype.sendData = function (data, callback) {
    console.log('Sending Data to: ' + this.url, data);
};

/**
 * @class Element
 * Base class to handle HTML Divs
 * @extend Base
 *
 *
 * @constructor
 * Create a new instace of the class 'Element'
 * @param {Object} options
 */
var Element = function (options) {
    Base.call(this, options);
    /**
     * Absolute X position of the HTML Element
     * @type {Number}
     */
    this.x = null;
    /**
     * Absolute Y position of the HTML Element
     * @type {Number}
     */
    this.y = null;
    /**
     * Width dimension of the HTML Element
     * @type {Number}
     */
    this.width = null;
    /**
     * Height dimension of the HTML Element
     * @type {Number}
     */
    this.height = null;
    /**
     * Pointer to the HTMLElement object
     * @type {HTMLElement}
     */
    this.html = null;
    /**
     * Intance of the jCore.Style object to handle style tags
     * @type {Object}
     */
    this.style = null;
    /**
     * Defines if the HTML element is visible
     * @type {Boolean}
     */
    this.visible = null;
    /**
     * Defines the value of the zIndex for the HTML Element
     * @type {Number}
     */
    this.zOrder = null;

    Element.prototype.initObject.call(this, options);
};
Element.prototype = new Base();

/**
 * Defines the object type
 * @type {String}
 * @private
 */
Element.prototype.type = "Base";
/**
 * Defines the object family
 * @type {String}
 * @private
 */
Element.prototype.family = "Base";

/**
 * Initialize the object with the default values
 * @param {Object} options
 * @private
 */
Element.prototype.initObject = function (options) {
    var defaults = {
        id : (options && options.id) || jCore.Utils.generateUniqueId(),
        style : {
            cssProperties: {},
            cssClasses: []
        },
        width : 0,
        height : 0,
        x : 0,
        y : 0,
        zOrder : 1,
        visible : true
    };
    $.extend(true, defaults, options);
    this.setId(defaults.id)
        .setStyle(new jCore.Style({
            belongsTo: this,
            cssProperties: defaults.style.cssProperties,
            cssClasses: defaults.style.cssClasses
        }))
        .setDimension(defaults.width, defaults.height)
        .setPosition(defaults.x, defaults.y)
        .setZOrder(defaults.zOrder)
        .setVisible(defaults.visible);
};

/**
* Sets the id property
* @param {String} newID
* @return {*}
*/
Element.prototype.setId = function (newID) {
    this.id = newID;
    if (this.html) {
        this.html.id = this.id;
    }
    return this;
};
/**
 * Sets the X property
 * @param {Number} x
 * @return {*}
 */
Element.prototype.setX = function (x) {
    if (typeof x === 'number') {
        this.x = x;
        if (this.html) {
            this.style.addProperties({left: this.x});
        }
    } else {
        throw new Error('setX: x param is not a number');
    }
    return this;
};

/**
 * Sets the Y property
 * @param {Number} y
 * @return {*}
 */
Element.prototype.setY = function (y) {
    if (typeof y === 'number') {
        this.y = y;
        if (this.html) {
            this.style.addProperties({top: this.y});
        }
    } else {
        throw new Error('setY: y param is not a number');
    }
    return this;
};

/**
 * Sets the width property
 * @param {Number} w
 * @return {*}
 */
Element.prototype.setWidth = function (w) {
    if (typeof w === 'number') {
        this.width = w;
        if (this.html) {
            this.style.addProperties({width: this.width});
        }
    } else {
        throw new Error('setWidth: w is not a number');
    }
    return this;
};

/**
 * Sets the height property
 * @param {Number} h
 * @return {*}
 */
Element.prototype.setHeight = function (h) {
    if (typeof h === 'number') {
        this.height = h;
        if (this.html) {
            this.style.addProperties({height: this.height});
        }
    } else {
        throw new Error('setHeight: h is not a number');
    }
    return this;
};

/**
 * Sets the position of the HTML Element
 * @param {Number} x
 * @param {Number} y
 * @return {*}
 */
Element.prototype.setPosition = function (x, y) {
    this.setX(x);
    this.setY(y);
    return this;
};

/**
 * Sets the dimension of the HTML Element
 * @param {Number} w
 * @param {Number} h
 * @return {*}
 */
Element.prototype.setDimension = function (w, h) {
    this.setWidth(w);
    this.setHeight(h);
    return this;
};

/**
 * Sets the xOrder property
 * @param {Number} z
 * @return {*}
 */
Element.prototype.setZOrder = function (z) {
    if (typeof z === 'number' && z > 0) {
        this.zOrder = z;
        if (this.html) {
            this.style.addProperties({zIndex: this.zOrder});
        }
    }
    return this;
};

/**
 * Sets the visible property
 * @param {Boolean} value
 * @return {*}
 */
Element.prototype.setVisible = function (value) {
    if (_.isBoolean(value)) {
        this.visible = value;
        if (this.html) {
            if (value) {
                this.style.addProperties({display: "inline"});
            } else {
                this.style.addProperties({display: "none"});
            }
        }
    }
    return this;
};

/**
 * Sets the style object
 * @param {Object} style Instance of jCore.Style
 * @return {*}
 */
Element.prototype.setStyle = function (style) {
    if (style instanceof jCore.Style) {
        this.style = style;
    }
    return this;
};

/**
 * Creates a new HTML Element
 * @param {String} type
 * @return {HTMLElement}
 */
Element.prototype.createHTMLElement = function (type) {
    return document.createElement(type);
};

/**
 * Creates the hmtl object
 * @return {HTMLElement}
 */
Element.prototype.createHTML = function () {
    if (!this.html) {
        this.html = this.createHTMLElement('div');
        this.html.id = this.id;

        this.style.applyStyle();

        this.style.addProperties({
            position: "absolute",
            left: this.x,
            top: this.y,
            width: this.width,
            height: this.height,
            zIndex: this.zOrder
        });
    }
    return this.html;
};

/**
 * Defines the functionality to paint the HTML element
 * @abstract
 */
Element.prototype.paint = function () {
};

/**
 * Returns the html pointer
 * @return {HTMLElement}
 */
Element.prototype.getHTML = function () {
    if (!this.html) {
        this.createHTML();
    }
    return this.html;
};

/**
 * Calculates the text width
 * @param {String} text
 * @param {String} [font]
 * @return {*}
 */
Element.prototype.calculateWidth = function (text, font) {
    //TODO Improve the div creation (maybe we can use a singleton for this)
    var f = font || '12px arial',
        o = $('<div>' + text + '</div>')
            .css({'position': 'absolute', 'float': 'left', 'white-space': 'nowrap', 'visibility': 'hidden', 'font': f})
            .appendTo($('body')),
        w = o.width();

    o.remove();

    return w;
};
/**
 * @class Container
 * Handle Containers
 * @extend Element
 *
 * @constructor
 * Create a new instance of the container class
 * @param {Object} options
 */
var Container = function (options) {
    Element.call(this, options);
    /**
     * Defines the items part of the container
     * @type {Array}
     */
    this.items = [];
    /**
     * Defines the pointer to the body HTML Element
     * @type {HTMLElement}
     */
    this.body = null;

    Container.prototype.initObject.call(this, options);
};

Container.prototype = new Element();
/**
 * Defines the object's type
 * @type {String}
 */
Container.prototype.type = "Container";
/**
 * Defines the object's family
 * @type {String}
 */
Container.prototype.family = "Container";

/**
 * Initialize the object with the default values
 */
Container.prototype.initObject = function (options) {
    var defaults = {
        items: [],
        body: null
    };
    $.extend(true, defaults, options);
    this.setItems(defaults.items)
        .setBody(defaults.body);
};

/**
 * Sets the items property
 * @param {Array}
 */
Container.prototype.setItems = function (items) {
    this.items = items;
    return this;
};

/**
 * Sets the body HTML Element
 * @param {HTMLElement} html
 */
Container.prototype.setBody = function (html) {
    this.body = html;
    return this;
};

/**
 * Returns the body HTML Element
 */
Container.prototype.getBody = function () {
    return this.body;
};

/**
 * Creates the HTML Element
 */
Container.prototype.createHTML = function () {
    var body;
    Element.prototype.createHTML.call(this);
    body = this.createHTMLElement('div');
    body.className = 'j-container';
    this.html.appendChild(body);
    this.body = body;
    return this.html;
};

/**
 * @class Window
 * Handle window objects
 * @extend Container
 *
 * @constructor
 * Creates a new instance of the window's class
 * @param {Object} options
 */
var Window = function (options) {
    Container.call(this, options);
    /**
     * Defines the window's title
     * @type {String}
     */
    this.title = null;
    /**
     * Defines the window's modal property
     * @type {Boolean}
     */
    this.modal = null;
    /**
     * Defines the Modal Object to handle modal windows
     * @type {Modal}
     */
    this.modalObject = null;

    /**
     * Defines the Close Button HTML Element
     * @type {HTMLElement}
     */
    this.closeButtonObject = null;
    /**
     * Defines the title label HTML Element
     * @type {HTMLElement}
     */
    this.titleLabelObject = null;
    /**
     * Records the loading state of the window
     * @type {Boolean}
     * @private
     */
    this.loaded = false;

    /**
     * Defines the DestroyOnHide property
     * @type {Boolean}
     */
    this.destroyOnHide = null;

    /**
     * Defines the modal handler HTML Element pointer
     * @type {HTMLElement}
     */
    this.modalHandler = null;

    /**
     * Defines the close button property
     * @type {Boolean}
     */
    this.closeButton = null;
    /**
     * Defines the window's panel object
     * @type {Panel}
     */
    this.panel = null;

    /**
     * Defines the HTML Element to apply the modal mask
     * @type {HTMLElement}
     * @private
     */
    this.modalContainer = null;
    Window.prototype.initObject.call(this, options);
};

Window.prototype = new Container();

/**
 * Defines the object's type
 * @type {String}
 */
Window.prototype.type = "Window";

/**
 * Initialize the object with the default values
 */
Window.prototype.initObject = function (options) {
    var defaults = {
        title: 'No Title',
        modal: true,
        modalHandler: null,
        destroyOnHide: false,
        closeButton: true
    };
    $.extend(true, defaults, options);
    this.setTitle(defaults.title)
        .setModalHandler(defaults.modalHandler)
        .setModal(defaults.modal)
        .setVisible(false)
        .setCloseButton(defaults.closeButton)
        .setDestroyOnHide(defaults.destroyOnHide);

    this.modalContainer = $('body');
};

/**
 * Sets the window's title
 * @param {String} text
 */
Window.prototype.setTitle = function (text) {
    this.title = text;
    if (this.titleLabelObject) {
        this.titleLabelObject.innerHTML = text;
    }
    return this;
};

/**
 * Sets the Modal handler function
 * @param {Function} fn
 * @return {*}
 */
Window.prototype.setModalHandler = function (fn) {
    this.modalHandler = fn;
    return this;
};

/**
 * Sets the window's modal property
 * @param {Boolean} value
 */
Window.prototype.setModal = function (value) {
    if (value) {
        this.modalObject = new Modal({
            clickHandler: this.modalHandler
        });
    } else {
        this.modalObject = null;
    }
    this.modal = value;
    return this;
};

/**
 * Sets the destroy on hide property
 * @param {Boolean} value
 * @return {*}
 */
Window.prototype.setDestroyOnHide = function (value) {
    this.destroyOnHide = value;
    return this;
};

/**
 * Sets the close Button property
 * @param {Boolean} value
 * @return {*}
 */
Window.prototype.setCloseButton = function (value) {
    this.closeButton = value;
    return this;
};

/**
 * Adds a panel to the container window
 * @param {Panel} p
 */
Window.prototype.addPanel = function (p) {
    if (p.family && p.family === 'Panel') {
        this.panel = p;
        if (this.loaded) {
            this.body.appendChild(this.panel.getHTML());
        }
        p.setParent(this);
    }
    return this;
};

/**
 * Creates the HTML Element fot the object
 * @return {*}
 */
Window.prototype.createHTML = function () {
    var marginProps, closeBtn, titleLabel;
    Container.prototype.createHTML.call(this);
    marginProps = '-' + parseInt(this.height / 2, 10) + 'px 0 0 -' + parseInt(this.width / 2, 10) + 'px';
    this.style.addClasses(['adam-window']);
    this.style.addProperties({
        'z-index': 900,
        'left': '50%',
        'top': '50%',
        'margin': marginProps
    });

    titleLabel = this.createHTMLElement('label');
    titleLabel.className = 'adam-window-title';
    titleLabel.innerHTML = this.title;

    if (this.closeButton) {
        closeBtn = this.createHTMLElement('span');
        closeBtn.className = 'adam-window-close';
        this.html.insertBefore(closeBtn, this.body);
        this.closeButtonObject = closeBtn;
        this.html.insertBefore(titleLabel, this.closeButtonObject);
    } else {
        this.html.insertBefore(titleLabel, this.body);
    }
    this.titleLabelObject = titleLabel;
    if (this.body) {
        this.body.className = 'adam-window-body';
        this.body.style.height = (this.height - 38) + 'px';
        //this.body.innerHTML = 'test';
    }
    return this.html;
};

/**
 * Shows the window
 */
Window.prototype.show = function () {
    if (!this.loaded) {
        this.load();
    }
    if (this.modal) {
        this.modalObject.show();
    }
    if (this.panel) {
        this.body.appendChild(this.panel.getHTML());
        this.panel.load();
    }
    document.body.appendChild(this.html);
    this.setVisible(true);
};

/**
 * Opens/Creates the windows object
 * @private
 */
Window.prototype.load = function () {
    if (!this.html) {
        this.createHTML();
        this.attachListeners();
        this.loaded = true;
    }
};


/**
 * Close the window and destroy the object
 */
Window.prototype.close = function () {
    if (this.visible) {
        this.hide();
    }
    if (this.dispose) {
        this.dispose();
    }
};

/**
 * Hides the window
 * @param {Boolean} [destroy]
 */
Window.prototype.hide = function (destroy) {
    if (this.modal) {
        this.modalObject.hide();
    }
    document.body.removeChild(this.html);
    this.setVisible(false);
    if (destroy || this.destroyOnHide) {
        this.close();
    }
};

/**
 * Sets the window listeners
 */
Window.prototype.attachListeners = function () {
    var self = this;
    $(this.html).draggable({
        cursor: "move",
        scroll: false,
        containment: "document"
    });
    if (this.closeButton && this.closeButtonObject) {
        $(this.closeButtonObject).click(function (e) {
            e.stopPropagation();
            self.hide();
        });
    }
};

/**
 * @class Action
 * Handle Actions
 * @extend Base
 *
 *
 * @constructor
 * Create a new instance of the class
 * @param {Object} options
 */
var Action = function (options) {
    Base.call(this, options);
    /**
     * Defines the text of the action
     * @type {String}
     */
    this.text = null;
    /**
     * Defines if the actions is enabled
     * @type {Boolean}
     */
    this.disabled = null;
    /**
     * Defines if the action should be showed
     * @type {Boolean}
     */
    this.hidden = null;
    /**
     * Defines the handler of the action
     * @type {Function}
     */
    this.handler = null;
    /**
     * Defines a style for the action
     * @type {String}
     */
    this.cssStyle = null;
    /**
     * Defines the object associated to this action
     * @type {Object}
     */
    this.related = null;
    Action.prototype.initObject.call(this, options);
};
Action.prototype = new Base();
/**
 * Defines the object's type
 * @type {String}
 */
Action.prototype.type = "Action";
/**
 * Defines the object's family
 * @type {String}
 */
Action.prototype.family = "Action";

/**
 * Initialize the object with default values
 * @param {Object} options
 */
Action.prototype.initObject = function (options) {
    var defaults = {
        text: null,
        cssStyle: null,
        disabled: false,
        hidden: false,
        handler: function () {

        },
        related: null
    };
    $.extend(true, defaults, options);
    this.setText(defaults.text)
        .setCssClass(defaults.cssStyle)
        .setDisabled(defaults.disabled)
        .setHidden(defaults.hidden)
        .setHandler(defaults.handler)
        .setRelated(defaults.related);
};

/**
 * Sets the action text property
 * @param text
 * @return {*}
 */
Action.prototype.setText = function (text) {
    this.text = text;
    return this;
};

/**
 * Sets the action's handler
 * @param {Function} fn
 * @return {*}
 */
Action.prototype.setHandler = function (fn) {
    if (_.isFunction(fn)) {
        this.handler = fn;
    }
    return this;
};

/**
 * Sets the CSS classes
 * @param {String} css
 * @return {*}
 */
Action.prototype.setCssClass = function (css) {
    this.cssStyle = css;
    return this;
};

/**
 * Sets the enabled property
 * @param {Boolean} value
 * @return {*}
 */
Action.prototype.setDisabled = function (value) {
    if (_.isBoolean(value)) {
        this.disabled = value;
        if (this.related) {
            if (_.isFunction(this.related.paint)) {
                this.related.paint();
            }
        }
    }
    return this;
};


/**
 * Sets the hidden property
 * @param {Boolean} value
 * @return {*}
 */
Action.prototype.setHidden = function (value) {
    if (_.isBoolean(value)) {
        this.hidden = value;
        if (this.related) {
            if (_.isFunction(this.related.paint)) {
                this.related.paint();
            }
        }
    }
    return this;
};

/**
 * Sets the action's associated object
 * @param {Object} relation
 * @return {*}
 */
Action.prototype.setRelated = function (relation) {
    this.related = relation;
    return this;
};

/**
 * Turns on the action
 */
Action.prototype.enable = function () {
    this.setDisabled(false);
};

/**
 * Turns off the action
 */
Action.prototype.disable = function () {
    this.setDisabled(true);
};

/**
 * Shows the action
 */
Action.prototype.hide = function () {
    this.setHidden(true);
};

/**
 * Hides the action
 */
Action.prototype.show = function () {
    this.setHidden(false);
};

/**
 * Returns the enabled property
 * @return {Boolean}
 */
Action.prototype.isEnabled = function () {
    return !this.disabled;
};

/**
 * Returns the hidden property
 * @return {Boolean}
 */
Action.prototype.isHidden = function () {
    return this.hidden;
};

/**
 * Defines the action validation
 * @type {Boolean}
 */
Action.prototype.isAction = true;


/**
 * @class Menu
 * Handles the Menues
 * @extend Container
 *
 * @constructor
 * Creates a new instance of the object
 * @param {Object} options
 */
var Menu = function (options) {
    Container.call(this, options);
    /**
     * Items Arrays
     * @type {Array}
     */
    this.items = [];
    /**
     * Defines the menu name
     * @type {String}
     */
    this.name = null;
    /**
     * Defines the menu's state
     * @type {String}
     */
    this.state = null;
    /**
     * Defines the menu's tooltip
     * @type {String}
     */
    this.toolTip = null;
    /**
     * Defines the parent object
     * @type {Object}
     */
    this.parent = null;

    this.canvas = null;

    this.visible = null;

    this.currentItem = null;

    this.loaded = false;

    Menu.prototype.initObject.call(this, options);
};
Menu.prototype = new Container();

/**
 * Defines the object's type
 * @type {String}
 */
Menu.prototype.type = "Menu";

/**
 * Defines the object's family
 * @type {String}
 */
Menu.prototype.family = "Menu";

/**
 * Initialize the object with default values
 * @param {Object} options
 */
Menu.prototype.initObject = function (options) {
    var defaults = {
        parent: null,
        items: [],
        name: null,
        state: null,
        toolTip: null,
        parentMenu: null,
        canvas: null,
        visible: false,
        currentItem: null
    };
    $.extend(true, defaults, options);
    this.setCanvas(defaults.canvas)
        .setItems(defaults.items)
        .setName(defaults.name)
        .setState(defaults.state)
        .setParent(defaults.parent)
        //.setParentMenu(defaults.parentMenu)
        .setToolTip(defaults.toolTip)
        .setVisible(defaults.visible)
        .setCurrentItem(defaults.currentItem);
};

/**
 * Sets the items of the menu
 * @param {Array} items
 * @return {*}
 */
Menu.prototype.setItems = function (items) {
    var item,
        i;
    for (i = 0; i < items.length; i += 1) {
        switch (items[i].jtype) {
        case 'separator':
            item = new SeparatorItem(items[i], this);
            break;
        case 'checkbox':
            item = new CheckboxItem(items[i], this);
            break;
        default:
            item = new MenuItem(items[i], this);
        }
        this.items.push(item);
    }
    this.calculateDimension();
    return this;
};

/**
 * Sets the name property
 * @param {String} text
 * @return {*}
 */
Menu.prototype.setName = function (text) {
    this.name = text;
    return this;
};

/**
 * Sets the state property
 * @param {String} state
 * @return {*}
 */
Menu.prototype.setState = function (state) {
    this.state = state;
    return this;
};

/**
 * Sets the tool tip property
 * @param {String} text
 * @return {*}
 */
Menu.prototype.setToolTip = function (text) {
    this.toolTip = text;
    return this;
};

/**
 * Sets the parent's menu property
 * @param {Object} obj
 * @return {*}
 */
Menu.prototype.setParent = function (obj) {
    if (typeof obj === 'object') {
        this.parent = obj;
    }
    return this;
};

// Menu.prototype.setParentMenu = function (obj) {
//     if (typeof obj === 'object') {
//         this.parentMenu = obj;
//     }
//     return this;
// };

Menu.prototype.setCanvas = function (obj) {
    this.canvas = obj;
    return this;
};

Menu.prototype.setVisible = function (value) {
    this.visible = value;
    return this;
};

Menu.prototype.setCurrentItem = function (item) {
    if (this.currentItem && this.currentItem.hasMenuActive) {
        this.currentItem.setFocused(false);
        this.currentItem.setHasMenuActive(false);
        this.currentItem.setActiveItem(false);
        this.currentItem.setActiveMenu(false);
    }
    this.currentItem = item;
    return this;
};

Menu.prototype.createHTML = function () {
    Element.prototype.createHTML.call(this);
    this.style.addClasses(['adam-menu']);
    this.setZOrder(1000);
    this.generateMenu();
    return this.html;
};

Menu.prototype.generateMenu = function () {
    var i, ul;
    ul = this.createHTMLElement('ul');
    ul.className = 'adam-list';
    for (i = 0; i < this.items.length; i += 1) {
        ul.appendChild(this.items[i].getHTML());
    }
    this.html.appendChild(ul);
    return this;
};

Menu.prototype.paint = function () {

};

/**
 * Sets the menu's position and show the menu
 * @param {Number} x
 * @param {Number} y
 */
Menu.prototype.show = function (x, y) {
    if (this.canvas) {
        if (!this.loaded) {
            this.setPosition(x, y);
            this.calculateItemCoords();
        }
        this.canvas.html.appendChild(this.getHTML());
        if (!this.loaded) {
            this.attachListeners();
            this.loaded = true;
        }
        this.setVisible(true);
        if (this.parent.type === 'AdamCanvas') {
            this.parent.setCurrentMenu(this);
        } else if (this.parent.type !== 'MenuItem') {
            this.parent.canvas.setCurrentMenu(this);
        }
    }
};

Menu.prototype.calculateDimension = function () {
    var c, h, i, len, label, w;
    h = 4;
    c = 0;
    for (i = 0; i < this.items.length; i += 1) {
        switch (this.items[i].getType()) {
        case 'MenuItem':
        case 'CheckboxItem':
            h += 24;
            break;
        case 'SeparatorItem':
            h += 4;
            break;
        }
        label = this.items[i].label || "";
        if (label !== "") {
            len = this.calculateWidth(label);
            if (len > c) {
                c = len;
            }
        }
    }

    w = 21 + 48 + 2 + c;
    this.setDimension(w, h);
    return this;
};

Menu.prototype.attachListeners = function () {
    var i;
    for (i = 0; i < this.items.length; i += 1) {
        this.items[i].attachListeners();
    }
    return this;
};

Menu.prototype.hide = function () {
    var i;
    if (this.canvas && this.visible) {
        for (i = 0; i < this.items.length; i += 1) {
            if (this.items[i].menu) {
                this.items[i].menu.hide();
            }
        }
        this.canvas.html.removeChild(this.getHTML());
        this.setVisible(false);
        if (this.parent.type === "AdamCanvas") {
            this.parent.setCurrentMenu(null);
        }
    }
};

Menu.prototype.calculateItemCoords = function () {
    var h, ht, i;
    ht = 2;
    for (i = 0; i < this.items.length; i += 1) {
        switch (this.items[i].getType()) {
        case 'CheckboxItem':
        case 'MenuItem':
            this.items[i].setPosition(this.x, this.y + ht);
            this.items[i].setDimension(this.width - 2, 24);
            h = 24;
            ht += h;
            break;
        default:
            this.items[i].setPosition(this.x, ht);
            this.items[i].setDimension(this.width - 2, 4);
            h = 4;
            ht += h;
        }
    }
};

/**
 * @class Item
 * Handles a menu item
 * @extend Element
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object/Action} options
 * @param {Menu} [parent]
 */
var Item = function (options, parent) {
    Element.call(this, options);
    /**
     * Defines the Label of the item
     * @type {String}
     */
    this.label = null;
    /**
     * Defines the action associated
     * @type {Action}
     */
    this.action = null;
    /**
     * Defines the parent menu associated
     * @type {Menu}
     */
    this.parentMenu = null;
    /**
     * Defines the child menu associated
     * @type {Menu}
     */
    this.menu = null;
    /**
     * Defines the tooltip value
     * @type {String}
     */
    this.toolTip = null;

    this.disabled = null;

    this.focused = null;

    this.icon = null;

    Item.prototype.initObject.call(this, options, parent);
};
Item.prototype = new Element();

/**
 * Defines the object's type
 * @type {String}
 */
Item.prototype.type = "Item";

/**
 * Defines the object's family
 * @type {String}
 */
Item.prototype.family = "Item";

/**
 * Initialize the object with the default values
 * @param {Object/Action} options
 */
Item.prototype.initObject = function (options, parent) {

    var defaults = {
        label: null,
        menu: null,
        toolTip: null,
        parentMenu: parent || null,
        disabled: false,
        focused: false,
        icon: 'adam-menu-icon-empty'
    };
    if (options && options.isAction) {
        this.loadAction(options, parent);
    } else {
        $.extend(true, defaults, options);
        this.setLabel(defaults.label)
            .setToolTip(defaults.toolTip)
            .setParentMenu(defaults.parentMenu)
            .setDisabled(defaults.disabled)
            .setIcon(defaults.icon)
            .setFocused(defaults.focused);
        if (!defaults.action) {
            this.action = new Action({
                text: defaults.label,
                cssStyle: defaults.icon,
                handler: defaults.handler
            });
        }
        if (defaults.menu) {
            this.setChildMenu(defaults.menu);
        }
    }
};

/**
 * Loads the action to the item
 * @param {Action} action
 */
Item.prototype.loadAction = function (action, parent) {
    this.action = action;
    this.setLabel(this.action.text);
    this.setIcon(this.action.cssStyle);
    this.setDisabled(this.action.disabled);
    this.setParentMenu(parent);
    this.setFocused(false);
    if (action.menu) {
        this.setChildMenu(action.menu);
    }
};

/**
 * Sets the item's label
 * @param {String} text
 * @return {*}
 */
Item.prototype.setLabel = function (text) {
    this.label = text;
    if (this.action) {
        this.action.setText(text);
    }
    return this;
};

Item.prototype.setIcon = function (icon) {
    this.icon = icon;
    if (this.action) {
        this.action.setCssClass(icon);
    }
    return this;
};



/**
 * Defines the way to paint this item
 */
Item.prototype.paint = function () {
    //TODO Implement this class
};

/**
 * Sets the parent menu
 * @param {Menu} parent
 * @return {*}
 */
Item.prototype.setParentMenu = function (parent) {
    this.parentMenu = parent;
    return this;
};

/**
 * Sets the child Menu
 * @param {Menu} child
 * @return {*}
 */
Item.prototype.setChildMenu = function (child) {
    if (child instanceof Menu) {
        //child.setParentMenu(this.parentMenu);
        child.setCanvas(this.parentMenu.canvas);
        child.setParent(this);
        this.menu = child;
    } else {
        //child.parentMenu = this.parentMenu;
        child.canvas = this.parentMenu.canvas;
        child.parent = this;
        this.menu = new Menu(child);
    }
    return this;
};

Item.prototype.setDisabled = function (value) {
    this.disabled = value;
    return this;
};

Item.prototype.setFocused = function (value) {
    this.focused = value;
    return this;
};

/**
 * Sets the tool tip value
 * @param {String} value
 * @return {*}
 */
Item.prototype.setToolTip = function (value) {
    this.toolTip = value;
    return this;
};

Item.prototype.createHTML = function () {
    var li;
    li = this.createHTMLElement('li');
    li.className = 'adam-item';
    if (this.disabled) {
        li.className = li.className + ' adam-disabled';
    }
    li.id = jCore.Utils.generateUniqueId();
    this.html = li;
    return this.html;
};

Item.prototype.attachListeners = function () {

};
Item.prototype.closeMenu = function () {
    if (this.parentMenu && this.parentMenu.canvas && this.parentMenu.canvas.currentMenu) {
        this.parentMenu.canvas.currentMenu.hide();
    }
};
/**
 * @class CheckboxItem
 * Handle checkboxes into the context menu
 * @extend Item
 *
 *
 * @constructor
 * Creates a new instance of this class
 * @param {Object} options
 * @param {Menu} [parent]
 */
var CheckboxItem = function (options, parent) {
    Item.call(this, options, parent);
    /**
     * Defines the checkbox's status
     * @type {Boolean}
     */
    this.checked = null;
    this.itemAnchor = null;
    CheckboxItem.prototype.initObject.call(this, options);
};
CheckboxItem.prototype = new Item();

/**
 * Defines the object's type
 * @type {String}
 */
CheckboxItem.prototype.type = "CheckboxItem";

/**
 * Initializes the object with the default values
 * @param {Object} options
 * @private
 */
CheckboxItem.prototype.initObject = function (options) {
    var defaults = {
        checked: false
    };
    $.extend(true, defaults, options);
    this.setChecked(defaults.checked);
};

/**
 * Sets the checkbox checked property
 * @param {Boolean} value
 * @return {*}
 */
CheckboxItem.prototype.setChecked = function (value) {
    if (_.isBoolean(value)) {
        this.checked = value;
    }
    return this;
};

CheckboxItem.prototype.createHTML = function () {
    var labelSpan, iconSpan;
    Item.prototype.createHTML.call(this);

    this.itemAnchor = this.createHTMLElement('a');
    this.itemAnchor.href = "#";

    labelSpan = this.createHTMLElement('span');
    labelSpan.innerHTML = this.label;
    labelSpan.className = "adam-label";

    iconSpan = this.createHTMLElement('span');
    iconSpan.className = (this.checked) ? 'adam-check-checked' : 'adam-check-unchecked';

    this.itemAnchor.appendChild(iconSpan);
    this.itemAnchor.appendChild(labelSpan);

    this.html.appendChild(this.itemAnchor);
    return this.html;
};

CheckboxItem.prototype.attachListeners = function () {
    var self = this;
    if (this.html) {
        $(this.itemAnchor)
            .click(function (e) {
                e.stopPropagation();
                if (!self.disabled) {
                    self.closeMenu();
                    self.action.handler(!self.checked);
                }
            })
            .mouseover(function () {
                self.setActiveItem(true);
            })
            .mouseout(function () {
                self.setActiveItem(false);
            })
            .mouseup(function (e) {
                e.stopPropagation();
            })
            .mousedown(function (e) {
                e.stopPropagation();
            });
    }
};

CheckboxItem.prototype.setActiveItem = function (value) {
    if (!this.disabled) {
        if (value) {
            this.style.addClasses(['adam-item-active']);
            this.style.applyStyle();
            this.parentMenu.setCurrentItem(this);
        } else {
            this.style.removeClasses(['adam-item-active']);
            this.style.applyStyle();
        }
    }
};

/**
 * @class SeparatorItem
 * Handles the menu item separator
 * @extend Item
 *
 * @constructor
 * Creates a new instance of a class
 * @param {Object} options
 * @param {Menu} [parent]
 */
var SeparatorItem = function (options, parent) {
    Item.call(this, options, parent);
};
SeparatorItem.prototype = new Item();

/**
 * Defines the object's type
 * @type {String}
 */
SeparatorItem.prototype.type = "SeparatorItem";

/**
 * Creates the HTML
 * @return {HTMLElement}
 */
SeparatorItem.prototype.createHTML = function () {
    var spanSep, itemSep;

    itemSep = this.createHTMLElement('li');
    itemSep.className = 'adam-item-separator';

    spanSep = this.createHTMLElement('span');
    spanSep.className = 'adam-separator';
    spanSep.innerHTML = " ";

    itemSep.appendChild(spanSep);
    this.html = itemSep;

    return this.html;
};

/**
 * @class MenuItem
 * Handles the items into the menu
 * @extend Item
 *
 * @constructor
 * Creates a new instance of the MenuItem Class
 * @param {Object} options
 * @param {Menu} [parent]
 */
var MenuItem = function (options, parent) {
    Item.call(this, options, parent);
    /**
     * Defines the icon to be used into the item
     * @type {String}
     */
    this.itemAnchor = null;
    this.hasMenuActive = null;
    MenuItem.prototype.initObject.call(this, options);
};
MenuItem.prototype = new Item();

/**
 * Defines the object's type
 * @type {String}
 */
MenuItem.prototype.type = "MenuItem";

/**
 * Initializes the object with default values
 * @param {Object} options
 * @private
 */
MenuItem.prototype.initObject = function (options) {
    var defaults = {
        hasMenuActive: false
    };
    $.extend(true, defaults, options);
    this.setHasMenuActive(defaults.hasMenuActive);
};


MenuItem.prototype.setHasMenuActive = function (value) {
    this.hasMenuActive = value;
    return this;
};

MenuItem.prototype.createHTML = function () {
    var labelSpan, iconSpan;
    Item.prototype.createHTML.call(this);


    this.itemAnchor = this.createHTMLElement('a');
    this.itemAnchor.href = '#';

    if (this.menu) {
        this.itemAnchor.className = 'adam-item-arrow';
    }

    labelSpan = this.createHTMLElement('span');
    labelSpan.innerHTML = this.label;
    labelSpan.className = "adam-label";

    iconSpan = this.createHTMLElement('span');
    iconSpan.className = 'adam-item-icon ' + this.icon;

    this.itemAnchor.appendChild(iconSpan);
    this.itemAnchor.appendChild(labelSpan);

    this.html.appendChild(this.itemAnchor);
    return this.html;

};

MenuItem.prototype.attachListeners = function () {
    var self = this;
    if (this.html) {
        $(this.itemAnchor)
            .click(function (e) {
                e.stopPropagation();
                if (!self.menu && !self.disabled) {
                    self.closeMenu();
                    self.action.handler();
                }
            })
            .mouseover(function () {
                self.setActiveItem(true);
                self.setActiveMenu(true);
                // if (self.menu && !self.disabled) {
                //     self.menu.show(self.x + self.width, self.y);
                //     self.setHasMenuActive(true);
                // }
            })
            .mouseout(function () {
                self.setActiveItem(false);
                self.setActiveMenu(false);
                // if (self.menu && !self.disabled) {
                //     self.menu.hide();
                // }
            })
            .mouseup(function (e) {
                e.stopPropagation();
            })
            .mousedown(function (e) {
                e.stopPropagation();
            });
    }
};

MenuItem.prototype.setActiveItem = function (value) {
    if (!this.disabled) {
        if (value) {
            if (!this.focused) {
                this.style.addClasses(['adam-item-active']);
                this.style.applyStyle();
                this.parentMenu.setCurrentItem(this);
            }
        } else {
            if (!this.hasMenuActive) {
                this.style.removeClasses(['adam-item-active']);
                this.style.applyStyle();
                this.setFocused(false);
            }
        }
    }
};

MenuItem.prototype.setActiveMenu = function (value) {
    if (this.menu && !this.disabled) {
        if (value) {
            if (!this.focused) {
                this.menu.show(this.x + this.width, this.y);
                this.setHasMenuActive(true);
                this.setFocused(true);
            }
        } else {
            if (!this.hasMenuActive) {
                this.menu.hide();
            }
        }
    }
};

/**
 * @class Layout
 * Handle the layout for panels
 * @extend Base
 *
 * @constructor
 * Creates a new instance of this class
 * @param {Object} options
 */
var Layout = function (options) {
    Base.call(this, options);

    Layout.prototype.initObject.call(this, options);
};

Layout.prototype = new Base();

/**
 * Defines the object's type
 * @type {String}
 */
Layout.prototype.type = 'Layout';

/**
 * Defines the object's family
 * @type {String}
 */
Layout.prototype.family = 'Layout';

/**
 * Initializes the object with default values
 * @param {Object} options
 */
Layout.prototype.initObject = function (options) {

};

/**
 * @class Tooltip
 * Handle tool tip messages
 * @extend Element
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Object} parent
 */
var Tooltip = function (options, parent) {
    Element.call(this, options);
    this.icon =  null;
    this.css = null;
    this.message = null;
    this.parent = null;
    this.messageObject = null;
    this.hoverParent = null;
    this.hoverClass = null;
    Tooltip.prototype.initObject.call(this, options, parent);
};

Tooltip.prototype = new Element();

Tooltip.prototype.type = 'Tooltip';

Tooltip.prototype.family = 'Tooltip';

Tooltip.prototype.initObject = function (options, parent) {
    var defaults = {
        message: null,
        icon: 'adam-tooltip-icon-default',
        css: '',
        parent: parent || null,
        hoverParent: true,
        hoverClass: 'hovered'
    };
    $.extend(true, defaults, options);
    this.setIcon(defaults.icon)
        .setMessage(defaults.message)
        .setParent(defaults.parent)
        .setCss(defaults.css)
        .setHoverClass(defaults.hoverClass)
        .setHoverParent(defaults.hoverParent);
};

Tooltip.prototype.setIcon = function (icon) {
    this.icon = icon;
    return this;
};

Tooltip.prototype.setMessage = function (msg) {
    this.message = msg;
    return this;
};

Tooltip.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};


Tooltip.prototype.setCss = function (value) {
    this.css = value;
    return this;
};

Tooltip.prototype.setHoverParent = function (value) {
    this.hoverParent = value;
    return this;
};

Tooltip.prototype.setHoverClass = function (css) {
    this.hoverClass = css;
    return this;
};

Tooltip.prototype.createHTML = function () {
    var msgDiv, iconSpan, tooltipAnchor;

    tooltipAnchor = this.createHTMLElement('a');
    tooltipAnchor.href = '#';
    tooltipAnchor.className = 'adam-tooltip ' + this.css;

    iconSpan = this.createHTMLElement('span');
    iconSpan.className = this.icon;

    // msgDiv = this.createHTMLElement('div');
    // msgDiv.className = 'adam-tooltip-message-off';
    // msgDiv.innerHTML = this.message;

    //this.messageObject = msgDiv;

    tooltipAnchor.appendChild(iconSpan);
    //tooltipAnchor.appendChild(msgDiv);

    this.html = tooltipAnchor;

    this.attachListeners();
    return this.html;
};

Tooltip.prototype.attachListeners = function () {
    var self = this;
    $(this.html)
        .mouseover(function (e) {
            e.stopPropagation();
            //console.log(e);
            self.show(e.pageX, e.pageY);
        })
        .mouseout(function (e) {
            e.stopPropagation();
            self.hide();
        });
};

Tooltip.prototype.show = function (x, y) {
    var msgDiv;

    if (!this.messageObject) {
        msgDiv = this.createHTMLElement('div');
        msgDiv.className = 'adam-tooltip-message';
        msgDiv.innerHTML = this.message;
        msgDiv.style.position = 'absolute';
        msgDiv.style.top = (y + 10) + 'px';
        msgDiv.style.left = (x + 10) + 'px';
        msgDiv.style.zIndex = 1000;

        this.messageObject = msgDiv;
    }

    document.body.appendChild(this.messageObject);
    if (this.hoverParent && this.parent) {
        $(this.parent.html).addClass(this.hoverClass);
    }
};

Tooltip.prototype.hide = function () {
    document.body.removeChild(this.messageObject);
    this.messageObject = null;
    if (this.hoverParent && this.parent) {
        $(this.parent.html).removeClass(this.hoverClass);
    }
};

/**
 * @class Panel
 * Handles panels to be inserted into containers
 * @extend Container
 *
 * @constructor
 * Creates a new instance of the object
 * @param {Object} options
 */
var Panel = function (options) {
    Container.call(this, options);
    /**
     * Defines the header HTML element
     * @type {HTMLElement}
     */
    this.header = null;
    /**
     * Defines the footer HTML Element
     * @type {HTMLElement}
     */
    this.footer = null;
    /**
     * Defines the layout object
     * @type {Layout}
     */
    this.layout = null;
    Panel.prototype.initObject.call(this, options);
};

Panel.prototype = new Container();
/**
 * Defines the object's type
 * @type {String}
 */
Panel.prototype.type = 'Panel';

/**
 * Defines the object's family
 * @type {String}
 */
Panel.prototype.family = 'Panel';

/**
 * Initializes the object with the default values
 */
Panel.prototype.initObject = function (options) {
    var defaults = {
        layout: null
    };
    $.extend(true, defaults, options);
    this.setHeader(defaults.header)
        .setFooter(defaults.footer)
        .setLayout(defaults.layout);
};

/**
 * Sets the header HTML element
 * @param {HTMLElement} h
 */
Panel.prototype.setHeader = function (h) {
    this.header = h;
    return this;
};

/**
 * Sets the header HTML element
 * @param {HTMLElement} f
 */
Panel.prototype.setFooter = function (f) {
    this.footer = f;
    return this;
};

/**
 * Sets the header HTML element
 * @param {Layout} layout
 */
Panel.prototype.setLayout = function (layout) {
    if (layout && layout.family && layout.family === 'Layout') {
        this.layout = layout;
    } else {
        this.layout = new Layout(layout);
    }
    return this;
};

Panel.prototype.createHTML = function () {
    var headerDiv, footerDiv;
    Container.prototype.createHTML.call(this);
    this.style.removeProperties(['width', 'height', 'position', 'top', 'left', 'z-index']);
    this.style.addClasses(['adam-panel']);
    if (this.header) {
        this.html.insertBefore(this.header, this.body);
    } else {
        headerDiv = this.createHTMLElement('div');
        headerDiv.className = 'adam-panel-header';
        this.html.insertBefore(headerDiv, this.body);
        this.header = headerDiv;
    }
    if (this.footer) {
        this.html.appendChild(this.footer);
    } else {
        footerDiv = this.createHTMLElement('div');
        footerDiv.className = 'adam-panel-footer';
        this.html.appendChild(footerDiv);
        this.footer = footerDiv;
    }
    this.body.className = 'adam-panel-body';
    return this.html;
};

Panel.prototype.load = function () {

};

/**
 * @class Form
 * Handles form panels
 * @extend Panel
 *
 * @constructor
 * Creates a new instance of the object
 * @param {Object} options
 */
var Form = function (options) {
    Panel.call(this, options);

    /**
     * Defines if the form has a proxy
     * @type {Boolean}
     */
    this.proxyEnabled = null;

    /**
     * Defines the form's url
     * @type {String}
     */
    this.url = null;

    /**
     * Defines the form's proxy object
     * @type {Proxy}
     */
    this.proxy = null;
    /**
     * Defines the form loading state
     * @type {Boolean}
     */
    this.loaded = false;
    /**
     * Defines the form's data
     * @type {Object}
     */
    this.data = null;
    /**
     * Defines the callback functions
     * @type {Object}
     */
    this.callback = {};
    /**
     * Defines the dirty form state
     * @type {Boolean}
     */
    this.dirty = false;

    this.buttons = [];

    this.footerAlign = null;

    this.labelWidth = null;

    this.footerHeight = null;

    this.headerHeight = null;

    this.closeContainerOnSubmit = null;

    this.parent = null;

    Form.prototype.initObject.call(this, options);
};

Form.prototype = new Panel();

/**
 * Defines the object's type
 * @type {String}
 */
Form.prototype.type = 'Form';

/**
 * Initializes the object with the default values
 */
Form.prototype.initObject = function (options) {
    var defaults = {
        url: null,
        data: null,
        proxyEnabled: true,
        callback: {},
        buttons: [],
        footerAlign: 'center',
        labelWidth: '30%',
        footerHeight: 40,
        headerHeight: 0,
        closeContainerOnSubmit: false
    };
    $.extend(true, defaults, options);
    this.setUrl(defaults.url)
        .setData(defaults.data)
        .setProxyEnabled(defaults.proxyEnabled)
        .setProxy(defaults.proxy)
        .setCallback(defaults.callback)
        .setButtons(defaults.buttons)
        .setLabelWidth(defaults.labelWidth)
        .setFooterHeight(defaults.footerHeight)
        .setHeaderHeight(defaults.headerHeight)
        .setCloseContainerOnSubmit(defaults.closeContainerOnSubmit)
        .setFooterAlign(defaults.footerAlign);
};

/**
 * Sets the form's url
 * @param {String} url
 * @return {*}
 */
Form.prototype.setUrl = function (url) {
    this.url = url;
    return this;
};

/**
 * Sets the Proxy Enabled property
 * @param {Boolean} value
 * @return {*}
 */
Form.prototype.setProxyEnabled = function (value) {
    this.proxyEnabled = value;
    return this;
};

/**
 * Defines the proxy object
 * @param {Proxy} proxy
 * @return {*}
 */
Form.prototype.setProxy = function (proxy) {
    if (proxy && proxy.family && proxy.family === 'Proxy') {
        this.proxy = proxy;
        this.url = proxy.url;
        this.proxyEnabled = true;
    } else {
        if (this.proxyEnabled) {
            if (proxy) {
                if (!proxy.url) {
                    proxy.url = this.url;
                }
                this.proxy = new Proxy(proxy);
            } else {
                if (this.url) {
                    this.proxy = new Proxy({url: this.url});
                }
            }
        }
    }
    return this;
};

/**
 * Defines the form's data object
 * @param {Object} data
 * @return {*}
 */
Form.prototype.setData = function (data) {
    this.data = data;
    if (this.loaded) {
        this.applyData();
    }
    return this;
};

/**
 * Sets the form's callback object
 * @param {Object} cb
 * @return {*}
 */
Form.prototype.setCallback = function (cb) {
    this.callback = cb;
    return this;
};

Form.prototype.setFooterAlign = function (position) {
    this.footerAlign = position;
    return this;
};

Form.prototype.setLabelWidth = function (width) {
    this.labelWidth = width;
    return this;
};

Form.prototype.setFooterHeight = function (width) {
    this.footerHeight = width;
    return this;
};

Form.prototype.setHeaderHeight = function (width) {
    this.headerHeight = width;
    return this;
};

Form.prototype.setCloseContainerOnSubmit = function (value) {
    this.closeContainerOnSubmit = value;
    return this;
};
/**
 * Loads the form
 */
Form.prototype.load = function () {
    if (!this.loaded) {
        if (this.proxy) {
            this.data = this.proxy.getData();
        }
        this.applyData();
        this.attachListeners();
        this.loaded = true;
    }
};

/**
 * Reloads the form
 */

Form.prototype.reload = function () {
    this.loaded = false;
    this.load();
};

/**
 * Applies the data to the form
 */
Form.prototype.applyData = function (dontLoad) {
    var propertyName, i, related;
    if (this.data) {
        if (this.data.related) {
            for (i = 0; i < this.items.length; i += 1) {
                if (this.items[i].getType() === 'ComboboxField' && this.items[i].related) {
                    related = this.items[i].related;
                    if (this.data.related[related]) {
                        this.items[i].setOptions(this.data.related[related]);
                    }
                }
            }
        }
        for (propertyName in this.data) {
            for (i = 0; i < this.items.length; i += 1) {
                if (this.items[i].name === propertyName) {
                    this.items[i].setValue(this.data[propertyName]);
                    break;
                }
            }
        }
    }
    if (this.callback.loaded && !dontLoad) {
        this.callback.loaded(this.data, this.proxy !== null);
    }
};

/**
 * Add Fields Items
 * @param {Object/Field}item
 */
Form.prototype.addItem = function (item) {
    var newItem;
    if (item && item.family && item.family === 'Field') {
        newItem = item;
        newItem.setParent(this);
    } else {
        if (item.jtype) {
            switch (item.jtype) {
            case 'text':
                newItem = new TextField(item, this);
                break;
            case 'combobox':
                newItem = new ComboboxField(item, this);
                break;
            case 'textarea':
                newItem = new TextareaField(item, this);
                break;
            case 'checkbox':
                newItem = new CheckboxField(item, this);
                break;
            case 'hidden':
                newItem = new HiddenField(item, this);
                break;
            case 'emailpicker':
                newItem = new EmailPickerField(item, this);
                break;
            case 'itemmatrix':
                newItem = new ItemMatrixField(item, this);
                break;
            case 'multipleitem':
                newItem = new MultipleItemField(item, this);
                break;
            case 'criteria':
                newItem = new CriteriaField(item, this);
                break;
            case 'itemupdater':
                newItem = new ItemUpdaterField(item, this);
                break;
            case 'expression':
                newItem = new ExpressionField(item, this);
            }
        }
    }
    if (newItem) {
        this.items.push(newItem);
    }
};

/**
 * Sets the items
 * @param {Array} items
 * @return {*}
 */
Form.prototype.setItems = function (items) {
    var i;
    for (i = 0; i < items.length; i += 1) {
        this.addItem(items[i]);
    }
    return this;
};

/**
 * Sets the buttons
 * @param {Array} buttons
 * @return {*}
 */
Form.prototype.setButtons = function (buttons) {
    var i;
    for (i = 0; i < buttons.length; i += 1) {
        this.addButton(buttons[i]);
    }
    return this;
};

/**
 * Resets the form
 */
Form.prototype.reset = function () {
    var i, oldNode;
    for (i = 0; i < this.items.length; i += 1) {
        this.items[i].reset();
    }
    this.setDirty(false);
    if (this.callback.reset) {
        this.callback.reset();
    }
};

/**
 * Submits the form
 */
Form.prototype.submit = function () {
    var data;
    if (this.testRequired()) {
        if (this.validate()) {
            data = this.getData();
            if (this.proxy) {
                this.proxy.sendData(data, this.callback);
            } else {
                if (this.callback.submit) {
                    this.callback.submit(data);
                }
            }
            if (this.closeContainerOnSubmit) {
                if (this.parent && this.parent.close) {
                    this.parent.close();
                }
            }
        } else {
            if (this.callback.failed) {
                this.callback.failed();
            }
        }
    } else {
        if (this.callback.required) {
            this.callback.required();
        }
    }
};

/**
 * Returns the data
 * @return {Object}
 */
Form.prototype.getData = function () {
    var i, result = {};
    for (i = 0; i < this.items.length; i += 1) {
        $.extend(result, this.items[i].getObjectValue());
    }
    return result;
};

/**
 * Sets the dirty form property
 * @param {Boolean} value
 * @return {*}
 */
Form.prototype.setDirty = function (value) {
    this.dirty = value;
    return this;
};

/**
 * Returns the dirty form property
 * @return {*}
 */
Form.prototype.isDirty = function () {
    return this.dirty;
};

/**
 * Evaluate the fields' validations
 * @return {Boolean}
 */
Form.prototype.validate = function () {
    var i, valid = true, current;
    for (i = 0; i < this.items.length; i += 1) {
        current = this.items[i].isValid();
        valid = valid && current;
        if (!current && this.items[i].errorTooltip) {
            $(this.items[i].errorTooltip.html).removeClass('adam-tooltip-error-off');
            $(this.items[i].errorTooltip.html).addClass('adam-tooltip-error-on');
        }
    }
    return valid;
};

Form.prototype.testRequired = function () {
    var i, response = true;
    for (i = 0; i < this.items.length; i += 1) {
        response = response && this.items[i].evalRequired();
    }
    return response;
};

Form.prototype.addButton = function (button) {
    var newButton;
    if (button && button.family && button.family === 'Button') {
        newButton = button;
        newButton.setParent(this);
    } else {
        newButton = new Button(button, this);
    }
    if (newButton) {
        this.buttons.push(newButton);
    }
};

Form.prototype.attachListeners = function () {
    var i;
    for (i = 0; i < this.items.length; i += 1) {
        this.items[i].attachListeners();
    }
    for (i = 0; i < this.buttons.length; i += 1) {
        this.buttons[i].attachListeners();
    }
    //$(this.footer).draggable( "option", "disabled", true);
    $(this.body).mousedown(function (e) {
        e.stopPropagation();
    });
};

Form.prototype.onEnterFieldHandler = function (fieldObject) {
    var that = this;
    return function () {
        var i;

        for(i = 0; i < that.items.length; i++) {
            if(that.items[i] !==  fieldObject && that.items[i] instanceof CriteriaField) {
                that.items[i].hidePanel();
            }
        }
    };
};

Form.prototype.createHTML = function () {
    var i, footerHeight, html;
    Panel.prototype.createHTML.call(this);
    this.footer.style.textAlign = this.footerAlign;
    for (i = 0; i < this.items.length; i += 1) {
        html = this.items[i].getHTML();
        $(html).find("select, input, textarea").focus(this.onEnterFieldHandler(this.items[i]));
        this.body.appendChild(html);
    }
    for (i = 0; i < this.buttons.length; i += 1) {
        this.footer.appendChild(this.buttons[i].getHTML());
    }
    this.body.style.bottom = (this.footerHeight + 8) + 'px';
    this.footer.style.height = this.footerHeight + 'px';
    return this.html;
};

Form.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

/**
 * @class Field
 * Handle form fields
 * @extend Element
 *
 * @constructor
 * Creates a new instace of the object
 * @param {Object} options
 * @param {Form} parent
 */
var Field = function (options, parent) {
    Element.call(this, options);
    /**
     * Defines the parent Form
     * @type {Form}
     */
    this.parent = null;
    /**
     * Defines the field's label
     * @type {String}
     */
    this.label = null;
    /**
     * Defines the Value
     * @type {*}
     */
    this.value = null;
    /**
     * Defines the validator object
     * @type {Validator}
     */
    this.validators = [];
    /**
     * Defines the field's name
     * @type {String}
     */
    this.name = null;
    /**
     * Defines the required state of the field
     * @type {Boolean}
     */
    this.required = null;
    /**
     * Defines the error message to show
     * @type {String}
     */
    this.messageError = null;
    /**
     * Defines the initial value of the field
     * @type {*}
     */
    this.initialValue = null;

    /**
     * Defines if the field is required but cannot be submited
     * @type {Boolean}
     */
    this.requiredFailed = false;

    this.fieldWidth = null;

    this.helpTooltip = null;

    this.errorTooltip = null;

    this.controlObject = null;

    this.change = null;

    this.readOnly = null;

    this.submit = null;

    this.proxy = null;

    Field.prototype.initObject.call(this, options, parent);
};
Field.prototype = new Element();

/**
 * Defines the object's type
 * @type {String}
 */
Field.prototype.type = 'Field';

/**
 * Defines the object's family
 * @type {String}
 */
Field.prototype.family = 'Field';

/**
 * Initializes the object with the default values
 * @param {Object} options
 * @param {Form} parent
 */
Field.prototype.initObject = function (options, parent) {
    var defaults = {
        required: false,
        label: '',
        validators: [],
        value: null,
        messageError: null,
        initialValue: null,
        fieldWidth: null,
        helpTooltip: null,
        change: function () {},
        readOnly: false,
        submit: true,
        proxy: null
    };
    $.extend(true, defaults, options);
    this.setParent(parent);
    this.setRequired(defaults.required)
        .setLabel(defaults.label)
        .setName(defaults.name || (this.type + '_' + this.id))
        .setValidators(defaults.validators)
        .setMessageError(defaults.messageError)
        .setInitialValue(defaults.initialValue)
        .setFieldWidth(defaults.fieldWidth)
        .setHelpTooltip(defaults.helpTooltip)
        .setErrorTooltip({})
        .setChangeHandler(defaults.change)
        .setReadOnly(defaults.readOnly)
        .setSubmit(defaults.submit)
        .setProxy(defaults.proxy)
        .setValue(defaults.value);
};

/**
 * Sets the required property
 * @param {Boolean} value
 * @return {*}
 */
Field.prototype.setRequired = function (value) {
    this.required = value;
    return this;
};

/**
 * Sets the field's value
 * @param {*} value
 * @param {Boolean} [change]
 * @return {*}
 */
Field.prototype.setValue = function (value, change) {
    if (change) {
        this.value = value;
    } else {
        this.value = value || this.initialValue;
    }
    if (this.html && this.controlObject) {
        this.controlObject.value = this.value;
    }
    if (this.proxy) {
        this.load();
    }
    return this;
};

/**
 * Sets the field's name
 * @param {String} name
 * @return {*}
 */
Field.prototype.setName = function (name) {
    this.name = name;
    return this;
};

/**
 * Sets the field's label
 * @param {String} label
 * @return {*}
 */
Field.prototype.setLabel = function (label) {
    this.label = label;
    return this;
};

/**
 * Sets the validator property
 * @param {Object/Validator} val
 * @return {*}
 */
Field.prototype.setValidators = function (val) {
    var i;

    for (i = 0; i < val.length; i += 1) {
        if (val[i] && val[i].family && val[i].family === 'Validator') {
            this.validators.push(val[i]);
        } else {
            this.validators.push(this.validatorFactory(val[i]));
        }
    }
    return this;
};

/**
 * Sets the fields validation error message
 * @param {String} msg
 * @return {*}
 */
Field.prototype.setMessageError = function (msg) {
    this.messageError = msg;
    return this;
};

/**
 * Sets the parent object
 * @param {Form} parent
 * @return {*}
 */
Field.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

/**
 * Sets the initial value property
 * @param {*} value
 * @return {*}
 */

Field.prototype.setInitialValue = function (value) {
    this.initialValue = value;
    return this;
};

Field.prototype.setFieldWidth = function (width) {
    this.fieldWidth = width;
    return this;
};

Field.prototype.setHelpTooltip = function (tooltip) {
    if (tooltip) {
        if (!tooltip.css) {
            tooltip.css = 'adam-tooltip-help';
        }
        this.helpTooltip = new Tooltip(tooltip, this);
    } else {
        this.helpTooltip = null;
    }
    return this;
};

Field.prototype.setErrorTooltip = function (tooltip) {
    if (tooltip) {
        if (!tooltip.css) {
            tooltip.css = 'adam-tooltip-error-off';
        }
        if (!tooltip.icon) {
            tooltip.icon = 'adam-tooltip-icon-error';
        }
        tooltip.visible = false;
        this.errorTooltip = new Tooltip(tooltip, this);
    } else {
        this.errorTooltip = null;
    }
    return this;
};

Field.prototype.setChangeHandler = function (fn) {
    this.change = fn;
    return this;
};

Field.prototype.setReadOnly = function (value) {
    this.readOnly = value;
    if (this.html) {
        this.controlObject.disabled = value;
    }
    return this;
};

Field.prototype.setSubmit = function (value) {
    this.submit = value;
    return this;
};

Field.prototype.setProxy = function (newProxy) {
    this.proxy = newProxy;
    return this;
};

/**
 * Returns a validator object
 * @param {Object} validator
 * @return {Validator}
 */
Field.prototype.validatorFactory = function (validator) {
    var out = null,
        regexp = {
            email: {
                pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                message: 'You must enter a valid email'
            },
            integer: {
                pattern: /^\s*(\+|-)?\d+\s*$/,
                message: "You must enter an integer"
            },
            required: {
                pattern: /\S/,
                message: "This field is required"
            }
        };
    if (validator && validator.jtype) {
        switch (validator.jtype) {
        case 'required':
            if (validator.criteria && !validator.criteria.trim) {
                /*validator.jtype = 'required_without_spaces';*/
                /*} else {*/
                out = new TextLengthValidator({
                    criteria: {
                        trim: false,
                        minLength: 1
                    },
                    errorMessage: validator.errorMessage || "this field is required"
                }, this);
            }
            break;
        case 'email':
        case 'integer':
            validator.criteria = regexp[validator.jtype].pattern;
            out = new RegExpValidator($.extend({
                errorMessage: regexp[validator.jtype].message
            }, validator), this);
            break;
        case 'comparison':
            out = new ComparisonValidator($.extend({
                errorMessage: "The comparison failed"
            }, validator), this);
            break;
        case 'regexp':
            out = new RegExpValidator($.extend({
                errorMessage: "The pattern text didn't match with the specified one"
            }, validator), this);
            break;
        case 'textLength':
            out = new TextLengthValidator($.extend({
                errorMessage: "The text length must be "
            }, validator), this);
            break;
        case 'custom':
            out = new CustomValidator($.extend({
                errorMessage: ""
            }, validator), this);
            break;
        case 'number':
            out = new NumberValidator($.extend({
                errorMessage: ""
            }, validator), this);
            break;
        case 'range':
            out = new RangeValidator($.extend({
                errorMessage: ""
            }, validator), this);
            break;
        default:
            out = new Validator($.extend({
                errorMessage: ""
            }, validator), this);
        }
    } else {
        out = new Validator(null, this);
    }
    return out;
};

/**
 * Returns the object representation of the field
 * @return {Object}
 */
Field.prototype.getObjectValue = function () {
    var result = {};
    if (this.submit) {
        result[this.name] = this.value;
    }
    return result;
};

/**
 * Returns the evaluation if the fields is required
 * @return {Boolean}
 */
Field.prototype.evalRequired = function () {
    var response = true;
    if (this.required) {
        response = (this.value !== null && this.value !== '');
        if (!response) {
            $(this.controlObject).addClass('required');
        } else {
            $(this.controlObject).removeClass('required');
        }
    }
    return response;
};

/**
 * Resets the field
 */
Field.prototype.reset = function () {
    this.setValue(this.initialValue || null, true);
    if (this.errorTooltip) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
    }
    if (this.required && this.controlObject) {
        $(this.controlObject).removeClass('required');
    }
};

Field.prototype.attachListeners = function () {

};

Field.prototype.createHTML = function () {
    Element.prototype.createHTML.call(this);
    this.style.removeProperties(['position', 'width', 'height', 'top', 'left', 'z-index']);
    this.style.addClasses(['adam-field']);
    return this.html;
};

Field.prototype.isValid = function () {
    var i, res = true;

    for (i = 0; i < this.validators.length; i += 1) {
        res = res && this.validators[i].isValid();

        if (!res) {
            this.errorTooltip.setMessage(this.validators[i].getErrorMessage());
            $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
            $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
            return res;
        }
    }

    if (res) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
    }

    return res;
};

Field.prototype.onChange = function () {
    if (this.required) {
        this.evalRequired();
    }

    this.isValid();

    if (this.change) {
        this.change(this);
    }

    return this;
};

Field.prototype.doLoad = function () {
    if (this.proxy) {
        this.load();
    }
};

/**
 * @abstract
 * Loads the field through the proxy defined
 */
Field.prototype.load = function () {

};

/**
 * @class Validator
 * Handles the validations of the fields
 * @extend Base
 *
 * @constructor
 * Create a new instance of the class
 * @param {Object} options
 * @param {Field} parent
 */
var Validator = function (options, parent) {
    Base.call(this, options);
    /**
     * Defines the Field parent
     * @type {Field}
     */
    this.parent = null;
    /**
     * Defines the criteria object
     * @type {Object}
     */
    this.criteria = null;
    /**
     * Defines if the object is validated
     * @type {Boolean}
     */
    this.validated = false;
    /**
     * Defines the validation state
     * @type {null/Boolean}
     */
    this.valid = null;
    /**
     * Defines the error message to show in case of the validation fails
     * @type {null/Boolean}
     */
    this.errorMessage = null;
    Validator.prototype.initObject.call(this, options, parent);
};
Validator.prototype = new Base();

/**
 * Defines the object's type
 * @type {String}
 */
Validator.prototype.type = 'Validator';

/**
 * Defines the object's family
 * @type {String}
 */
Validator.prototype.family = 'Validator';

/**
 * Initializes the object with default values
 * @param {Object} options
 * @param {Field} parent
 */
Validator.prototype.initObject = function (options, parent) {
    var defaults = {
        criteria: null,
        errorMessage: 'the validation has failed'
    };
    $.extend(true, defaults, options);
    this.setCriteria(defaults.criteria)
        .setParent(parent)
        .setErrorMessage(defaults.errorMessage);
};

/**
 * Sets the validation error message to show in case of the validation fails
 * @param {String} errorMessage
 * @return {*}
 */
Validator.prototype.setErrorMessage = function (errorMessage) {
    this.errorMessage = errorMessage;
    return this;
};

/**
 * GSets the validation error message to show in case of the validation fails
 * @param {String} errorMessage
 * @return {*}
 */
Validator.prototype.getErrorMessage = function () {
    return this.errorMessage;
};

/**
 * Sets the validation criteria
 * @param {Object} criteria
 * @return {*}
 */
Validator.prototype.setCriteria = function (criteria) {
    this.criteria = criteria;
    return this;
};

/**
 * Sets the parent field
 * @param {Field} parent
 * @return {*}
 */
Validator.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

/**
 * Evaluates the validator
 */
Validator.prototype.validate = function () {
    this.valid = true;
};

/**
 * Returns the validation response
 * @return {*}
 */
Validator.prototype.isValid = function () {
    this.validate();
    this.updateTooltip();
    return this.valid;
};

Validator.prototype.updateTooltip = function () {
    if (this.parent && this.parent.errorTooltip) {
        if (this.valid) {
            $(this.parent.errorTooltip.html)
                .removeClass('adam-tooltip-error-on')
                .addClass('adam-tooltip-error-off');
        } else {
            this.parent.errorTooltip.setMessage(this.errorMessage);
            $(this.parent.errorTooltip.html)
                .removeClass('adam-tooltip-error-off')
                .addClass('adam-tooltip-error-on');
        }
    }
};

var RegExpValidator = function (options, parent) {
    Validator.call(this, options, parent);
    RegExpValidator.prototype.initObject.call(this, options);
};

RegExpValidator.prototype = new Validator();

RegExpValidator.prototype.type = "RegExpValidator";

RegExpValidator.prototype.initObject = function (options) {
    var defaults = {
        errorMessage: "The text pattern doesn't match"
    };

    $.extend(true, defaults, options);

    this.setErrorMessage(defaults.errorMessage);
};

RegExpValidator.prototype.validate = function () {
    var res = false;
    if (this.criteria instanceof RegExp && this.parent && this.parent.value) {
        this.valid = this.criteria.test(this.parent.value);
    } else {
        this.valid = false;
    }
};

var TextLengthValidator = function (options, parent) {
    Validator.call(this, options, parent);
    TextLengthValidator.prototype.initObject(this, options);
};

TextLengthValidator.prototype = new Validator();

TextLengthValidator.prototype.type = 'TextLengthValidator';

TextLengthValidator.prototype.initObject = function (options) {
    var defaults = {
        errorMessage: "The text length doesn't match with the specified one"
    };

    $.extend(true, defaults, options);

    this.setErrorMessage(defaults.errorMessage);
};

TextLengthValidator.prototype.validate = function () {
    var res = false,
        value = this.criteria.trim ? $.trim(this.parent.value) : this.parent.value;

    this.valid = true;

    if (this.criteria.maxLength) {
        this.valid = value.length <= parseInt(this.criteria.maxLength, 10);
    }
    if (this.criteria.minLength) {
        this.valid = (this.valid !== null ? this.valid : true) && value.length >= parseInt(this.criteria.minLength, 10);
    }
};

var CustomValidator = function (options, parent) {
    Validator.call(this, options, parent);
};

CustomValidator.prototype = new Validator();

CustomValidator.prototype.type = "CustomValidator";

CustomValidator.prototype.validate = function () {
    if (typeof this.criteria.validationFunction === 'function') {
        this.valid = this.criteria.validationFunction.call(this.parent, this.parent.parent);
    }
    if (typeof this.valid === 'undefined' || this.valid === null) {
        this.valid = false;
    }
};

var NumberValidator = function (options, parent) {
    Validator.call(this, options, parent);

    NumberValidator.prototype.initObject.call(this, options);
};

NumberValidator.prototype = new Validator();

NumberValidator.prototype.initObject = function (options) {
    var defaults = {
        criteria: {
            decimalSeparator: ".",
            errorMessage: 'The value must be a number'
        }
    };
    $.extend(true, defaults, options);

    this.setDecimalSeparator(defaults.criteria.decimalSeparator)
        .setErrorMessage(defaults.errorMessage);
};

NumberValidator.prototype.setDecimalSeparator = function (separator) {
    this.criteria.decimalSeparator = separator;
};

NumberValidator.prototype.validate = function () {
    var evaluate, n, aux,
        intValid = false,
        decValid = false,
        i, r, c,
        milesSeparator;
    this.valid = false;
    if (this.parent && this.parent.value) {
        evaluate = this.parent.value.replace(/\./g, "");
        evaluate = evaluate.replace(/,/g, "");
        if (! /^\s*\d+\s*$/.test(evaluate)) {
            return;
        }

        if (this.criteria.decimalSeparator !== '.' && this.criteria.decimalSeparator !== ',') {
            return;
        }

        milesSeparator = this.criteria.decimalSeparator === ',' ? '.' : ',';

        r = new RegExp("\\" + milesSeparator, "g"); //generates a regular expression equivalent to /\./g
        //split the string into integer part and decimal part
        n = this.parent.value.split(this.criteria.decimalSeparator);
        //checks if there's at most one decimal separator
        aux = this.parent.value.match(new RegExp("\\" + this.criteria.decimalSeparator, 'g'));
        if (aux && aux.length > 1) {
            return;
        }
        //checks if the integer part (witouth miles separator) is composed only by digits
        if (!/^\s*\d+\s*$/.test(n[0].replace(new RegExp('\\' + milesSeparator, 'g'), ""))) {
            return;
        }
        //checks if the integer part has at least one miles separator, if it is 
        //check the number of them is the correct
        if (n[0].match(r) && n[0].match(r).length !== 0) {
            if (n[0].charAt(0) === '0') {
                return;
            }
            aux = Math.floor(n[0].length / 4);
            aux -= (n[0].length % 4) ? 0 : 1; //the number of separators
            if (n[0].match(r).length !== aux) {
                return;
            }
            i = n[0].length - 4;
            c = 0;
            while (i > 0) {
                if (n[0].charAt(i) === milesSeparator) {
                    c += 1;
                }
                i -= 4;
            }
            if (c != aux) {
                return;
            }
            intValid = true;
        }

        if (n[1]) {
            if (!/^\s*\d+\s*$/.test(n[1])) {
                return;
            }
        }
        this.valid = true;
    }
};

var ComparisonValidator = function (options, parent) {
    Validator.call(this, options, parent);
    ComparisonValidator.prototype.initObject(this, options);
};

ComparisonValidator.prototype = new Validator();

ComparisonValidator.prototype.type = "ComparisonValidator";

ComparisonValidator.prototype.initObject = function (options) {
    var defaults = {
        errorMessage: "The comparison failed"
    };

    $.extend(true, defaults, options);

    this.setErrorMessage(defaults.errorMessage);
};

ComparisonValidator.prototype.validate = function () {
    var evaluate, i, operators = {
        '==': function (a, b) {
            return a === b;
        },
        '>': function (a, b) {
            return a > b;
        },
        '>=': function (a, b) {
            return a >= b;
        },
        '<': function (a, b) {
            return a < b;
        },
        '<=': function (a, b) {
            return a <= b;
        }
    }, fields = this.parent.parent.items.slice(0), currentField, j;
    this.valid = false;
    if (!operators[this.criteria.operator]) {
        return;
    }
    switch (this.criteria.compare) {
    case 'textLength':
        evaluate = this.parent.value.length;
        for (i = 0; i < this.criteria.compareWith.length; i += 1) {
            for (j = 0; j < fields.length; j += 1) {
                currentField = fields.shift();
                if (currentField.name === this.criteria.compareWith[j]) {
                    break;
                }
            }
            if (!operators[this.criteria.operator](evaluate, currentField.value.length)) {
                return;
            }
        }
        break;
    case 'numeric':
        if (isNaN(this.parent.value.replace(/,/g, ""))) {
            return;
        }
        evaluate = parseFloat(this.parent.value.replace(/,/g, ""));
        for (i = 0; i < this.criteria.compareWith.length; i += 1) {
            for (j = 0; j < fields.length; j += 1) {
                currentField = fields.shift();
                if (currentField.name === this.criteria.compareWith[j]) {
                    break;
                }
            }
            if (isNaN(currentField.value.replace(/,/g, ""))) {
                return;
            }
            if (!operators[this.criteria.operator](evaluate, parseFloat(currentField.value.replace(/,/g, "")))) {
                return;
            }
        }
        break;
    default: //string
        evaluate = this.parent.value;
        for (i = 0; i < this.criteria.compareWith.length; i += 1) {
            for (j = 0; j < fields.length; j += 1) {
                currentField = fields.shift();
                if (currentField.name === this.criteria.compareWith[j]) {
                    break;
                }
            }
            if (!operators[this.criteria.operator](evaluate, currentField.value)) {
                return;
            }
        }
    }
    this.valid = true;
};

var RangeValidator = function (options, parent) {
    Validator.call(this, options, parent);
    RangeValidator.prototype.initObject.call(this, options);
};

RangeValidator.prototype = new Validator();

RangeValidator.prototype.initObject = function (options) {
    var defaults = {
        criteria: {
            type: "string",
            dateFormat: "yyyy-mm-dd"
        },
        errorMessage: "the value is out of ranges"
    };

    $.extend(true, defaults, options);

    this.setCriteria(defaults.criteria)
        .setErrorMessage(defaults.errorMessage);
};

RangeValidator.prototype.validate = function () {
    var that = this,
        options = [
            "minValue",
            "maxValue"
        ],
        parser = {
            string: function (val) {
                return val.toString();
            },
            numeric: function (val) {
                if (isNaN(val)) {
                    return NaN;
                }
                return parseFloat(val);
            },
            date: function (val) {
                var i, date, aux = {}, dateParts = {}, length,
                    indexes = ["yyyy", "mm", "dd", "hh", "ii", "ss"];
                if (typeof val === 'object') {
                    date = new Date(
                        val.year,
                        val.month - 1,
                        val.day,
                        val.hours || 0,
                        val.minutes || 0,
                        val.seconds || 0,
                        val.milliseconds || 0
                    );
                } else if (typeof val === 'string') {
                    that.criteria.dateFormat = $.trim(that.criteria.dateFormat);
                    /*if(that.criteria.dateFormat.length !== val.length) {
                        return null;
                    }*/
                    for (i = 0; i < indexes.length; i += 1) {
                        aux[indexes[i]] = that.criteria.dateFormat.toLowerCase().indexOf(indexes[i]);
                        switch (indexes[i]) {
                        case 'yyyy':
                        case 'mm':
                        case 'dd':
                            dateParts[indexes[i]] = aux[indexes[i]] >= 0 ? val.substr(aux[indexes[i]], indexes[i].length) : "x";
                            break;
                        default:
                            dateParts[indexes[i]] = (aux[indexes[i]] >= 0 ? val.substr(aux[indexes[i]], 2) : 0) || 0;
                        }

                        if (isNaN(dateParts[indexes[i]]) || !/^\s*\d+\s*$/.test(dateParts[indexes[i]])) {
                            return null;
                        } else {
                            dateParts[indexes[i]] = parseInt(dateParts[indexes[i]], 10);
                        }
                    }

                    if (dateParts.mm <= 0 && dateParts.dd <= 0) {
                        return null;
                    }
                    switch (dateParts.mm) {
                    case 4:
                    case 6:
                    case 9:
                    case 11:
                        if (dateParts.dd > 30) {
                            return null;
                        }
                        break;
                    case 2:
                        if (((dateParts.yyyy % 4 === 0 && dateParts.yyyy % 100 !== 0) || (dateParts.yyyy % 400 === 0))
                                && dateParts.dd > 29) {
                            return null;
                        } else {
                            if (dateParts.dd > 28) {
                                return null;
                            }
                        }
                        break;
                    default:
                        if (dateParts.dd > 31) {
                            return null;
                        }
                        break;
                    }

                    date = new Date(
                        dateParts.yyyy,
                        dateParts.mm > 0 && dateParts.mm < 13 ? dateParts.mm - 1 : "x",
                        dateParts.dd,
                        dateParts.hh >= 0 && dateParts.hh < 24 ? dateParts.hh : "x",
                        dateParts.ii >= 0 && dateParts.ii < 60 ? dateParts.ii : "x",
                        dateParts.ss >= 0 && dateParts.ss < 60 ? dateParts.ss : "x"
                    );

                } else {
                    return null;
                }
                if (Object.prototype.toString.call(date) !== "[object Date]") {
                    return null;
                }
                return !isNaN(date.getTime()) ? date : null;
            }
        },
        i,
        parsedValues = {};

    for (i = 0; i < options.length; i += 1) {
        if (this.criteria[options[i]]) {
            parsedValues[options[i]] = parser[this.criteria.type.toLowerCase()](this.criteria[options[i]]);
        }
    }

    if (!(this.criteria.minValue || this.criteria.maxValue)) {
        this.valid = false;
    } else {
        this.valid = true;
        if (parsedValues.maxValue) {
            this.valid = parser[this.criteria.type.toLowerCase()](this.parent.value) <= parsedValues.maxValue;
        }

        if (parsedValues.minValue) {
            this.valid = this.valid && parser[this.criteria.type.toLowerCase()](this.parent.value) >= parsedValues.minValue;
        }
    }
};

/**
 * @class TextField
 * Handle text input fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var TextField = function (options, parent) {
    Field.call(this, options, parent);
    /**
     * Defines the maximum number of characters supported
     * @type {Number}
     */
    this.maxCharacters = null;
    TextField.prototype.initObject.call(this, options);
};
TextField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
TextField.prototype.type = 'TextField';

/**
 * Initializes the object with the default values
 * @param {Object} options
 */
TextField.prototype.initObject = function (options) {
    var defaults = {
        maxCharacters: 0
    };
    $.extend(true, defaults, options);
    this.setMaxCharacters(defaults.maxCharacters);
};

/**
 * Sets the maximun characters property
 * @param {Number} value
 * @return {*}
 */
TextField.prototype.setMaxCharacters = function (value) {
    this.maxCharacters = value;
    return this;
};

/**
 * Create the HTML Element
 */
TextField.prototype.createHTML = function () {
    var fieldLabel, textInput, required = '', readAtt;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    this.html.appendChild(fieldLabel);

    textInput = this.createHTMLElement('input');
    textInput.id = this.name;
    textInput.value = this.value || "";
    if (this.fieldWidth) {
        textInput.style.width = this.fieldWidth;
    }
    if (this.readOnly) {
        readAtt = document.createAttribute('readonly');
        textInput.setAttributeNode(readAtt);
    }
    this.html.appendChild(textInput);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.controlObject = textInput;

    return this.html;
};

TextField.prototype.attachListeners = function () {
    var self = this;
    if (this.controlObject) {
        $(this.controlObject)
            .change(function () {
                self.setValue(this.value, true);
                self.onChange();
            })
            .keydown(function (e) {
                e.stopPropagation();
            });
    }
};

//

/**
 * @class ComboboxField
 * Handles drop down fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var ComboboxField = function (options, parent) {
    Field.call(this, options, parent);
    /**
     * Defines the combobox options
     * @type {Array}
     */
    this.options = [];
    this.related = null;
    ComboboxField.prototype.initObject.call(this, options);
};
ComboboxField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
ComboboxField.prototype.type = 'ComboboxField';

/**
 * Initializes the object with default values
 * @param {Object} options
 */
ComboboxField.prototype.initObject = function (options) {
    var defaults = {
        options: [],
        related: null
    };
    $.extend(true, defaults, options);
    this.setOptions(defaults.options)
        .setRelated(defaults.related);
};

/**
 * Sets the combo box options
 * @param {Array} data
 * @return {*}
 */
ComboboxField.prototype.setOptions = function (data) {
    var i;
    this.options = data;
    if (this.html) {
        for (i = 0; i < this.options.length; i += 1) {
            this.controlObject.appendChild(this.generateOption(this.options[i]));
        }

        if (!this.value) {
            this.value = this.controlObject.value;
        }
    }
    return this;
};

ComboboxField.prototype.setRelated = function (data) {
    this.related = data;
    return this;
};

/**
 * Creates the HTML Element of the field
 */
ComboboxField.prototype.createHTML = function () {
    var fieldLabel, selectInput, required = '', opt, i, disableAtt;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    this.html.appendChild(fieldLabel);

    selectInput = this.createHTMLElement('select');
    selectInput.id = this.name;
    for (i = 0; i < this.options.length; i += 1) {
        selectInput.appendChild(this.generateOption(this.options[i]));
    }
    if (!this.value) {
        this.value = selectInput.value;
    }
    if (this.fieldWidth) {
        selectInput.style.width = this.fieldWidth;
    }
    if (this.readOnly) {
        disableAtt = document.createAttribute('disabled');
        selectInput.setAttributeNode(disableAtt);
    }
    this.html.appendChild(selectInput);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.controlObject = selectInput;

    return this.html;
};

ComboboxField.prototype.generateOption = function (item) {
    var out, selected = '', value, text;
    out = this.createHTMLElement('option');
    if (typeof item === 'object') {
        value = item.value;
        text = item.text;
    } else {
        value = item;
    }
    out.selected = this.value === value;
    out.value = value;
    out.label = text || value;
    out.appendChild(document.createTextNode(text || value));
    return out;
};

ComboboxField.prototype.attachListeners = function () {
    var self = this;
    if (this.controlObject) {
        $(this.controlObject)
            .change(function (e) {
                self.setValue(this.value, true);
                self.onChange();
            });
    }
};

//

/**
 * @class TextareaField
 * Handles TextArea fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var TextareaField = function (options, parent) {
    Field.call(this, options, parent);
    this.fieldHeight = null;
    TextareaField.prototype.initObject.call(this, options);
};
TextareaField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
TextareaField.prototype.type = "TextareaField";

TextareaField.prototype.initObject = function (options) {
    var defaults = {
        fieldHeight: null
    };
    $.extend(true, defaults, options);
    this.setFieldHeight(defaults.fieldHeight);
};

TextareaField.prototype.setFieldHeight = function (height) {
    this.fieldHeight = height;
    return this;
};
/**
 * Creates the HTML Element of the field
 */
TextareaField.prototype.createHTML = function () {
    var fieldLabel, textInput, required = '', readAtt;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    textInput = this.createHTMLElement('textarea');
    textInput.id = this.name;
    textInput.value = this.value;
    if (this.fieldWidth) {
        textInput.style.width = this.fieldWidth;
    }
    if (this.fieldHeight) {
        textInput.style.height = this.fieldHeight;
    }
    if (this.readOnly) {
        readAtt = document.createAttribute('readonly');
        textInput.setAttributeNode(readAtt);
    }
    this.html.appendChild(textInput);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.controlObject = textInput;

    return this.html;
};


TextareaField.prototype.attachListeners = function () {
    var self = this;
    if (this.controlObject) {
        $(this.controlObject)
            .change(function () {
                self.setValue(this.value, true);
                self.onChange();
            })
            .keydown(function (e) {
                e.stopPropagation();
            });
    }
};
//

/**
 * @class CheckboxField
 * Handles the checkbox fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var CheckboxField = function (options, parent) {
        Field.call(this, options, parent);

//        CheckboxField.prototype.initObject.call(this, options);
    };
CheckboxField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
CheckboxField.prototype.type = 'CheckboxField';

/**
 * Creates the HTML Element of the field
 */
CheckboxField.prototype.createHTML = function () {
    var fieldLabel, textInput, required = '', readAtt;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
//    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    textInput = this.createHTMLElement('input');
    textInput.id = this.name;
    textInput.type = 'checkbox';
    if (this.value) {
        textInput.checked = true;
    } else {
        textInput.checked = false;
    }
    if (this.readOnly) {
        readAtt = document.createAttribute('readonly');
        textInput.setAttributeNode(readAtt);
    }
    this.html.appendChild(textInput);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.controlObject = textInput;

    return this.html;
};

CheckboxField.prototype.attachListeners = function () {
    var self = this;
    if (this.controlObject) {
        $(this.controlObject)
            .change(function (a, b) {
                var val;
                if (this.checked) {
                    val = true;
                } else {
                    val = false;
                }
                self.setValue(val, true);
                self.onChange();
            });
    }
};

CheckboxField.prototype.getObjectValue = function () {
    var response = {};
    if (this.value) {
        response[this.name] = true;
    } else {
        response[this.name] = false;
    }
    return response;
};

CheckboxField.prototype.evalRequired = function () {
    var response = true;
    if (this.required) {
        response = this.value;
        if (!response) {
            $(this.controlObject).addClass('required');
        } else {
            $(this.controlObject).removeClass('required');
        }
    }
    return response;
};

/**
 * @class RadiobuttonField
 * Handles the radio button fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var RadiobuttonField = function (options, parent) {
    Field.call(this, options, parent);
    this.defaults = {
        options: {},
        onClick: function (e, ui) {}
    };
    $.extend(true, this.defaults, options);
//    RadiobuttonField.prototype.initObject.call(this, options);
};
RadiobuttonField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
RadiobuttonField.prototype.type = 'RadiobuttonField';

/**
 * Creates the HTML Element of the field
 */
RadiobuttonField.prototype.createHTML = function () {
    var fieldLabel, textInput, required = '', readAtt;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }
//    console.log(this.defaults);
    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';

    textInput = this.createHTMLElement('input');
    textInput.name = this.name;
    textInput.type = 'radio';
    textInput.value = this.value;

    if (typeof (this.defaults.options.labelAlign) === 'undefined' ||
            this.defaults.options.labelAlign === 'left') {
        fieldLabel.style.width = this.parent.labelWidth;
        fieldLabel.innerHTML = required + this.label + ':';
        fieldLabel.style.verticalAlign = 'top';
        this.html.appendChild(fieldLabel);
        this.html.appendChild(textInput);
    } else if (this.defaults.options.labelAlign === 'right') {
        fieldLabel.innerHTML = '&nbsp;' + this.label + required;
        textInput.style.marginLeft = (this.defaults.options.marginLeft) ? this.defaults.options.marginLeft + 'px' : '0px';
        this.html.appendChild(textInput);
        this.html.appendChild(fieldLabel);
    }

    if (this.value) {
        textInput.checked = true;
    } else {
        textInput.checked = false;
    }

    if (this.readOnly) {
        readAtt = document.createAttribute('readonly');
        textInput.setAttributeNode(readAtt);
    }

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.controlObject = textInput;

    return this.html;
};

RadiobuttonField.prototype.attachListeners = function () {
    var self = this;
    if (this.controlObject) {
        if (typeof this.defaults.onClick !== 'undefined' && typeof this.defaults.onClick === 'function') {
            $(this.controlObject).on('click', function (e, ui) {return self.defaults.onClick(); });
        }

//        $(this.controlObject)
//            .change(function (a, b) {
//                var val;
//                if (this.checked) {
//                    val = true;
//                } else {
//                    val = false;
//                }
//                self.setValue(val, true);
//                self.onChange();
//            });
    }
};

RadiobuttonField.prototype.getObjectValue = function () {
    return this.value;
};

RadiobuttonField.prototype.evalRequired = function () {
    var response = true;
    if (this.required) {
        response = this.value;
        if (!response) {
            $(this.controlObject).addClass('required');
        } else {
            $(this.controlObject).removeClass('required');
        }
    }
    return response;
};

RadiobuttonField.prototype.setValue = function (value, change) {
    if (change) {
        this.value = value;
    } else {
        this.value = value || this.initialValue;
    }
    if (this.html && this.controlObject) {
        this.controlObject.checked = this.value;
    }
    if (this.proxy) {
        this.load();
    }
    return this;
};

/**
 * @class LabelField
 * Handles the Label fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var LabelField = function (options, parent) {
    Field.call(this, options, parent);
    this.submit = false;
    this.defaults = {
        options: {
            marginLeft : 10
        }
    };
    $.extend(true, this.defaults, options);
};
LabelField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
LabelField.prototype.type = 'LabelField';

/**
 * Creates the HTML Element of the field
 */
LabelField.prototype.createHTML = function () {
    var fieldLabel;
    Field.prototype.createHTML.call(this);

    fieldLabel = this.createHTMLElement('span');
//    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = this.label + ':';
    fieldLabel.style.verticalAlign = 'top';
    fieldLabel.style.marginLeft = this.defaults.options.marginLeft + 'px';
    this.html.appendChild(fieldLabel);

    return this.html;
};

/**
 * @class HiddenField
 * Handle the hidden fields
 * @extend Field
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var HiddenField = function (options, parent) {
    Field.call(this, options, parent);
};
HiddenField.prototype = new Field();

/**
 * Defines the object's type
 * @type {String}
 */
HiddenField.prototype.type = 'HiddenField';

/**
 * Creates the HTML Element of the field
 */
HiddenField.prototype.createHTML = function () {
    Element.prototype.createHTML.call(this);
    return this.html;
};

//

var EmailGroupField = function (options, parent) {
    Field.call(this, options, parent);
};

EmailGroupField.prototype = new Field();

EmailGroupField.prototype.type = 'EmailGroupField';

/**
 * @class Button
 * Handles buttons
 * @extend Element
 *
 * @constructor
 * Create a new instance of the class
 * @param {Object} options
 * @param {Form} parent
 */
var Button = function (options, parent) {
    Element.call(this, options);
    this.parent = null;
    this.caption = null;
    this.action = null;
    this.icon = null;
    Button.prototype.initObject.call(this, options, parent);
};

Button.prototype = new Element();

Button.prototype.type = 'Button';
Button.prototype.family = 'Button';

Button.prototype.initObject = function (options, parent) {
    var defaults, self = this;
    if (options.isAction) {
        this.loadAction(options, parent);
    } else {
        defaults = {
            caption: null,
            parent: parent || null,
            jtype: 'normal',
            handler: function () {},
            icon: null
        };
        $.extend(true, defaults, options);
        this.setCaption(defaults.caption)
            .setParent(defaults.parent)
            .setIcon(defaults.icon);
        switch (defaults.jtype) {
        case 'reset':
            this.action = new Action({
                text: this.caption,
                handler: function () {
                    self.parent.reset();
                },
                cssStyle: this.icon
            });
            break;
        case 'submit':
            this.action = new Action({
                text: this.caption,
                handler: function () {
                    self.parent.submit();
                },
                cssStyle: this.icon
            });
            break;
        case 'normal':
            this.action = new Action({
                text: this.caption,
                handler: defaults.handler,
                cssStyle: this.icon
            });
            break;
        }
    }
};

Button.prototype.loadAction = function (action, parent) {
    this.action = action;
    this.setCaption(this.action.text);
    this.setIcon(this.action.cssStyle);
    this.setParent(parent);
};

Button.prototype.setCaption = function (text) {
    this.caption = text;
    return this;
};

Button.prototype.setIcon = function (value) {
    this.icon = value;
    return this;
};

Button.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

Button.prototype.createHTML = function () {
    var buttonAnchor, iconSpan, labelSpan;

    buttonAnchor = this.createHTMLElement('a');
    buttonAnchor.href = '#';
    buttonAnchor.className = 'adam-button';
    buttonAnchor.id = this.id;


    if (this.icon) {
        iconSpan = this.createHTMLElement('span');
        iconSpan.className = this.icon;
        buttonAnchor.appendChild(iconSpan);
    }

    labelSpan = this.createHTMLElement('span');
    labelSpan.className = 'adam-button-label';
    labelSpan.innerHTML = this.caption;
    buttonAnchor.appendChild(labelSpan);

    this.html = buttonAnchor;

    return this.html;
};

Button.prototype.attachListeners = function () {
    var self = this;
    $(this.html)
        .click(function (e) {
            e.stopPropagation();
            if (self.action.handler) {
                self.action.handler();
            }
        })
        .mousedown(function (e) {
            e.stopPropagation();
        });
};

var RestProxy = function (options) {
    Proxy.call(this, options);
    this.restClient = null;
    this.getMethod = null;
    this.sendMethod = null;
    this.uid = null;
    RestProxy.prototype.initObject.call(this, options);
};

RestProxy.prototype = new Proxy();

RestProxy.prototype.type = 'RestProxy';

RestProxy.prototype.initObject = function (options) {
    var defaults = {
        restClient: null,
        sendMethod: 'PUT',
        getMethod: 'GET',
        uid: null
    };
    $.extend(true, defaults, options);
    this.setUid(defaults.uid)
        .setRestClient(defaults.restClient)
        .setSendMethod(defaults.sendMethod)
        .setGetMethod(defaults.getMethod);
};

RestProxy.prototype.setUid = function (id) {
    this.uid = id;
    return this;
};


RestProxy.prototype.setRestClient = function (restClient) {
    this.restClient = restClient;
    return this;
};

RestProxy.prototype.setSendMethod = function (method) {
    this.sendMethod = method;
    return this;
};

RestProxy.prototype.setGetMethod = function (method) {
    this.getMethod = method;
    return this;
};

RestProxy.prototype.getData = function (params) {
    var operation, self = this, resp;
    if (this.restClient) {
        operation = this.getOperation(this.getMethod);
        this.restClient.consume({
            operation: operation,
            url: this.url,
            id: this.uid,
            data: params,
            success: function (xhr, response) {
                status = response.success;
                if (response.success) {
                    resp = response;
                }
            }
        });
    }
    return resp;
};

RestProxy.prototype.sendData = function (data, callback) {
    var operation, self = this, send;
    if (this.restClient) {
        operation = this.getOperation(this.sendMethod);
        send = {
            operation: operation,
            url: this.url,
            id: this.uid,
            data: data
        };
        if (callback) {
            if (callback.success) {
                send.success = callback.success;
            }
            if (callback.failure) {
                send.failure = callback.failure;
            }
        }
        this.restClient.consume(send);
    }
};

RestProxy.prototype.getOperation = function (method) {
    var out;
    switch (method) {
    case 'GET':
        out = 'read';
        break;
    case 'POST':
        out = 'create';
        break;
    case 'PUT':
        out = 'update';
        break;
    case 'DELETE':
        out = 'delete';
        break;
    }
    return out;
};

var MultipleItemField = function (options, parent) {
    this.items = [];
    Field.call(this, options, parent);
    this.controlObject = null;
    this.panel = null;
    this.fieldHeight = null;
    this.itemsContainer = null;
    MultipleItemField.prototype.initObject.call(this, options);
};

MultipleItemField.prototype  = new Field();

MultipleItemField.prototype.initObject = function (options) {
    var defaults = {
        items: [],
        fieldWidth: 280,
        fieldHeight: 120,
        panel: null
    };

    $.extend(true, defaults, options);

    this.setPanel(defaults.panel || new MultipleItemPanel())
        .setItems(defaults.items)
        .setFieldWidth(defaults.fieldWidth)
        .setFieldHeight(defaults.fieldHeight);
};

MultipleItemField.prototype.setPanel = function (panel) {
    if(panel instanceof MultipleItemPanel) {
        if(this.panel) {
            $(this.panel.getHTML()).remove();
        }
        this.panel = panel;
    }

    return this;
};

MultipleItemField.prototype.setValue = function (value) {
    if(value) {
        var items = JSON.parse(value);
        this.setItems(items);
    }
    return this;
};

MultipleItemField.prototype.setFieldWidth = function (width) {
    if(!isNaN(width)) {
        this.fieldWidth = width;   
    }
    return this;
};

MultipleItemField.prototype.setFieldHeight = function (height) {
    if(!isNaN(height)) {
        this.fieldHeight = height;   
    }
    return this;
};

MultipleItemField.prototype.addItem = function (item) {
    var that = this, newItem;

    if(item instanceof SingleItem) {
        newItem = item;
    } else if (typeof item === 'object') {
        newItem = new SingleItem({
            label: item.label || "",
            value: item.value || null
        });
    } else {
        return this;
    }

    this.items.push(newItem);

    $(this.controlObject).before(newItem.getHTML());

    newItem.onRemove = function () {
        that.removeItem(this);
    };

    this.hidePanel();

    this.onChange();

    return this;
};

MultipleItemField.prototype.removeItem = function (item) {
    var id = item.id, index = null, i;     

    for (i = 0; i < this.items.length; i += 1) {
        if (this.items[i].id === id) {
            index = i;
            break;
        }
    }
    if (index !== null) {
        this.items.splice(index, 1);
    }
    this.hidePanel();
    $(this.controlObject).focus();
    this.onChange();

    return this;
};

MultipleItemField.prototype.clear = function () {
    $(this.itemsContainer).find("> li").remove();
    this.items = [];
};

MultipleItemField.prototype.setItems = function (items) {
    var i, newItem;
    for (i = 0; i < items.length; i += 1) {
        this.addItem(items[i]);
    }
    return this;
};

MultipleItemField.prototype.hidePanel = function () {
    var i;
    $(this.itemsContainer).removeClass('expanded');
    for(i = 0; i < this.items.length; i++) {
        $(this.items[i].html).removeClass("focused");
    }
    this.panel.close();

    return this;
};

MultipleItemField.prototype.scrollTo = function () {
    var fieldsDiv = this.html.parentNode;
    if (fieldsDiv.scrollTop + $(fieldsDiv).outerHeight() < getRelativePosition(this.itemsContainer, fieldsDiv).top + $(this.itemsContainer).outerHeight() + fieldsDiv.scrollTop) {
        this.html.parentNode.scrollTop = $(this.itemsContainer).position().top;
        return;
    }

    return this;
};

MultipleItemField.prototype.showPanel = function () {

    $(this.itemsContainer).addClass('focused');
    $(this.panel.html).addClass('focused');

    this.panel.open();
    $(this.itemsContainer).addClass('expanded');
    
    return this;
};

MultipleItemField.prototype.createControlObject = function () {
    var ctrlObj = document.createElement('input');

    ctrlObj.className = 'multiple-item-input';

    return ctrlObj;
};

MultipleItemField.prototype.attachListeners = function () {
    var controlObject, itemsContainer, that = this;

    controlObject = this.controlObject;
    itemsContainer = this.itemsContainer;
    
    $(this.itemsContainer).on("click", function (e) {
        e.stopPropagation();
        $(controlObject).focus().select();
    });

    $(controlObject).focus(function (e) {
        that.scrollTo();
        $(itemsContainer).addClass('focused');
        $(that.panel.html).addClass('focused');
    }).blur(function () {
        that.hidePanel();
        $(itemsContainer).removeClass('focused');
        $(that.panel.html).removeClass('focused');
    }).on('keyup', function (e) {
        if(e.keyCode === 27) {
            that.hidePanel();
        }
    }).on('keydown', function (e) {
        e.stopPropagation();
    });

    return this;
};

MultipleItemField.prototype.createHTML = function () {
    var fieldLabel, textInput, required = '', 
        style, i, that = this, itemsContainer, controlObject;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    itemsContainer = this.createHTMLElement('ul');
    itemsContainer.className = 'multiple-item-container';
    itemsContainer.id = this.name;

    if (this.fieldWidth && this.fieldHeight) {
        style = document.createAttribute('style');
        if (this.fieldWidth) {
            style.value += 'width: ' + this.fieldWidth + 'px; ';
        }
        if (this.fieldHeight) {
            style.value += 'height: ' + this.fieldHeight + 'px; ';
        }
        itemsContainer.setAttributeNode(style);
    }

    for (i = 0; i < this.items.length; i += 1) {
        itemsContainer.appendChild(this.items[i].getHTML());
    }

    controlObject = this.createControlObject();
    itemsContainer.appendChild(controlObject);

    this.html.appendChild(itemsContainer);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.itemsContainer = itemsContainer;
    this.controlObject = controlObject;

    $(this.parent.body).on('scroll', function () {
        that.hidePanel();
    });

    this.panel.belongsTo = this.itemsContainer;
    this.parent.parent.html.appendChild(this.panel.getHTML());

    return this.html;
};

MultipleItemField.prototype.evalRequired = function () {
    var res = true;
    if (this.required) {
        res = (this.items.length > 0);
        if (!res) {
            $(this.itemsContainer).addClass('required');
        } else {
            $(this.itemsContainer).removeClass('required');
        }
    }
    return res;
};

MultipleItemField.prototype.getObject = function () {
    var i, obj = [];

    for(i = 0; i < this.items.length; i++) {
        obj.push(this.items[i].getObject());
    }

    return obj;
};

MultipleItemField.prototype.getObjectValue = function () {
    this.value = JSON.stringify(this.getObject());
    return Field.prototype.getObjectValue.call(this);
};

//MultipleItemPanel

    var MultipleItemPanel = function(settings) {
        Element.call(this, {
            style: {
                cssProperties: {
                    "display": "none"
                },
                cssClasses: [
                    "multiple-item-panel"
                ]
            }
        });

        this.belongsTo = null;
        this.isOpen = null;
        this.buttons = null;
        this.subpanels = null;
        this.matchParentWidth = null;

        MultipleItemPanel.prototype.initObject.call(this, settings);
    };

    MultipleItemPanel.prototype = new Element();

    MultipleItemPanel.prototype.type = "MultipleItemPanel";

    MultipleItemPanel.prototype.initObject = function (settings) {
        var defaults = {
            belongsTo: null,
            buttons: [],
            matchParentWidth: true,
            width: 200
        };

        $.extend(true, defaults, settings);

        this.belongsTo = defaults.belongsTo;
        this.buttons = [];
        this.subpanels = [];
        this.isOpen = false;
        this.width = defaults.width;
        this.matchParentWidth = defaults.matchParentWidth;

        this.setButtons(defaults.buttons);
    };

    MultipleItemPanel.prototype.refreshPosition = function () {
        var pos, margin = {
            top: 0,
            left: 0
        };

        if(this.html){
            pos = getRelativePosition(this.belongsTo, this.html.parentElement);
            if(this.html.parentElement.style.position !== 'absolute') {
                margin.top = parseInt($(this.html.parentElement).css("margin-top"), 10);
                margin.left = parseInt($(this.html.parentElement).css("margin-left"), 10);
            }

            if(this.matchParentWidth) {
                this.setWidth($(this.belongsTo).outerWidth() - 2);   
            }
            this.setY((pos.top + margin.top -1 + $(this.belongsTo).outerHeight()));
            this.setX(pos.left + margin.left);
            this.style.addProperties({
                "display": "none",
                "zIndex": 999
            });
        }

        return this;
    };

    MultipleItemPanel.prototype.setBelongsTo = function (element) {
        this.belongsTo = element;

        this.refreshPosition();

        return this;
    };

    MultipleItemPanel.prototype.createSubpanel = function (settings, type) {
        var subpanel;

        switch(type) {
            case 'list':
                subpanel = new MultipleItemListSubpanel(settings);
        }

        return subpanel;
    };

    MultipleItemPanel.prototype.createButtonPanel = function (settings) {
        return new MultipleItemButtonPanel(settings);
    };

    MultipleItemPanel.prototype.showSubPanel = function (p) {
        if(this.html) {
            this.html.appendChild(p.getHTML());
        }
    };

    MultipleItemPanel.prototype.addSubpanel = function (subpanel, type) {
        var newSubpanel;
        if(subpanel instanceof MultipleItemSubpanel) {
            newSubpanel = subpanel;
        } else {
            newSubpanel = this.createSubpanel(subpanel, type);
        }

        this.subpanels.push(newSubpanel);

        if(this.html) {
            this.html.appendChild(newSubpanel.getHTML());
        }

        return  this;
    };

    MultipleItemPanel.prototype.addButton = function (button) {
        var buttonHTML, span;

        if(typeof button.caption === 'string') {
            buttonHTML = new Element({
                style: {
                    cssClasses: [
                        'multiple-item-panel-button'
                    ],
                    cssProperties: {
                        position: "relative",
                        width: "auto",
                        height: "auto"
                    }
                }
            });

            if(typeof button.onClick === 'function') {
                $(buttonHTML.getHTML()).on("mousedown", function(e){
                    e.stopPropagation();
                    if (e.button !== 0 && e.button !== undefined) {
                        return;
                    }
                    button.onClick.call(buttonHTML, e);
                });
            } else {
                $(buttonHTML).on("mousedown", function (e) {
                    e.stopPropagation();
                });
            }

            buttonHTML.getHTML().style.position = 'relative';
            buttonHTML.getHTML().style.width = 'auto';
            buttonHTML.getHTML().style.height = 'auto';
            span = buttonHTML.createHTMLElement('span');
            span.innerHTML = button.caption;
            buttonHTML.getHTML().appendChild(span);

            buttonHTML.data = button.data;
            buttonHTML.name = button.caption;

            this.buttons.push(buttonHTML);

            if(this.html) {
                this.html.appendChild(buttonHTML.getHTML());
            }
        }

        return this;
    };

    MultipleItemPanel.prototype.setButtons = function (buttons) {
        var i;

        for(i = 0; i < buttons.length; i++) {
            this.addButton(buttons[i]);
        }

        return this;
    };

    MultipleItemPanel.prototype.clear = function () {
        this.buttons = [];
        this.subpanels = [];
        $(this.html).empty();

        return this;
    };

    MultipleItemPanel.prototype.createHTML = function () {
        var html = Element.prototype.createHTML.call(this),
            i;

        html.style.height = 'auto';

        for(i = 0; i < this.subpanels.length; i++) {
            this.html.appendChild(this.subpanels[i].getHTML());
        }

        $(this.html).on("mousedown", function(e){
            e.stopPropagation();
        }).on("click", function(e){
            e.stopPropagation();
        }).on("mouseup", function(e){
            e.stopPropagation();
        });

        if(!this.matchParentWidth) {
            html.style.width = this.width + "em";
        }

        return html;
    };

    MultipleItemPanel.prototype.open = function () {
        var html = this.html, pos, parentNode = this.parentNode, i;

        if(this.isOpen || !this.belongsTo) {
            return this;
        }

        if(!html) {
            html = this.getHTML();
        }

        this.refreshPosition();

        for(i = 0; i < this.buttons.length; i++) {
            this.html.appendChild(this.buttons[i].getHTML());
        }

        this.isOpen = true;
        $(this.html).slideDown();

        return this;
    };

    MultipleItemPanel.prototype.close = function () {
        this.isOpen = false;
        $(this.html).hide();
        return this;
    };

    MultipleItemPanel.prototype.remove = function() {
        $(this.html).remove();
        delete this.subpanels;
        delete this.belongsTo;
        delete this.isOpen;
        delete this.buttons;
        delete this.matchParentWidth;
    };

//SubPanel

    var MultipleItemSubpanel = function (settings) {
        Element.call(this);
        this.header = null;
        this.title = null;
        this.content = null;
        this.collapsable = null;
        this.visibleHeader = null;
        this.showContentOnStart = null;
        this.onOpen = null;
        this.onClose = null;

        MultipleItemSubpanel.prototype.initObject.call(this, settings);
    };

    MultipleItemSubpanel.prototype = new Element();

    MultipleItemSubpanel.prototype.type = "mutlipleItemSubPanel";

    MultipleItemSubpanel.prototype.initObject = function (settings) {
        var defaults = {
            title: "",
            collapsable: false,
            showContentOnStart: false,
            visibleHeader: true,
            onOpen: null,
            onClose: null
        };

        $.extend(true, defaults, settings);

        this.showContentOnStart = defaults.showContentOnStart;
        this.onOpen = defaults.onOpen;
        this.onClose = defaults.onClose;

        this.setTitle(defaults.title)
            .setIsCollapsable(defaults.collapsable)
            .isVisibleHeader(defaults.visibleHeader);
    };

    MultipleItemSubpanel.prototype.isVisibleHeader = function (visible) {
        this.visibleHeader = visible;

        if(this.header) {
            this.header.style.display = visible ? 'block' : 'none';
        }

        return this;
    };

    MultipleItemSubpanel.prototype.setTitle = function (title) {
        this.title = title;
        if(this.header) {
            $(this.header).find('.header-text').text(this.title);
        }

        return this;
    };

    MultipleItemSubpanel.prototype.setIsCollapsable = function (boolean) {
         if(typeof boolean === 'boolean') {
            this.collapsable = boolean;
         }

         return this;
    };

    MultipleItemSubpanel.prototype.reset = function () {};

    MultipleItemSubpanel.prototype.close = function () {
        var that = this;

        this.isOpen = false;

        $(this.html).removeClass('opened').find('.header .bullet').removeClass("adam-menu-icon-arrow-down");
        $(this.content).slideUp(function() {
            that.reset();
        });

        if(typeof this.onClose === 'function') {
            this.onClose.call(this);
        }

        return this;
    };

    MultipleItemSubpanel.prototype.open = function () {
        var loader = document.createElement('div'),
            $content = $(this.content),
            aux, i, field, that = this;

        this.isOpen = true;

        if($content.css("display") !== 'none') {
            return this;
        }

        $(this.html).addClass('opened').find('.header .bullet').addClass("adam-menu-icon-arrow-down");
        if(this.displayFormOnStart) {
            $(this.content).css("display", "block");
        } else {
            $(this.content).slideDown();
        }

        if(typeof this.onOpen === 'function') {
            this.onOpen.call(this);
        }

        return this;
    };

    MultipleItemSubpanel.prototype.attachListeners = function () {
        var that = this;
        $(this.header).on("mousedown", function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (e.button !== 0 && e.button !== undefined) {
                return;
            }
            if(that.isOpen) {
                that.close();
            } else {
                that.open();
            }
        });

        return this;
    };

    MultipleItemSubpanel.prototype.hideLoader = function () {
        if(this.content) {
            $(this.content).find('.loader').remove();
        }

        return this;
    };

    MultipleItemSubpanel.prototype.showLoader = function () {
        var msg;
        if(this.content) {
            if($(this.content).find(".loader").get(0)) {
                return this;
            } 

            msg = document.createElement("div");
            msg.className = "loader";
            msg.appendChild(document.createTextNode("loading..."));

            $(this.content).prepend(msg);
        }

        return this;
    };

    MultipleItemSubpanel.prototype.createHTML = function () {
        var header, aux, content;
        if(!this.html) {
            Element.prototype.createHTML.call(this);
            this.html.style.height = 'auto';
            this.html.style.width = 'auto';
            this.html.style.position = "relative";
            this.html.className = 'multiple-item-subpanel';

            header = this.createHTMLElement('div');
            header.className = "header";
            content = this.createHTMLElement('div');
            content.className = 'content';

            if(!this.visibleHeader) {
                header.style.display = 'none';
            }

            if(this.collapsable) {
                aux = this.createHTMLElement('span');
                aux.className = "adam-menu-icon-arrow-right bullet";
                content.style.display = 'none';
                header.appendChild(aux);
            }

            aux = this.createHTMLElement("span");
            aux.className = "header-text";
            aux.appendChild(document.createTextNode(this.title));

            header.appendChild(aux);

            this.header = header;
            this.content = content;
            this.html.appendChild(header);
            this.html.appendChild(content);

            this.attachListeners();

            if(this.showContentOnStart) {
                $(header).trigger("mousedown");
            }
        }

        return this.html;
    };

//SubPanelList
    var MultipleItemListSubpanel = function (settings) {
        MultipleItemSubpanel.call(this, settings);
        this.items = null;
        this.listMaxHeight = null;
        this.onItemSelect = null;
        MultipleItemListSubpanel.prototype.initObject.call(this, settings);
    };

    MultipleItemListSubpanel.prototype = new MultipleItemSubpanel();

    MultipleItemListSubpanel.prototype.initObject = function (settings) {
        var defaults = {
            items: [], 
            listMaxHeight: null, 
            onItemSelect: null
        };

        this.items = [];
        this.listMaxHeight = defaults.listMaxHeight;
        this.onItemSelect = settings.onItemSelect;

        $.extend(true, defaults, settings);

        this.setItems(defaults.items);
    };

    MultipleItemListSubpanel.prototype.selectItem = function (i) {
        if(this.html) {
            $(this.html).find('li:eq(' + i + ')').trigger("mousedown");
        }

        return this;
    };

    MultipleItemListSubpanel.prototype.setItems = function (items) {
        var i;
        if(items.push) {
            for(i = 0; i < items.length; i++) {
                this.addItem(items[i]);
            }
        }

        return this;
    };

    MultipleItemListSubpanel.prototype.displayItem = function (item) {
        var list, li, that = this, defaultData, label = item.label || item.text;
        if(this.html) {
            defaultData = {
                label: label
            };
            list = $(this.html).find("ul");
            li = $(document.createElement("li"));
            li.html(label || "untitled");
            li.data("value", item.value || null);
            li.data("label", label);
            li.data("data", $.extend(true, defaultData, item.data));

            if(typeof this.onItemSelect === 'function') {
                li.on("mousedown", function(e) {
                    e.stopPropagation();
                    if (e.button !== 0 && e.button !== undefined) {
                        return;
                    }
                    that.onItemSelect.call(that, $(this).data("value"), $(this).data("data"));
                });
            }

            list.append(li);
        }

        return this;
    };

    MultipleItemListSubpanel.prototype.clear = function () {
        this.items = [];
        $(this.content).find("ul").empty();

        return this;
    };

    MultipleItemListSubpanel.prototype.addItem = function (item) {
        this.items.push(item);
        this.displayItem(item);

        return this;
    };

    MultipleItemListSubpanel.prototype.createHTML = function() {
        var i, ul;
        MultipleItemSubpanel.prototype.createHTML.call(this);

        ul = this.createHTMLElement("ul");
        ul.className = 'multiple-item-list';
        ul.style.height = 'auto';
        ul.style.overflow = 'auto';
        if(this.listMaxHeight) {
            $(ul).css("max-height", this.listMaxHeight);
        }
        this.content.appendChild(ul);

        for(i = 0; i < this.items.length; i++) {
            this.displayItem(this.items[i]);
        }

        return this.html;
    };

//ButtonPanel
    var MultipleItemButtonPanel = function (settings) {
        Element.call(this, {
            style: {
                cssClasses: ["multiple-item-button-panel"]
            }
        });
        this.buttons = null;
        this.buttonsContainer = null;
        this.label = null;
        this.fallbackOnClickHandler = null;
        MultipleItemButtonPanel.prototype.initObject.call(this, settings);
    };

    MultipleItemButtonPanel.prototype = new Element();

    MultipleItemButtonPanel.prototype.initObject = function (settings) {
        var defaults = {
            buttons: [],
            label: "[Button Panel]",
            fallbackOnClickHandler: null
        };

        $.extend(true, defaults, settings);

        this.buttons = [];
        this.fallbackOnClickHandler = defaults.fallbackOnClickHandler;

        this.setButtons(defaults.buttons)
            .setLabel(defaults.label);
    };

    MultipleItemButtonPanel.prototype.setLabel = function (label) {
        this.label = label;
        if(this.html) {
            $(this.html).find(".label").text(label);
        }

        return this;
    };

    MultipleItemButtonPanel.prototype.getButtonHTML = function (button) {
        var element;

        if(button.html) {
            return button.html;
        }

        element = this.createHTMLElement("button");
        element.value = button.value;
        element.appendChild(document.createTextNode(button.caption));
        if(typeof button.onClick === 'function') {
            $(element).on("mousedown", function(e){
                e.stopPropagation();
                if(e.button !== 0 && e.button !== undefined) {
                    return;
                } 
                button.onClick.call(button);
            });
        };
        button.html = element;

        return button.html;            
    };

    MultipleItemButtonPanel.prototype.addButton = function (button) {
        var element;

        if(typeof button === 'object') {
            if(!button.onClick) {
                button.onClick = this.fallbackOnClickHandler;
            }
            this.buttons.push(button);
            
            if(this.buttonsContainer) {
                this.buttonsContainer.appendChild(this.getButtonHTML(button));
            }
        }

        return this;
    };

    MultipleItemButtonPanel.prototype.setButtons = function (buttons) {
        var i;

        this.buttons = [];

        if(this.buttonsContainer) {
            $(this.buttonsContainer).empty();
        }

        for(i = 0; i< buttons.length; i++) {
            this.addButton(buttons[i]);
        }

        return this;
    };

    MultipleItemButtonPanel.prototype.createHTML = function () {
        var i = 0, label, container;

        if(this.html) {
            return this.html;
        }

        Element.prototype.createHTML.call(this);

        this.html.style.position = 'relative';
        this.html.style.height = 'auto';
        this.html.style.width = 'auto';
        this.html.style.display = 'block';

        if(this.label !== "") {
            label = this.createHTMLElement('span');
            label.className = 'label';
            label.appendChild(document.createTextNode(this.label));
        }

        container = this.createHTMLElement("div");
        container.className = 'container';

        for(i = 0; i < this.buttons.length; i++) {
            container.appendChild(this.getButtonHTML(this.buttons[i]));
        }

        this.buttonsContainer = container;

        if(label) {
            this.html.appendChild(label);   
        }
        this.html.appendChild(container);

        return this.html;
    };

//Single Item
    var SingleItem = function (options) {
        Element.call(this, options);
        this.label = null;
        this.value = null;
        this.onRemove = null;
        this.onClick = null;
        this.showValueTooptip = null;
        this.panel = null;
        this.editable = null;
        this.onChange = null;
        this.inEditMode = null;
        this.onEdit = null;
        this.data = null;

        SingleItem.prototype.initObject.call(this, options);
    };

    SingleItem.prototype = new Element();

    SingleItem.prototype.type = 'SingleItem';

    SingleItem.prototype.family = 'SingleItem';

    SingleItem.prototype.initObject = function (options) {
        var defaults = {
            label: "",
            value: null,
            showValueTooptip: true,
            editable: false,
            //panel: null,
            data: {},
            onClick: null,
            onChange: null,
            onEdit: null,
            onRemove: function () {}
        };

        $.extend(true, defaults, options);

        this.onRemove = defaults.onRemove;
        this.onClick = defaults.onClick;
        this.onChange = defaults.onChange;
        this.inEditMode = false;
        this.data = typeof defaults.data === 'object' ? defaults.data : {};

        this.setLabel(defaults.label)
            .setShowValueToolTip(defaults.showValueTooptip)
            .setValue(defaults.value)
            .setIsEditable(defaults.editable)
            .setOnEditHandler(defaults.onEdit);
    };
    /*
    SingleItem.prototype.setOnClickHandler = function(handler) {
        if(typeof handler === 'function') {
            this.onClick = handler;
            if(this.html) {
                this.html.style.cursor = 'pointer';
            }
        } else {
            this.onClick = null;
            if(this.html) {
                this.html.style.cursor = 'default';
            }
        }

        return this;
    };*/
    /*
    SingleItem.prototype.setIconVisible = function (visible) {
        this.showIcon = visible;
        this.updateHTML();
        return this;
    };*/
    /*
    SingleItem.prototype.setIconClasses = function(classes) {
        this.iconClasses = classes;
        this.updateHTML();
        return this;
    };*/

    SingleItem.prototype.setOnEditHandler = function (handlerFunction) {
        delete this.setOnEditHandler;
        if(typeof handlerFunction === 'function') {
            this.onEdit = handlerFunction;
        } else {
            this.onEdit = null;
        }

        this.refreshCursor();

        return this;
    };

    SingleItem.prototype.refreshCursor = function() {
        if(this.html) {
            this.html.style.cursor = this.editable ? 'pointer' : (typeof this.onClick === 'function' ? 'pointer' : 'default');
        }

        return this;
    };

    SingleItem.prototype.setIsEditable = function(editable) {
        this.editable = !!editable;

        return this.refreshCursor();
    };

    SingleItem.prototype.setValue = function (value) {
        this.value = value;
        return this.updateHTML();    
    };

    SingleItem.prototype.getValue = function () {
        return this.value;
    };

    SingleItem.prototype.setLabel = function (label) {
        this.label = label;
        return this.updateHTML();
    };

    SingleItem.prototype.getLabel = function () {
        return this.label;
    };

    SingleItem.prototype.setData = function (key, value) {
        this.data[key] = value;
    };

    SingleItem.prototype.getData = function (key) {
        if(key) {
            return this.data[key];
        } else {
            return this.data;
        }
    };

    SingleItem.prototype.showCloseButton = function () {
        if(this.html) {
            $(this.html).find('.multiple-item-close').css("visibility", "visible");
        }

        return this;
    };

    SingleItem.prototype.hideCloseButton = function () {
        if(this.html) {
            $(this.html).find('.multiple-item-close').css("visibility", "hidden");
        }

        return this;
    };

    SingleItem.prototype.setShowValueToolTip = function (show) {
        if(typeof show === 'boolean') {
            this.showValueTooptip = show;
        }
        return this;
    };

    SingleItem.prototype.updateHTML = function () {
        if(this.html) {
            $(this.html).find('span.label').text(this.getLabel());

            if (this.showValueTooptip && this.value) {
                this.html.setAttribute("title", this.value);
            }

            $(this.html).find('.multiple-item-icon').removeClass()
                .addClass('multiple-item-icon').addClass(this.iconClasses)
                .css("display", this.showIcon ? 'inline-block' : 'none');
        }
        return this;
    };

    SingleItem.prototype.edit = function() {};

    SingleItem.prototype.prepareEditionPanel = function() {};

    SingleItem.prototype.exitEditMode = function() {
        if(!this.inEditMode) {
            return this;
        }
        this.showCloseButton()
            .panel.close();
        $(this.html).removeClass('expanded');
        this.inEditMode = false;
        return this;
    };
    
    SingleItem.prototype.onClickHandler = function () {
        var that = this;

        return function (e) {
            e.stopPropagation();
            if(that.editable) {
                if(!that.inEditMode) {
                    if(!that.panel) {
                        that.panel = new MultipleItemPanel({
                            belongsTo: that.html,
                            width: 50
                        });
                    }
                    that.prepareEditionPanel()
                        .hideCloseButton()
                        .html.parentElement.parentElement.appendChild(that.panel.getHTML());
                    $(that.html).addClass('expanded');
                    that.inEditMode = true;
                    that.panel.open();
                    if(typeof that.onEdit === 'function') {
                        that.onEdit.call(that);
                    }
                } else {
                    that.exitEditMode();
                }
            } else {
                if(typeof that.onClick === 'function') {
                    that.onClick.call(that);
                }
            }
        };
    };

    SingleItem.prototype.createHTML = function () {
        if(this.html) {
            return this.html;
        }
        var item = document.createElement('li'),
            itemName = document.createElement('span'),
            that = this;

        /*itemName.className = 'multiple-item-icon';
        if(this.iconClasses) {
            $(itemName).addClass(this.iconClasses);
        }
        if(!this.showIcon) {
            itemName.style.display = 'none';
        }
        item.appendChild(itemName);

        itemName = document.createElement('span');*/
        itemName.className = "label";
        itemName.innerHTML = this.getLabel() || "";

        item.setAttribute("id", this.id);
        if (this.showValueTooptip && this.value) {
            item.setAttribute("title", this.value);
        }

        item.appendChild(itemName);

        close = document.createElement('a');
        close.href = "javascript: ;";
        close.className = 'multiple-item-close';
        $(close).on("click", function (e) {
            e.stopPropagation();
            $(that.html).remove();
            if(typeof that.onRemove === 'function') {
                that.onRemove.call(that);
            }
        });

        item.appendChild(close);

        this.html = item;
        $(item).on("click", that.onClickHandler());
        this.refreshCursor();

        return this.html;
    };

    SingleItem.prototype.getObject = function () {
        return {
            label: this.label,
            value: this.value
        };
    };
var EmailPickerField = function (options, parent) {
    MultipleItemField.call(this, options, parent);
    this.keyDelay = null;
    this.timer = null;
    this.groups = null;
    this.selectedHandler = null;
    this.searchValue = null;
    this.nameField = null;
    this.valueField = null;
    this.suggestionsPanel;
    EmailPickerField.prototype.initObject.call(this, options);
};

EmailPickerField.prototype = new MultipleItemField();

EmailPickerField.prototype.type = 'EmailPickerField';

EmailPickerField.prototype.initObject = function (options) {
    var defaults = {
            keyDelay: 500,
            nameField: 'text',
            valueField: 'value',
            groups: [],
            showValue: true
        },
        groupDefaults = {
            nameField: 'text',
            valueField: 'value',
            showValue: true
        },
        i;

    $.extend(true, defaults, options);

    this.groups = new jCore.ArrayList();

    for (i = 0; i < defaults.groups.length; i += 1) {
        defaults.groups[i] = $.extend({}, groupDefaults, defaults.groups[i]);
    }

    this.setKeyDelay(defaults.keyDelay)
        .setGroups(defaults.groups)
        .setNameField(defaults.nameField)
        .setValueField(defaults.valueField);
};

EmailPickerField.prototype.setNameField = function (fieldName) {
    this.nameField = fieldName;
    return this;
};

EmailPickerField.prototype.getNameField = function () {
    return this.nameField;
};

EmailPickerField.prototype.setValueField = function (fieldName) {
    this.valueField = fieldName;
    return this;
};

EmailPickerField.prototype.getValueField = function () {
    return this.valueField;
};

EmailPickerField.prototype.setGroups = function (groups) {
    var i;

    for(i = 0; i < groups.length; i ++) {
        this.groups.insert(groups[i]);
    }

    return this;
};

EmailPickerField.prototype.setKeyDelay = function (milliseconds) {
    this.keyDelay = milliseconds;
    return this;
};

EmailPickerField.prototype.hidePanel = function () {
    MultipleItemField.prototype.hidePanel.call(this);
    this.clearSelectedHandler();
    this.panel.clear();
};

EmailPickerField.prototype.clearInput = function () {
    this.searchValue = $(this.controlObject).val("").val();
    return this;
};

EmailPickerField.prototype.addItem = function (item) {
    var size = this.items.length, that = this;

    if(typeof item === 'object') {
        item.label = item.label || item.name;
        item.value = item.value || item.emailAddress;
    }

    MultipleItemField.prototype.addItem.call(this, item);

    if(size + 1 !== this.items.length) {
        return this;
    }

    item = this.items[size];

    if(!item.getValue()) {
        item.onClick = function() {
            that.clearSelectedHandler();
            that.selectedHandler = this;
            $(this.html).addClass("focused");
            that.loadGroupSuggestions(this.getData('groupName'));
        };
    }

    $(this.controlObject).val("").focus().select();
    this.hidePanel();

    return this;
};

EmailPickerField.prototype.processInputValue = function (value) {
    var flag = true, aux, i;
    aux = this.suggestionsPanel.items;
    for (i = 0; i < aux.length; i += 1) {
        if ($.trim(aux[i].value) === value) {
            this.suggestionsPanel.selectItem(i);
            return;
        }
    }

    if (!/^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/.test(value)) {
        flag = false;
    }

    if (flag) {
        this.addItem({
            label: value,
            value: value
        });
    }

    return flag;
};

EmailPickerField.prototype.hideSuggestionsList = function () {
    if(this.suggestionsPanel) {
        $(this.suggestionsPanel.html).remove();    
    }
    
    return this;
};

EmailPickerField.prototype.attachListeners = function () {
    MultipleItemField.prototype.attachListeners.call(this);
    var control, self = this;

    control = $(this.controlObject);
    control.blur(function (e) {
        self.clearInput();
    }).on("click", function (e) {
        self.clearSelectedHandler();
        self.hideSuggestionsList();
        self.showPanel();
    }).on("keyup", function (e) {
        e.stopPropagation();
        var aux, trimmedValue;
        trimmedValue = $.trim(this.value);
        clearInterval(self.timer);
        if (e.keyCode === 13) {
            if (trimmedValue) {
                if (!self.processInputValue(trimmedValue)) {
                    $(self.controlObject).focus().select();
                }
            } else {
                self.showPanel(['groups']);
            }
        } else if (e.keyCode === 27) {
            $(self.controlObject).val("");
            self.hidePanel();
        } else {
            if (trimmedValue && trimmedValue !== self.searchValue && self.proxy) {
                self.showPanel(['groups', 'suggestions']);
                self.timer = setInterval(function () {
                    clearInterval(self.timer);
                    if (trimmedValue) {
                        self.loadSuggestions(trimmedValue);
                        self.searchValue = trimmedValue;
                    }
                }, self.keyDelay);
            } else {
                self.hideSuggestionsList();
            }
        }
    });
};

EmailPickerField.prototype.onSuggestionSelectHandler = function () {
    var that = this;

    return function (a, b, c) {
        if(b.type === 'group' && that.selectedHandler) {
            that.selectedHandler.setValue(b.groupName);
            that.selectedHandler.setLabel(b.name);
            that.selectedHandler.onClick = null;
            that.hidePanel();
            that.onChange();
        } else {
            that.addItem({
                label: b.name,
                value: a
            });
        }
    };
};

EmailPickerField.prototype.showSuggestionsPanel = function () {
    var that = this;

    if(this.panel.subpanels.length === 0) {
        if(!this.suggestionsPanel) {
            this.suggestionsPanel = this.panel.createSubpanel({
                title: "Suggestions", 
                onItemSelect: that.onSuggestionSelectHandler(),
                listMaxHeight: 200
            }, "list");
        }
        this.panel.addSubpanel(this.suggestionsPanel);
    }

    this.suggestionsPanel.clear();
    this.panel.showSubPanel(this.suggestionsPanel);
    this.suggestionsPanel.showLoader();
    this.suggestionsPanel.setTitle("Suggestions");

    return this;
};

EmailPickerField.prototype.showGroupsList = function () {
    var i, g, that = this, size = this.groups.getSize(),
        helperFunction = function(target, gName) {
            var that = target;
            return function(){
                var css = [], icon = "", showIcon = false;

                if(!this.data.value) {
                    css = ["unset"];
                }

                if(this.data.name === 'Module') {
                    icon = 'icon-module';
                    showIcon = true;
                } else if(this.data.name === 'Team') {
                    icon = 'adam-menu-icon-group';
                    showIcon = true;
                }

                that.addItem(new EmailItem({
                    label: this.data.name,
                    value: this.data.value,
                    showIcon: showIcon,
                    iconClasses: icon,
                    data: {
                        proxy: this.data.proxy || null,
                        nameField: this.data.nameField,
                        valueField: this.data.valueField,
                        showValue: this.data.showValue || false,
                        groupName: gName
                    },
                    cssClasses: css
                }));
            }
        };

    if(this.panel.buttons.length > 0) {
        return this;
    }

    for (i = 0; i < size; i += 1) {
        g = this.groups.get(i);
        this.panel.addButton({
            caption: '<b>' + g.name + '</b>' + (g.proxy ? '&nbsp;<small>[configurable]</small>' : ''),
            data: {
                name: g.name,
                value: g.value || null,
                proxy: g.proxy,
                nameField: g.nameField,
                valueField: g.valueField,
                showValue: g.showValue
            },
            onClick: helperFunction(that, g.name)
        });
    }
    return this;
};

EmailPickerField.prototype.clearSelectedHandler = function () {
    this.selectedHandler = null;
    $(this.itemsContainer).find("> li.focused").removeClass("focused");
    return this;
};

EmailPickerField.prototype.showPanel = function (subpanels) {
    var i, suggestions = false;

    if(!subpanels || !subpanels.push) {
        subpanels = ["groups"];
    }

    for(i = 0; i < subpanels.length; i++) {
        switch(subpanels[i]) {
            case 'groups':
                this.showGroupsList();
                break;
            case 'suggestions':
                suggestions = true;
                this.showSuggestionsPanel();
        }
    }

    return MultipleItemField.prototype.showPanel.call(this);
};

EmailPickerField.prototype.fillSuggestionsList = function(response, settings) {
    var num = 0, i, label, items = [],
        defaultSettings =  {
            nameField: this.nameField,
            valueField: this.valueField,
            type: "single",
            showValue: true
        }, suggestionsPanel, num = 0;

    $.extend(true, defaultSettings, settings);

    suggestionsPanel = this.suggestionsPanel;
    suggestionsPanel.setTitle("Suggestions");

    if (response.result) {
        num = response.result.length;
        for (i = 0; i < num; i += 1) {
            label = '<b>' + response.result[i][defaultSettings.nameField] + "</b>";
            if (response.result[i][defaultSettings.valueField] &&  defaultSettings.showValue) {
                label += ("<br/>" + "<small>" + response.result[i][defaultSettings.valueField] + "</small>");
            }
            items.push({
                label: label,
                value: response.result[i][defaultSettings.valueField] || response.result[i][defaultSettings.nameField],
                data: {
                    name: response.result[i][defaultSettings.nameField],
                    type: defaultSettings.type,
                    groupName: (settings && settings.name) || null
                }
            });
        }
    }

    this.suggestionsPanel.hideLoader();
    if(defaultSettings.resultMessage) {
        this.suggestionsPanel.setTitle(defaultSettings.resultMessage + " (" + num + ")");
    } else {
        this.suggestionsPanel.setTitle(num + " suggestion(s) for \"" + response.search + "\"");    
    }
    this.suggestionsPanel.setItems(items);

    return this;
};

EmailPickerField.prototype.loadSuggestions = function (query, settings) {
    this.showPanel(['suggestions']);

    query = $.trim(query || "");
    if (query) {
        this.proxy.uid = query;
        this.fillSuggestionsList(this.proxy.getData({}), settings);
    }

    return this;
};

EmailPickerField.prototype.loadGroupSuggestions = function (group) {
    this.showPanel(['suggestions']);

    group = this.groups.find("name", group);

    this.fillSuggestionsList(group.proxy.getData({}), $.extend(true, {resultMessage: group.name+" list", type: "group"}, group));
};

EmailPickerField.prototype.isValid = function () {
    var i, res = true;

    for (i = 0; i < this.items.length; i += 1) {
        res = res && !!this.items[i].getValue();
        if (!res) {
            this.errorTooltip.setMessage("All the items must be properly set");
            $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
            $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
            return res;
        } else {
            $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
            $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
        }
    }

    res = res && Field.prototype.isValid.call(this);

    return res;
};

EmailPickerField.prototype.getObject = function () {
    var i, obj = [], aux;

    for(i = 0; i < this.items.length; i++) {
        aux = this.items[i].getObject();
        obj.push({
            name: aux.label,
            emailAddress: aux.value
        });
    }

    return obj;
};

    var EmailItem = function(settings) {
        SingleItem.call(this, settings);
        this.colorAlert = null;
        this.iconClasses = null;
        this.showIcon = null;
        this.emailItemType = null;
        EmailItem.prototype.initObject.call(this, settings);
    };

    EmailItem.prototype = new SingleItem();

    EmailItem.prototype.type = "EmailItem";

    EmailItem.prototype.emailType = {
        SINGLE: 0,
        GROUP: 1
    };

    EmailItem.prototype.initObject = function(settings) {
        var defaults = {
            colorAlert: true,
            iconClasses: null,
            showIcon: false,
            emailItemType: this.emailType.SINGLE
        };

        $.extend(true, defaults, settings);

        this.colorAlert = defaults.colorAlert;

        this.setIconClasses(defaults.iconClasses)
            .setIconVisible(defaults.showIcon);
    };

    EmailItem.prototype.setIconVisible = function (visible) {
        this.showIcon = visible;
        this.updateHTML();
        return this;
    };

    EmailItem.prototype.setIconClasses = function(classes) {
        this.iconClasses = classes;
        this.updateHTML();
        return this;
    };

    EmailItem.prototype.updateHTML = function() {
        SingleItem.prototype.updateHTML.call(this);

        if(this.colorAlert) {
            if(this.getValue() !== null) {
                $(this.html).removeClass("unset");
            } else {
                $(this.html).addClass("unset");
            }
        }

        return this;
    };

    EmailItem.prototype.createHTML = function() {
        var span;
        if(this.html) {
            return this.html;
        }

        SingleItem.prototype.createHTML.call(this);
        span = this.createHTMLElement('span');
        span.className = 'multiple-item-icon';
        if(this.iconClasses) {
            $(span).addClass(this.iconClasses);
        }
        if(!this.showIcon) {
            span.style.display = 'none';
        }
        $(this.html).prepend(span);

        return this.html;
    };
var ItemMatrixField = function (options, parent) {
    Field.call(this, options, parent);
    this.moduleName = null;
    this.lockedFields = [];
    this.terminateFields = {};
    this.fieldWidth = null;
    this.fieldHeight = null;
    this.keyDelay = null;
    this.selectedHandler = null;
    this.searchValue = null;
    this.visualStyle = null;
    this.nColumns = null;
    ItemMatrixField.prototype.initObject.call(this, options);
};

ItemMatrixField.prototype = new Field();

ItemMatrixField.prototype.initObject = function (options) {
    var defaults = {
        visualStyle : 'list',
        nColumns : 2
    };
    $.extend(true, defaults, options);
//    this.setItems(defaults.items)
    this.setFieldWidth(defaults.fieldWidth)
        .setFieldHeight(defaults.fieldHeight)
        .setName(defaults.name)
        .setVisualStyle(defaults.visualStyle)
        .setNColumns(defaults.nColumns);
//        .setValueField(defaults.valueField);
};

ItemMatrixField.prototype.createHTML = function () {
    var fieldLabel, required = '', checkContainer, style;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    if (this.visualStyle === 'list') {
        checkContainer = this.createHTMLElement('ul');
        checkContainer.className = 'adam-item-matrix';
    } else {
        checkContainer = this.createHTMLElement('div');
        checkContainer.className = 'adam-item-matrix table';
    }

    if (this.fieldWidth && this.fieldHeight) {
        style = document.createAttribute('style');
        if (this.fieldWidth) {
            style.value += 'width: ' + this.fieldWidth + 'px; ';
        }
        if (this.fieldHeight) {
            style.value += 'height: ' + this.fieldHeight + 'px; ';
        }
        style.value += 'display: inline-block; margin: 0; overflow: auto; padding: 3px;';

        checkContainer.setAttributeNode(style);
    }
    this.html.appendChild(checkContainer);

    this.controlObject = checkContainer;

    return this.html;
};

ItemMatrixField.prototype.attachListeners = function () {
    var self = this;
    $(this.controlObject).on('click', '.item-matrix-field', function (e) {
        if ($(this).is(":checked")) {
            self.addLockedFields($(this).attr('value'));
        } else {
            self.removeLockedFields($(this).attr('value'));
        }
    });
};

/* **** SETTERS **** */
ItemMatrixField.prototype.setFieldHeight = function (height) {
    this.fieldHeight = height;
    return this;
};

ItemMatrixField.prototype.setFieldWidth = function (width) {
    this.fieldWidth = width;
    return this;
};

ItemMatrixField.prototype.setNColumns = function (nColumns) {
    this.nColumns = nColumns;
    return this;
};

ItemMatrixField.prototype.setNameModule = function (moduleName) {
    this.nameModule = moduleName;
    return this;
};

ItemMatrixField.prototype.setLockedFields = function (lockedFields) {
    if (typeof lockedFields === 'object' && (lockedFields instanceof Array)) {
        this.lockedFields = lockedFields;
    }
    return this;
};

ItemMatrixField.prototype.setVisualStyle = function (vStyle) {
    this.visualStyle = vStyle;
    return this;
};

ItemMatrixField.prototype.addLockedFields = function (fieldName) {
    this.lockedFields.push(fieldName);
    return this;
};

ItemMatrixField.prototype.removeLockedFields = function (fieldName) {
    var index = this.lockedFields.indexOf(fieldName);
    this.lockedFields.splice(index, 1);
    return this;
};
/**
 * Sets the combo box options
 * @param {Array} data
 * @return {*}
 */
ItemMatrixField.prototype.setList = function (data, selected) {
    var i, opt = '';
    if (this.html) {
        $(this.controlObject).empty();
        this.lockedFields = [];
        if (this.visualStyle === 'table') {
            opt += '<div class="row">';
        }
        for (i = 0; i < data.length; i += 1) {
            opt += this.generateOption(data[i], selected);
            if ((i + 1) % this.nColumns === 0) {
                opt += '</div><div class="row">';
            }
        }
        if (this.visualStyle === 'table') {
            opt += '</div></div>';
        }
        this.controlObject.innerHTML = opt;
    }
    return this;
};

ItemMatrixField.prototype.generateOption = function (item, selected) {
    var out = '', value, text, i = 0, select;
    if (typeof item === 'object') {
        value = item.value;
        text = item.text;
    }
    if (typeof selected === 'object' && (selected instanceof Array)) {
        if (selected.indexOf(value) !== -1) {
            select = 'checked = "checked"';
        }
    }
    if (this.visualStyle === 'list') {
        out = '<li style="list-style-type: none;"><label><input type="checkbox" name="' + value + '" value="' + value + '" class="item-matrix-field" ' + select + '/> ' + text + '</label></li>';
    } else {
        out = '<div class="box cell"><label><input type="checkbox" name="' + value + '" value="' + value + '" class="item-matrix-field" ' + select + '/> ' + text + '</label></div>';;
    }
    return out;
};

/* **** GETTERS **** */
ItemMatrixField.prototype.getFieldHeight = function () {
    return this.fieldHeight;
};

ItemMatrixField.prototype.getFieldWidth = function () {
    return this.fieldWidth;
};

ItemMatrixField.prototype.getNameModule = function () {
    return this.nameModule;
};

ItemMatrixField.prototype.getLockedField = function () {
    return this.lockedFields;
};

ItemMatrixField.prototype.getObjectValue = function () {
    var e, auxValue = [];
    this.value = JSON.stringify(this.lockedFields);
    return Field.prototype.getObjectValue.call(this);
};
var ItemUpdaterField = function (options, parent) {
    Field.call(this, options, parent);
    this.fields = [];
    this.options = [];
    this.fieldHeight = null;
    this.visualObject = null;
    ItemUpdaterField.prototype.initObject.call(this, options);
};

ItemUpdaterField.prototype = new Field();
ItemUpdaterField.prototype.type = 'ItemUpdaterField';

ItemUpdaterField.prototype.initObject = function (options){
    var defaults = {
        fields: [],
        fieldHeight: null
    };
    $.extend(true, defaults, options);
    this.setFields(defaults.fields)
        .setFieldHeight(defaults.fieldHeight);
};

ItemUpdaterField.prototype.setFields = function (items) {
    var i, aItems = [], newItem;
    for (i = 0; i < items.length; i += 1) {
        if (items[i].type === 'FieldUpdater') {
            items[i].setParent(this);
            aItems.push(items[i]);
        } else {
            newItem = new FieldUpdater(item[i], this);
            aItems.push(newItem);
        }
    }
    this.fields = aItems;
    return this;
};

ItemUpdaterField.prototype.setFieldHeight = function (value) {
    this.fieldHeight = value;
    return this;
};

ItemUpdaterField.prototype.getObjectValue = function () {
    var f, auxValue = [];
    this.convertOptionsToFields();
    for (f = 0; f < this.fields.length; f += 1) {
        auxValue.push(this.fields[f].getJSONObject());
    }
    this.value = JSON.stringify(auxValue);
    return Field.prototype.getObjectValue.call(this);
};

ItemUpdaterField.prototype.convertOptionsToFields = function () {
    var fields = [], i;
    for (i = 0; i < this.options.length; i += 1) {
        if (this.options && this.options[i].active) {
            fields.push(new FieldUpdater(this.options[i], this));
        }
    }
    this.fields = fields;
    return this;
};

ItemUpdaterField.prototype.setOptions = function (data, opt) {
    var i, options = [], newOption, messageMap;
    if (data) {
        for (i = 0; i < data.length; i += 1) {
            if (data[i].type.toLowerCase() !== 'id') {
                if (data[i].type === 'FieldOption') {
                    newOption = data[i];
                } else {
                    messageMap = {
                        integer : 'Please enter only integer values',
                        checkbox : 'Please insert Checkbox values (0 or 1)',
                        textfield : 'Please insert text',
                        name: 'Please insert text',
                        date : 'Please insert only valid dates',
                        datetime:' Please insert only valid dates',
                        textarea : 'Please insert text',
                        phone: 'Please enter a valid Phone',
                        float: 'Please enter only valid float values',
                        decimal:'Please enter only valid decimal values',
                        url: 'Please enter only valid url'
                    };
                    newOption =  new FieldOption({
                        fieldId   : data[i].value,
                        fieldName : data[i].text,
                        fieldType : data[i].type.toLowerCase(),
                        validators: [
                            {
                                jtype: data[i].type.toLowerCase(),
                                errorMessage: messageMap [data[i].type.toLowerCase()]
                            }
                        ]
                    }, fields_updater);
                }
                options.push(newOption);
            }
        }
    }
    this.options = options;

    if (this.html) {
        this.visualObject.innerHTML = '';
        for (i = 0; i < this.options.length; i += 1) {
            insert = this.options[i].getHTML();
            if (i % 2 === 0) {
                insert.className += ' updater-inverse';
            }
            this.visualObject.appendChild(insert);
        }
    }
    return this;
};

ItemUpdaterField.prototype.createHTML = function () {
    var fieldLabel, required = '', criteriaContainer;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = this.parent.labelWidth;
    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    criteriaContainer = this.createHTMLElement('div');
    criteriaContainer.className = 'adam-item-updater table';
    criteriaContainer.id = this.id;

    if (this.fieldWidth || this.fieldHeight) {
        style = document.createAttribute('style');
        if (this.fieldWidth) {
            style.value += 'width: ' + this.fieldWidth + 'px; ';
        }
        if (this.fieldHeight) {
            style.value += 'height: ' + this.fieldHeight + 'px; ';
        }
        style.value += 'display: inline-block; margin: 0; overflow: auto; padding: 3px;';
        criteriaContainer.setAttributeNode(style);
    }

    for (i = 0; i < this.options.length; i += 1) {
        insert = this.options[i].getHTML();
        console.log( i % 2, 'aa');
        if (i % 2 === 0) {
            insert.className = insert.className + ' updater-inverse';
            console.log('entro');
        }
        criteriaContainer.appendChild(insert);
    }

    this.html.appendChild(criteriaContainer);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.visualObject = criteriaContainer;

    return this.html;
};

ItemUpdaterField.prototype.setValue = function (value) {
    this.value = value;
    if (this.options && this.options.length > 0) {
        try{
            fields = JSON.parse(value);
            if (fields && fields.length > 0) {
                for (i = 0; i < fields.length; i += 1) {
                    for (j = 0; j < this.options.length; j += 1) {
                        if (fields[i].field === this.options[j].fieldId) {
                            this.options[j].active = true;
                            this.options[j].checkboxControl.checked = true;
                            this.options[j].textControl.disabled = false;
                            this.options[j].textControl.value = fields[i].value;
                            this.options[j].fieldValue = fields[i].value;
                            this.options[j].value = fields[i].value;
                            if(this.options[j].fieldType === 'date' ||
                                this.options[j].fieldType === 'datetime') {
                                $(this.options[j].textControl)
                                    .datepicker( "option", {disabled: false});
                            }
                            //
                            break;
                        }
                    }
                }
            }
        } catch (e) {}
    }
    return this;
};

ItemUpdaterField.prototype.validate = function () {
    var i, valid = true, current;
    for (i = 0; i < this.options.length; i += 1) {
        if (this.options[i].checkboxControl.checked) {
            current = this.options[i].isValid();
            valid = valid && current;
            if (!current && this.options[i].errorTooltip) {
                $(this.options[i].errorTooltip.html).removeClass('adam-tooltip-error-off');
                $(this.options[i].errorTooltip.html).addClass('adam-tooltip-error-on');
            }
        }

    }
    return valid;
};

//

var FieldUpdater = function (options, parent) {
    Base.call(this, options);
    this.field = null;
    this.fieldName = null;
    this.value = null;
    this.parent = null;
    this.label = null;
    this.module = null;
    FieldUpdater.prototype.initObject.call(this, options, parent);
};

FieldUpdater.prototype = new Base();
FieldUpdater.prototype.type = "FieldUpdater";
FieldUpdater.prototype.initObject = function (options, parent) {
    if (options && options.type === 'FieldOption') {
        this.setField(options.fieldId)
            .setFieldName(options.fieldName)
            .setValue(options.fieldValue)
            .setParent(parent || null);
    } else {
        var defaults = {
            field: null,
            fieldName: null,
            value: null,
            label: null,
            module: null
        };
        $.extend(true, defaults, options);
        this.setField(defaults.field)
            .setFieldName(defaults.fieldName)
            .setValue(defaults.value)
            .setLabel(defaults.label)
            .setModule(defaults.module)
            .setParent(parent || null);
    }
};

FieldUpdater.prototype.setField = function (value, name) {
    this.field = value;
    if (typeof name !== 'undefined') {
        this.fieldName = name;
    }
    return this;
};

FieldUpdater.prototype.setFieldName = function (value) {
    this.fieldName = value;
    return this;
};

FieldUpdater.prototype.setValue = function (value) {
    this.value = value;
    return this;
};

FieldUpdater.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};

FieldUpdater.prototype.setLabel = function (label) {
    this.label = label;
    return this;
};

FieldUpdater.prototype.setModule = function (value) {
    this.module = value;
    return this;
};

FieldUpdater.prototype.getLabel = function () {
    var output;
    if (!this.label) {
        if (this.field && this.fieldName) {
            this.label = this.fieldName + ' = ' + "'" + this.value + "'";
        }
    }
    return this.label;
};

FieldUpdater.prototype.getJSONObject = function() {
    return {
        field: this.field,
        fieldName: this.fieldName,
        value: this.value
    };
};

//

var FieldOption = function (options, parent) {
    Element.call(this, options);
    /**
     * Defines the parent Form
     * @type {Form}
     */
    this.parent = null;
    /**
     * Defines the validator object
     * @type {Validator}
     */
    this.validators = [];

    this.active = null;
    this.fieldId = null;
    this.fieldName = null;
    this.fieldValue = null;
    this.checkboxControl = null;
    this.textControl = null;
    this.parent = null;
    FieldOption.prototype.initObject.call(this, options, parent);
};

FieldOption.prototype = new Element();
FieldOption.prototype.type = 'FieldOption';

FieldOption.prototype.initObject = function (options, parent) {
    var defaults;

    defaults = {
        active: false,
        fieldId: null,
        fieldValue: null,
        fieldName: null,
        fieldType: null,
        validators: [],
    };
    $.extend(true, defaults, options);
    this.setParent(parent);
    this.setActive(defaults.active)
        .setFieldId(defaults.fieldId)
        .setFieldName(defaults.fieldName)
        .setFieldValue(defaults.fieldValue)
        .setFieldType(defaults.fieldType)
        .setValidators(defaults.validators)
        .setMessageError(defaults.messageError)
        .setErrorTooltip({});
};

FieldOption.prototype.setActive = function (value) {
    this.active = value;
    return this;
};

FieldOption.prototype.setFieldId = function (value) {
    this.fieldId = value;
    return this;
};

FieldOption.prototype.setFieldName = function (value) {
    this.fieldName = value;
    return this;
};

FieldOption.prototype.setFieldValue = function (value) {
    this.fieldValue = value;
    this.value = value;
    return this;
};

FieldOption.prototype.setFieldType = function (value) {
    this.fieldType = value;
    return this;
};
FieldOption.prototype.setParent = function (value) {
    this.parent = value;
    return this;
};

FieldOption.prototype.createHTML = function () {
    var div,
        checkbox,
        label,
        edit,
        readAtt,
        disabledValue;
    Element.prototype.createHTML.call(this);
    this.style.removeProperties(['width', 'height', 'position', 'top', 'left', 'z-index']);
    this.style.width = '100%';
    this.style.addClasses(['row']);



    div = this.createHTMLElement('div');
    div.className = 'cell';
    div.style.width = '30%';
    checkbox = document.createElement('input');
    checkbox.id = "chk_" + this.id;
    checkbox.type = 'checkbox';
    checkbox.className = 'adam-updater-checkbox';
    div.appendChild(checkbox);

    this.checkboxControl = checkbox;
    label = document.createElement('span');
    label.innerHTML = this.fieldName;
    label.className = 'adam-updater-label';
    div.appendChild(label);
    this.html.appendChild(div);

    div = this.createHTMLElement('div');
    div.className = 'cell';
    div.style.width = '58%';

    edit = document.createElement('input');
    edit.id = "val_" + this.id;
    edit.type = 'text';
    edit.className = 'adam-updater-value';
    div.appendChild(edit);
    edit.value = this.fieldValue;
    readAtt = document.createAttribute('disabled');
    edit.setAttributeNode(readAtt);
    if (this.fieldType === 'password') {
        edit.type = 'password';
    }
    this.textControl = edit;
    if (this.fieldType === 'date') {
        $(edit).datepicker({
            showOn: 'button',
            constrainInput: false,
            disabled : true
        }).next('button').text('').button({icons:{primary : 'ui-icon-calendar'}});
    }
    if (this.fieldType === 'datetime') {
        $(edit).datetimepicker({
            showOn: 'button',
            constrainInput: false,
            disabled : true
        }).next('button').text('').button({icons:{primary : 'ui-icon-calendar'}});
    }


    this.html.appendChild(div);

    div = this.createHTMLElement('div');
    div.className = 'cell';
    div.style.width = '5%';
    if (this.errorTooltip) {
        div.appendChild(this.errorTooltip.getHTML());
    }
    this.html.appendChild(div);

    div = this.createHTMLElement('div');
    div.className = 'clear';
    this.html.appendChild(div);

    this.attachListeners();

    return this.html;
};

FieldOption.prototype.attachListeners = function () {
    var root = this;
    $(this.checkboxControl).click(function (e) {
        if (root.checkboxControl.checked) {
            root.textControl.disabled = false;
            root.setActive(true).setFieldValue(root.textControl.value);
            //console.log(root);
            if (root.fieldType  === 'date' || root.fieldType === 'datetime') {
                $(root.textControl).datepicker( "option", { disabled: false } );
            }


        } else {
            root.textControl.disabled = true;
            root.setActive(false);
            $(root.textControl).removeClass('required');
            $(root.errorTooltip.html).addClass('adam-tooltip-error-off');
            if (root.fieldType  === 'date' || root.fieldType === 'datetime') {
                $(root.textControl).datepicker( "option", { disabled: true } );
            }
        }
    });
    $(this.textControl).change(function (e) {
        root.setFieldValue(root.textControl.value);
    });
//    $(this.textControl).focusout(function () {
////        if (!root.isValid() && root.errorTooltip) {
////            $(root.errorTooltip.html).removeClass('adam-tooltip-error-off');
////            $(root.errorTooltip.html).addClass('adam-tooltip-error-on');
////        }
//    });


};

FieldOption.prototype.validate4 = function () {
    var response = true;
    response = (this.textControl.value !== null && this.textControl.value !== '');
    if (!response) {
        $(this.textControl).addClass('required');
    } else {
        $(this.textControl).removeClass('required');
    }
    return response;
};

/**
 * Sets the validator property
 * @param {Object/Validator} val
 * @return {*}
 */
FieldOption.prototype.setValidators = function (val) {
    var i;

    for (i = 0; i < val.length; i += 1) {
        if (val[i] && val[i].family && val[i].family === 'Validator') {
            this.validators.push(val[i]);
        } else {
            this.validators.push(this.validatorFactory(val[i]));
        }
    }
    return this;
};

/**
 * Returns a validator object
 * @param {Object} validator
 * @return {Validator}
 */
FieldOption.prototype.validatorFactory = function (validator) {
    var out = null,
        regexp = {
            email: {
                pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                message: 'You must enter a valid email'
            },
            integer: {
                pattern: /^\s*(\+|-)?\d+\s*$/,
                message: "You must enter an integer"
            },
            required: {
                pattern: /\S/,
                message: "This field is required"
            },
            phone: {
                pattern : /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/,
                message: 'You must enter a phone number'

            },
            float: {
                pattern : /^-?\d*(\.\d+)?$/,
                message: 'You must enter a float number'
            },
            url: {
                pattern: /^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,4}|travel)(:\d{2,5})?(\/.*)?$/i,
                message: 'You must enter a url direction'
            }
        };
    if (validator && validator.jtype) {
        switch (validator.jtype) {
            case 'required':
                if (validator.criteria && !validator.criteria.trim) {
                    /*validator.jtype = 'required_without_spaces';*/
                    /*} else {*/
                    out = new TextLengthValidator({
                        criteria: {
                            trim: false,
                            minLength: 1
                        },
                        errorMessage: validator.errorMessage || "this field is required"
                    }, this);
                }
                break;
            case 'email':
            case 'integer':
            case 'phone':
            case 'float':
            case 'url':
                validator.criteria = regexp[validator.jtype].pattern;
                out = new RegExpValidator($.extend({
                    errorMessage: regexp[validator.jtype].message
                }, validator), this);
                break;
            case 'comparison':
                out = new ComparisonValidator($.extend({
                    errorMessage: "The comparison failed"
                }, validator), this);
                break;
            case 'regexp':
                out = new RegExpValidator($.extend({
                    errorMessage: "The pattern text didn't match with the specified one"
                }, validator), this);
                break;
            case 'textLength':
                out = new TextLengthValidator($.extend({
                    errorMessage: "The text length must be "
                }, validator), this);
                break;
            case 'custom':
                out = new CustomValidator($.extend({
                    errorMessage: ""
                }, validator), this);
                break;
            case 'number':
                out = new NumberValidator($.extend({
                    errorMessage: ""
                }, validator), this);
                break;
            case 'range':
                out = new RangeValidator($.extend({
                    errorMessage: ""
                }, validator), this);
                break;
            case 'checkbox':
                validator.criteria = {
                    validationFunction : function (parent, p2) {

                        return (this.value === '0' || this.value === '1');
                    }
                };
                out = new CustomValidator($.extend({
                    errorMessage: ''
                }, validator), this);
                break;
            case 'decimal':
                validator.criteria = {
                    validationFunction : function (parent, p2) {
//                        return (!isNaN(this.value) && this.value.indexOf(".") >= 0);
                        if(isNaN(this.value) || this.value.indexOf(".") < 0) {
                            return false
                        }
                        else{
                            return true;
                        }
                    }
                };
                out = new CustomValidator($.extend({
                    errorMessage: ''
                }, validator), this);
                break;
            case 'textfield':
            case 'textarea':
            case 'name':
                validator.criteria = {
                    minLength: 1
                };
                out = new TextLengthValidator($.extend({
                    errorMessage: "The text length must be "
                }, validator), this);
                break;
            case 'date':
            case 'datetime':
                validator.criteria = {
                    validationFunction : function (parent, p2) {
                        date = Date.parse(this.value);
                        if (date !== null) {
                            //date_string.addClass("accept").text(date.toString("dddd, MMMM dd, yyyy h:mm:ss tt"));
                            return true;
                        } else {
                            return false;
                            // date_string.addClass("error").text(messages[Math.round(messages.length * Math.random())] + "...");
                        }

                    }
                };
                out = new CustomValidator($.extend({
                    errorMessage: ''
                }, validator), this);
                break;
            case 'password':
                validator.criteria = {
                    minLength: 8
                };
                out = new TextLengthValidator($.extend({
                    errorMessage: "The text length must be "
                }, validator), this);
                break;
                break;
            default:
                out = new Validator($.extend({
                    errorMessage: ""
                }, validator), this);
        }
    } else {
        out = new Validator(null, this);
    }
    return out;
};

/**
 * Sets the fields validation error message
 * @param {String} msg
 * @return {*}
 */
FieldOption.prototype.setMessageError = function (msg) {
    this.messageError = msg;
    return this;
};


FieldOption.prototype.setErrorTooltip = function (tooltip) {
    if (tooltip) {
        if (!tooltip.css) {
            tooltip.css = 'adam-tooltip-error-off';
        }
        if (!tooltip.icon) {
            tooltip.icon = 'adam-tooltip-icon-error';
        }
        tooltip.visible = false;
        this.errorTooltip = new Tooltip(tooltip, this);
    } else {
        this.errorTooltip = null;
    }
    return this;
};

FieldOption.prototype.isValid = function () {
    var i, res = true;

    for (i = 0; i < this.validators.length; i += 1) {
        res = res && this.validators[i].isValid();

        if (!res) {
            this.errorTooltip.setMessage(this.validators[i].getErrorMessage());
            $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
            $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
            return res;
        }
    }

    if (res) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
    }

    return res;
};



var HtmlPanel = function (options) {
    Panel.call(this, options);
    this.source = this;
    this.scroll = null;
    this.parent = null;
    HtmlPanel.prototype.initObject.call(this, options);
};

HtmlPanel.prototype = new Panel();

HtmlPanel.prototype.type = "HtmlPanel";

HtmlPanel.prototype.initObject = function (options) {
    var defaults = {
        source: null,
        scroll: true
    };
    $.extend(true, defaults, options);
    this.setSource(defaults.source)
        .setScroll(defaults.scroll);
};

HtmlPanel.prototype.setSource = function (source) {
    this.source = source;
    return this;
};

HtmlPanel.prototype.setScroll = function (value) {
    this.scroll = value;
    return this;
};

HtmlPanel.prototype.createHTML = function () {
    var HPDiv,
        scrollMode;
    Panel.prototype.createHTML.call(this);
    if (this.source) {
        scrollMode = (this.scroll) ? 'auto' : 'none';
        HPDiv = this.createHTMLElement('div');
        HPDiv.id = this.id;
        HPDiv.innerHTML = this.source;
//        HPDiv.style.overflow = scrollMode;
        this.body.appendChild(HPDiv);
        this.body.style.bottom = '8px';
    }
    this.attachListeners();
    return this.html;
};

HtmlPanel.prototype.setParent = function (parent) {
    this.parent = parent;
    return this;
};
HtmlPanel.prototype.attachListeners = function () {
    $(this.body).on('mousedown', function (e) {
        e.stopPropagation();
    });
};
var CriteriaField = function (settings, parent) {
	MultipleItemField.call(this, settings, parent);
    this.panels = null;
	this.editMode = false;
	this.expressionInEdition = null;
    this.currentIndex = null;
    this.viewingForm = null;
    this.panelSemaphore = true;
	CriteriaField.prototype.initObject.call(this, settings);
};

CriteriaField.prototype = new MultipleItemField();

CriteriaField.prototype.type = 'CriteriaField';

CriteriaField.prototype.initObject = function (settings) {
    var defaults = {
        value: null,
        panels: {
            logic: {
                enabled: true
            },
            group: {
                enabled: true
            },
            fieldEvaluation: {
                enabled: true,
                modulesProxy: new RestProxy({
                    url: SUGAR_URL + '/rest/v10/CrmData/related/',
                    restClient: settings.restClient || new RestClient(),
                    uid: PROJECT_MODULE,
                    callback: null
                }),
                fieldsProxy: new RestProxy({
                    url: SUGAR_URL + '/rest/v10/CrmData/fields/',
                    restClient: settings.restClient || new RestClient(),
                    uid: "",
                    callback: null
                })
            },
            businessRulesEvaluation: {
                enabled: true,
                proxy: new RestProxy({
                    url: SUGAR_URL + '/rest/v10/CrmData/businessrules/',
                    restClient: settings.restClient || new RestClient(),
                    uid: project.uid,
                    callback: null
                })
            },
            formResponseEvaluation: {
                enabled: true,
                proxy: new RestProxy({
                    url: SUGAR_URL + '/rest/v10/CrmData/activities/',
                    restClient: settings.restClient || new RestClient(),
                    uid: project.uid,
                    callback: null
                })
            }
        }
    };

    $.extend(true, defaults, settings);

    this.modulesProxy = defaults.modulesProxy;
    this.businessRulesProxy = defaults.businessRulesProxy;
    this.viewingForm = false;
    this.panels = defaults.panels;

    this.setModuleFieldsProxy(defaults.moduleFieldsProxy)
        .setControlFormsProxy(defaults.controlFormsProxy);
};

CriteriaField.prototype.setControlFormsProxy = function (proxy) {
    if(proxy instanceof RestProxy) {
        this.controlFormsProxy = proxy;
    }
    return this;
};

CriteriaField.prototype.setModuleFieldsProxy = function (proxy) {
    if(proxy instanceof RestProxy) {
        this.moduleFieldsProxy = proxy;
    }
    return this;
};

CriteriaField.prototype.showPanel = function () {
    var panel = this.panel, subPanel, i, subpanels;

    if(this.editMode) {
        for(i = 0; i < this.items.length; i++) {
            this.items[i].setIsEditMode(false);
        }
    } else if(this.panel.isOpen){
        return this;
    }

    panel.clear();

    subpanels = [
        "logic", 
        "group",
        "fieldEvaluation",
        "formResponseEvaluation",
        "businessRulesEvaluation"
    ];

    for(i = 0; i < subpanels.length; i++) {
        if(this.panels[subpanels[i]].enabled) {
            subPanel = null;
            switch(subpanels[i]) {
                case 'formResponseEvaluation':
                    subPanel = this.createControlPanel();
                    break;
                case 'fieldEvaluation':
                    subPanel = this.createModulesPanel();
                    break;
                case 'logic':
                    subPanel = this.createLogicPanel().getHTML();
                    break;
                case 'group':
                    subPanel = this.createGroupPanel().getHTML();
                    break;
                case 'businessRulesEvaluation':
                        subPanel = this.createBusinessRulePanel();
            }

            if(subPanel) {
                panel.html.appendChild(subPanel);
            }
        }
    }

    MultipleItemField.prototype.showPanel.call(this);
};

CriteriaField.prototype.showEditionPanel = function (settings) {
    var panel = this.panel, subPanel;

    panel.clear();

    $(panel.html).css("min-width", 210);

    if(!settings) {
        settings = {};
    }

    if(!settings.type) {
        return this;
    }

    switch(settings.type) {
        case 'control':
            subPanel = this.createControlPanel(settings.settings);
            break;
        case 'modules':
            subPanel = this.createModulesPanel(settings.settings);
            break;
        case 'business_rules':
            subPanel = this.createBusinessRulePanel(settings.settings);
    }

    if(subPanel) {
        panel.html.appendChild(subPanel);
    }

    MultipleItemField.prototype.showPanel.call(this);

    $(panel.html).removeClass('focused');
    $(this.itemsContainer).removeClass('focused expanded');

    return this;
};

CriteriaField.prototype.itemEditHandler = function () {
    var that = this,
        logicOptions = ["AND", "OR", "NOT"];

    return function () {
        var miniPanel, i, expression = this, values = {}, type;

        for(i = 0 ; i < that.items.length; i++) {
            if(that.items[i] !== this) {
                that.items[i].setIsEditMode(false);
            }
        }
        that.editMode = true;
        that.expressionInEdition = this;
        that.hidePanel();
        
        values.fields = []

        switch(this.expType) {
            case 'LOGIC':
                this.setOnExitEditHandler(function () {
                    $(miniPanel.html).remove();
                    delete miniPanel;
                    that.editMode = false;
                    that.expressionInEdition = null;
                    delete this.setOnExitEditHandler(null);
                });
                miniPanel = new MultipleItemPanel({
                    belongsTo: this.getHTML()
                });
                $(miniPanel.getHTML()).css('width', 'auto');
                for(i = 0; i < logicOptions.length; i++) {
                    if(logicOptions[i] !== this.expValue.value) {
                        miniPanel.addButton({
                            caption: logicOptions[i],
                            onClick: function (e) {
                                expression.setExpressionValue(this.name).setIsEditMode(false);
                                that.onChange();
                            }
                        });
                    }
                }
                
                that.parent.parent.html.appendChild(miniPanel.getHTML());
                miniPanel.open();
                break;
            case 'MODULE':
                type = 'modules';
                values.fields.push({
                    defaultSelection: this.expDirection
                }, {
                    defaultSelection: this.expModule.value
                }, {
                    defaultSelection: this.expField.value
                }, {
                    defaultSelection: this.expOperator.value
                }, {
                    value: this.expValue.value
                });
                break;
            case 'CONTROL':
                type = 'control';
                values.fields.push({
                    defaultSelection: this.expField.value
                }, {
                    defaultSelection: this.expValue.value
                });
                break;
            case 'BUSINESS_RULES':
                type = "business_rules";
                values.fields.push({
                    defaultSelection: this.expField.value
                }, {
                    value: this.expValue.value
                });
                break;
        }

        if(this.expType !== 'LOGIC') {
            that.panel.setBelongsTo(this.html);
            that.showEditionPanel({
                type: type,
                settings: values
            });
        }
    };
};

CriteriaField.prototype.itemExitEditHandler = function () {
    var that = this;

    return function () {
        that.hidePanel();
        that.panel.belongsTo = that.itemsContainer;
        that.expressionInEdition = null;
        that.editMode = false;
    };
};

CriteriaField.prototype.hidePanel = function () {
    MultipleItemField.prototype.hidePanel.call(this);

    if(this.viewingForm) {
        this.viewingForm = false;
        $(this.itemsContainer).removeClass("focused");
    }

    return this;
};

CriteriaField.prototype.removeItem = function (item) {
    var input = $(item.getData("inputArea"));
    var index = input.attr("tab-index");
    input.remove();
    this.updatePlaceholdersIndexes();
    MultipleItemField.prototype.removeItem.call(this, item);
    $(this.itemsContainer).find("input").filter('[tab-index="'+index+'"]').focus().select();
    return this;
};

CriteriaField.prototype.updatePlaceholdersIndexes = function () {
    $(this.itemsContainer).find('input').each(function(i) {
        $(this).attr("tab-index", i);
    }).val("");
};

CriteriaField.prototype.addItem = function (expression) {
    var expOb, that = this, target, index = parseInt(this.currentIndex, 10);
    if (expression) {
        if (expression.family === 'SugarExpression') {
            expObj = expression;
        } else {
            expObj = new SugarExpression(expression);
        }

        expObj.onClose = function() {
            var id = this.id, index = null, i;
            that.hidePanel();
            for(i = 0; i < that.items.length; i++) {
                if(that.items[i].id === id) {
                    index = i;
                    break;
                }
            }
            if(index !== null) {
                that.items.splice(index, 1);
            }
            that.onChange();
        };

        expObj.onRemove = function () {
            that.removeItem(this);
        };

        if(expObj.expType !== 'GROUP') {
            expObj.setOnEditHandler(this.itemEditHandler());
            if(expObj != 'LOGIC'){
                expObj.setOnExitEditHandler(this.itemExitEditHandler());
            }
        }

        if(this.controlObject) {
            if(isNaN(index)) {
                target = $(this.controlObject);
            } else {
                target = $(this.itemsContainer).find('input').eq(index);
                if(!target[0]) {
                    target = $(this.controlObject);
                    index = null;
                }
            }
            expObj.setData("inputArea", this.createControlObject());
            target.before(expObj.getData("inputArea")).before(expObj.getHTML());
            target.before(expObj.getHTML());
        }

        console.log("inserting object at index: "+ index);

        if(!isNaN(index) && index > -1) {
            this.items.splice(index, 0, expObj);
        } else {
            this.items.push(expObj);
        }
        this.updatePlaceholdersIndexes();
        $(this.itemsContainer).find('input').val("").filter(":eq("+(index + 1)+")").focus().select();
        this.hidePanel();
        this.onChange();
    }
    return this;
};

CriteriaField.prototype.createChildForm = function (formSettings) {
    var that = this,
        form, html;

    formSettings.showContentOnStart = this.editMode;
    formSettings.visibleHeader = !this.editMode;
    formSettings.submitCaption = this.editMode ? "Update" : "Add";
    formSettings.collapsable = !this.editMode;
    formSettings.cancelButton = true;
    formSettings.onCancel = function () {
        if(that.editMode) {
            that.updateExpression(that.expressionInEdition, {});    
        } else {
            that.hidePanel();
        }
    };
    formSettings.onOpen = function() {
        that.viewingForm = true;
        $(that.parent.parent.html).find('.content').not(this.content).slideUp().end().end()
            .find('.multiple-item-subpanel, .multiple-item-button-panel').not(this.html).slideUp();
    };

    formSettings.onClose = function () {
        $(that.parent.parent.html).find('.multiple-item-subpanel, .multiple-item-button-panel').not(this.html).slideDown();
        $(that.controlObject).focus();
        that.viewingForm = false;
    };

    form = new CriteriaForm(formSettings);

    html = form.getHTML();
    html.id = '#' + formSettings.name + '-panel';

    return html;
}

CriteriaField.prototype.onLogicSelectHandler = function () {
    var that = this;
    return function () {
        that.addItem(new SugarExpression({
            expType: 'LOGIC',
            expValue: this.value
        }));
    };
};

CriteriaField.prototype.onGroupSelectHandler = function () {
    var that = this;
    return function () {
        that.addItem(new SugarExpression({
            expType: 'GROUP',
            expValue: this.value,
            editable: false
        }));
    };
};

CriteriaField.prototype.createBusinessRulePanel = function (formSettings) {
    var that = this,
        defaultFormSettings = {
            title: "Business Rules Evaluation",
            name: "businessRulesEvaluation",
            fields: [
                {
                    name: "expField",
                    type: "select",
                    label: "Business",
                    dataSource: {
                        source: that.panels.businessRulesEvaluation.proxy
                    },
                    defaultSelection: '[first]',
                    required: true
                }, {
                    name: "expOperator",
                    type: "select",
                    label: "Operator",
                    options: SugarExpression.prototype.operators,
                    defaultSelection: "equals",
                    required: true
                }, {
                    name: "expValue",
                    type: "long_text",
                    label: "Response",
                    required: true
                }
            ],
            onSubmit: function (data) {
                var obj;

                obj = {
                    expField: {
                        text: this.getFieldByName("expField").getSelectedText() || null,
                        value: data.expField || null
                    },
                    expOperator: "equals",
                    expValue: data.expValue
                };

                if(that.editMode) {
                    that.updateExpression(that.expressionInEdition, obj);
                } else {
                    obj.expType = "BUSINESS_RULES",
                    that.addItem(obj);
                }
                that.viewingForm = false;
            }
        };

    $.extend(true, defaultFormSettings, formSettings || {});

    return this.createChildForm(defaultFormSettings);
};

CriteriaField.prototype.createGroupPanel = function () {
    var that = this,
        settings = {
            label: "Group",
            buttons: [
                {
                    caption: "(",
                    value: "(",
                    onClick: that.onGroupSelectHandler()
                }, {
                    caption: ")",
                    value: ")",
                    onClick: that.onGroupSelectHandler()
                }
            ]
        };

    return this.panel.createButtonPanel(settings);
};

CriteriaField.prototype.createLogicPanel = function () {
    var that = this,
        settings = {
            label: "Logic operators",
            buttons: [
                {
                    caption: "AND",
                    value: "AND",
                    onClick: that.onLogicSelectHandler()
                }, {
                    caption: "OR",
                    value: "OR",
                    onClick: that.onLogicSelectHandler()
                }, {
                    caption: "NOT",
                    value: "NOT",
                    onClick: that.onLogicSelectHandler()
                }
            ]
        };

    return this.panel.createButtonPanel(settings);
};

CriteriaField.prototype.updateExpression = function (target, newValues) {
    var f, change = false;
    for(property in newValues) {
        switch(property) {
            case 'expDirection':
                f = 'Direction';
                break;
            case 'expModule':
                f = "Module";
                break;
            case 'expField':
                f = 'Field';
                break;
            case 'expOperator':
                f = 'Operator';
                break;
            case 'expValue':
                f = 'Value'
        }


        f = target['setExpression' + f];
        if(typeof f === 'function') {
            f.call(target, newValues[property]);
            change = true;
        }
    }
    target.setIsEditMode(false);
    if(change) {
        this.onChange();
    }
    return this;
};

CriteriaField.prototype.createModulesPanel = function (formSettings) {
    var that = this,
        options = PROJECT_MODULE ? [{
            label: "<<Target Module>>",
            value: PROJECT_MODULE || ""
        }] : [],
        defaultFormSettings = {
        title: "Module Field Evaluation",     
        name: "module",
        fields: [   
            {
                name: "expDirection",
                type: "select",
                label: "Direction",
                options: [
                    {
                        label: "before"
                    },
                    {
                        label: "after",
                        selected: true
                    }
                ],
                required: true
            },
            {
                name: "expModule",
                type: "select",
                label: "Module",
                options: options,
                preserveDefaultOptions: true, 
                dataSource: {
                    source: that.panels.fieldEvaluation.modulesProxy,
                    labelField: "text",
                    valueField: "value"
                },
                defaultSelection: '[first]',
                required: true
            },
            {
                name: "expField",
                type: "select",
                label: "Variable",
                dependsOn: 'expModule',
                required: true,
                dependencyHandler: function (e) {
                    var data;
                    that.panels.fieldEvaluation.fieldsProxy.uid = e.getValue();
                    if(that.panels.fieldEvaluation.fieldsProxy.uid) {
                        data = that.panels.fieldEvaluation.fieldsProxy.getData();
                        if(data.result) {
                            this.fill(data.result, "text", "value");
                        }
                    } else {
                        this.clear();
                    }
                },
                defaultSelection: 'name'
            },
            {
                name: "expOperator",
                type: "select",
                label: "Operator",
                options: SugarExpression.prototype.operators,
                defaultSelection: "equals",
                required: true
            },
            {
                name: "expValue",
                type: "text",
                label: "Value",
                required: true
            }
         ],
        onSubmit: function (data) {
            var obj;

            obj = {
                expDirection: data.expDirection || null,
                expModule: {
                    text: this.getFieldByName('expModule').getSelectedText() || null,
                    value: data.expModule || null
                },
                expField: {
                    text: this.getFieldByName('expField').getSelectedText() || null,
                    value: data.expField || null
                },
                expOperator: { 
                    text: this.getFieldByName("expOperator").getSelectedText() || null,
                    value: data.expOperator
                },
                expValue: data.expValue
            };

            if(that.editMode) {
                that.updateExpression(that.expressionInEdition, obj);
            } else {
                obj.expType = "MODULE"; 
                that.addItem(obj);
            }
            that.viewingForm = false;
        },
        onCancel: function() {
            alert("dsfsadf");
        }
    };

    $.extend(true, defaultFormSettings, formSettings || {});

    return this.createChildForm(defaultFormSettings);
};

CriteriaField.prototype.createControlPanel = function (formSettings) {
    var that = this,
        defaultFormSettings = {
        title: "Form Response Evaluation",
        name: "control",
        fields: [
            {
                name: "expField",
                type: "select",
                label: "Form",
                dataSource: {
                    source: this.panels.formResponseEvaluation.proxy
                },
                defaultSelection: '[first]',
                required: true
            },
            {
                name: "expValue",
                type: "select",
                label: "Status",
                options: [
                    {
                        label: "Approved",
                        value: "Approve"
                    },
                    {
                        label: "Rejected",
                        value: "Reject"
                    },
                ],
                defaultSelection: "[first]",
                required: true
            }
        ],
        onSubmit: function (data) {
            var obj;

            obj = {
                expField: {
                    text: this.getFieldByName('expField').getSelectedText() || null,
                    value: data.expField || null
                },
                expOperator: {
                    text: "==",
                    value: "equals"
                },
                expValue: {
                    text: this.getFieldByName('expValue').getSelectedText() || null,
                    value: data.expValue || null
                }
            };

            if(that.editMode) {
                that.updateExpression(that.expressionInEdition, obj);
            } else {
                obj.expType = "CONTROL",
                that.addItem(obj);
            }
            that.viewingForm = false;
        }
    };

    $.extend(true, defaultFormSettings, formSettings || {})

    return this.createChildForm(defaultFormSettings);
};

CriteriaField.prototype.getInputAreaValidationFunction = function () {
    var that = this;
    return function (value) {
        var value = $.trim(value).toUpperCase();
        if(value === 'AND' || value === 'OR' || value === 'NOT') {
            that.panelSemaphore = false;
            that.addItem(new SugarExpression({
                expType: 'LOGIC',
                expValue: value
            }));
            this.clear();
        } else if(value ==='(' || value === ')'){
            that.panelSemaphore = false;
            that.addItem(new SugarExpression({
                expType: 'GROUP',
                expValue: value,
                editable: false
            }));
            this.clear();
        } else {
            $(this.html).focus().select();
        }
        that.currentIndex = $(this.html).attr("tab-index");
        return true;
    };
};

CriteriaField.prototype.createControlObject = function () {
    var input = new InputArea();
    input.validationFunction = this.getInputAreaValidationFunction();
    $(input.getHTML()).on("click", function(e){
        e.stopPropagation();
    }).on("keydown", function(e){
        var $this = $(this), value = $this.val();
        if(e.keyCode === 37 && value === '') {
            $this.prev().prev().focus().select();
        } else if(e.keyCode === 39 && value === '') {
            $this.next().next().focus().select();
        }
    });
    return input.getHTML();
};

CriteriaField.prototype.attachListeners = function () {
    MultipleItemField.prototype.attachListeners.call(this);

    var control, that = this,
        $itemsContainer = $(this.itemsContainer);

    control = $(this.controlObject);

    control.off("blur");

    $itemsContainer.on('blur', '.multiple-item-input', function (e) {
        if(!that.viewingForm) {
            that.hidePanel();
            $itemsContainer.removeClass('focused');
            $(that.panel.html).removeClass('focused');
        }
    }).on("click", '.multiple-item-input', function (e) {
        e.stopPropagation();
        that.showPanel();
    }).on("keyup", '.multiple-item-input', function (e){
        e.stopPropagation();
        if(e.keyCode !== 27) {
            if(!that.panelSemaphore) {
                that.panelSemaphore = true;
            } else {
                if((!that.panel.isOpen && this.value !== "") || e.keyCode === 13) {
                    that.showPanel();
                }
            }
        } else {
            that.hidePanel();
        }
    }).on("focus", '.multiple-item-input', function(){
        that.currentIndex = $(this).attr("tab-index");
        that.showPanel();
    });

    $itemsContainer.on("click", function (e) {
        e.stopPropagation();
        $(control).trigger("click");
    });
};

CriteriaField.prototype.isValid = function () {
    var i, valid = true, prev = null, exp, pendingToClose = 0;

    for (i = 0; i < this.items.length; i += 1) {
        exp = this.items[i]
        if (exp.expType === 'MODULE' || exp.expType === 'BUSINESS_RULES' || exp.expType === 'CONTROL' || (exp.expType === 'GROUP' && exp.expValue.value === '(') || (exp.expType === 'LOGIC' && exp.expValue.value === 'NOT')) {
            valid = valid && (
                prev === null ||
                    prev.expType === 'LOGIC' ||
                    (prev.expType === 'GROUP' && prev.expValue.value === '(')
            );
        } else {
            if (prev === null) {
                valid = false;
                break;
            }
            valid = valid && (
                prev.expType === 'MODULE' || 
                    prev.expType === 'CONTROL' ||
                    prev.expType === 'BUSINESS_RULES' ||
                    (prev.expType === 'GROUP' && prev.expValue.value === ')')
                );
        }

        if (exp.expType === 'GROUP') {
            if (exp.expValue.value === ')') {
                valid = valid && pendingToClose > 0;
                pendingToClose -= 1;
            } else if (exp.expValue.value === '(') {
                pendingToClose += 1;
            }
        }

        if (!valid) {
            break;
        }
        prev = exp;
    }

    if (valid) {
        if (prev) {
            valid = valid && prev.expType !== 'LOGIC' && !(prev.expType === 'GROUP' && prev.expValue.value === "(");
        }
        valid = valid && pendingToClose === 0;
    }

    if (valid) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
        valid = valid && Field.prototype.isValid.call(this);
    } else {
        this.errorTooltip.setMessage("invalid criteria syntax");
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
    }

    if(valid) {
        return valid && Field.prototype.isValid.call(this);
    }

    return valid;
};

CriteriaField.prototype.getObject = function () {
    var e, auxValue = [], exp, obj;
    for (e = 0; e < this.items.length; e += 1) {
        obj = {};
        exp = this.items[e];
        switch(exp.expType){
            case 'MODULE':
                obj.expDirection = exp.expDirection || null;
            case 'CONTROL':
            case 'BUSINESS_RULES':
                obj.expModule = (exp.expModule && exp.expModule.value) || null;
                obj.expField = exp.expField.value || null;
                obj.expOperator = (exp.expOperator && exp.expOperator.value) || null;
        }
        obj.expValue = exp.expValue.value;
        obj.expType = exp.expType;
        obj.expLabel = exp.getLabel();
        auxValue.push(obj);
    }
    return auxValue;
};

CriteriaField.prototype.createHTML = function () {
    var that = this;
    if(this.html) {
        return this.html;
    }

    MultipleItemField.prototype.createHTML.call(this);
    $(this.itemsContainer).on('scroll', function () {
        if(that.expressionInEdition) {
            that.updateExpression(that.expressionInEdition, {});
        }
    });

    return this.html;
};

//input area
    var InputArea = function () {
        Element.call(this);
        this.textlabel = null;
    };

    InputArea.prototype = new Element();

    InputArea.prototype.type = "InputArea";

    InputArea.prototype.getTextWidth = function () {
        var width;
        this.textlabel.innerText = this.html.value;
        $(this.html).before(this.textlabel);
        width = $(this.textlabel).width();
        $(this.textlabel).detach();
        return width;
    };

    InputArea.prototype.validationFunction = function() {
        return !!$.trim(this.html.value);
    };

    InputArea.prototype.acceptInput = function(value) {};

    InputArea.prototype.clear = function() {
        $(this.html).val("").css("width", "1px");
        return this;
    };

    InputArea.prototype.onBlur = function () {
        var that = this;
        return function (e) {
            that.clear();
        };
    };

    InputArea.prototype.onKeyUp = function() {
        var that = this;
        return function (e) {
            var $this = $(this);
            var value = $this.val();
            var valid = false;

            if(e.keyCode !== 37 && $.trim(value) !== '') {
                $this.css("width", that.getTextWidth()+8);
            }

            if(e.keyCode === 13) {
                e.preventDefault();
                if(typeof that.validationFunction === 'function') {
                    valid = that.validationFunction.call(that, value);
                } else {
                    valid = true;
                }

                if(valid) {
                    that.acceptInput(value);    
                }        
            } else if(e.keyCode === 27) {
                that.clear();
            }
        };
    };

    InputArea.prototype.createHTML = function () {
        var textbox, label; 
        if(!this.html) {
            textbox = this.createHTMLElement('input');
            textbox.style.width = "1px";
            textbox.type = "text";
            textbox.className = 'multiple-item-input';

            label = this.createHTMLElement('span');
            label.style.display = 'none';
            this.textlabel = label;

            this.html = textbox;

            this.style.addProperties({
                "padding-left": 0,
                "padding-right": 0,
                "margin-right": 0,
                "margin-left": 0
            });

            $(this.html).on('keyup', this.onKeyUp())
                .on('blur', this.onBlur());
        }
        return this.html;
    };

//CriteriaFormFields
    //Criteria form field

        var CriteriaFormField = function (settings) {
            Base.call(this);
            this.name = null;
            this.label = null;
            this.value = null;
            this.form = null;
            this.dependsOn = null;
            this.required = null;
            this.dependentFields = null;
            this.dependencyHandler = null;
            this.control = null;
            this.restClient = null;

            CriteriaFormField.prototype.initObject.call(this, settings || {});
        };

        CriteriaFormField.prototype = new Base();

        CriteriaFormField.prototype.type = 'CriteriaFormField';

        CriteriaFormField.prototype.initObject = function (settings) {
            var defaults = {
                fieldType: "text",
                name: "",
                label: "",
                dependsOn: null,
                dependencyHandler: null,
                value: null,
                required: false,
                dependentFields: [],
                restClient: null  
            };

            $.extend(true, defaults, settings || {});

            this.fieldType = defaults.fieldType;

            this.setName(defaults.name)
                .setLabel(defaults.label)
                .setValue(defaults.value)
                .setDependentFields(defaults.dependentFields)
                .setDependency(defaults.dependsOn)
                .setDependencyHandler(defaults.dependencyHandler)
                .setRequired(defaults.required)
                .setRestClient()
                .createControl();
        };

        CriteriaFormField.prototype.setRestClient = function(restClient) {
            this.restClient = restClient;
            return this;
        };

        CriteriaFormField.prototype.setRequired = function (required) {
            if(typeof required === 'boolean') {
                this.required = required;
            }

            return this;
        };

        CriteriaFormField.prototype.setName = function (value) {
            this.name = value;

            return this;
        };

        CriteriaFormField.prototype.setLabel = function (value) {
            this.label = value;

            return this;
        };

        CriteriaFormField.prototype.setValue = function (value) {
            this.value = value;

            this.updateHTML();

            return this;
        };

        CriteriaFormField.prototype.getValue = function (value) {
            return $(this.control).val();
        };

        CriteriaFormField.prototype.setDependentFields = function (fields) {
            this.dependentFields = fields;

            return this;
        };

        CriteriaFormField.prototype.addDependentField = function (field) {
            if(field instanceof CriteriaFormField && field !== this) {
                this.dependentFields.push(field);
            }

            return this;
        };

        CriteriaFormField.prototype.setDependency = function (field) {
            if(field instanceof CriteriaFormField) {
                this.dependsOn = field;
                field.addDependentField(this);
            }

            return this;
        };

        CriteriaFormField.prototype.setDependencyHandler = function (value) {
            if(typeof value === 'function') {
                this.dependencyHandler = value;
            }

            return this;
        };

        CriteriaFormField.prototype.clear = function () {
            $(this.control).val("");
            this.value = "";

            return this;
        };

        CriteriaFormField.prototype.updateHTML = function () {
            if(this.control) {
                $(this.control).val(this.value || "");
            }

            return  this;
        };

        CriteriaFormField.prototype.createControl = function () {};

        CriteriaFormField.prototype.onChangeValueHandler = function () {
            this.value = $(this.control).val();

            return this;
        };

        CriteriaFormField.prototype.reset = function () {
            $(this.control).removeClass("error");

            return this;
        };

        CriteriaFormField.prototype.triggerDependentFields = function () {
            var i, f;
            for(i = 0; i < this.dependentFields.length; i++) {
                f = this.dependentFields[i];
                if(typeof f.dependencyHandler === 'function') {
                    f.dependencyHandler.call(f, this);
                }
            }

            return this;
        };

        CriteriaFormField.prototype.getHTML = function () {
            var label, that = this;

            if(this.html) {
                return this.html;
            }

            label = document.createElement("span");
            label.className = "label";
            label.appendChild(document.createTextNode(this.label));
            this.control.name = this.name;
            this.control.id = this.id;
            $(this.control).on("change", function() {
                that.onChangeValueHandler();
                that.isValid();
                that.triggerDependentFields();
            });
            if(this.value !== null) {
                $(this.control).val(this.value);
            }

            this.html = document.createElement("label");
            this.html.appendChild(label);
            this.html.appendChild(this.control);

            return this.html;
        };

        CriteriaFormField.prototype.isValid = function () {
            var valid = !!($(this.control).val() || !this.required);

            if(!valid) {
                $(this.control).addClass('error');
            } else {
                $(this.control).removeClass('error');
            }

            return valid;
        };

        CriteriaFormField.prototype.onAppend = function () {};

    //Criteria text field

        var CriteriaFormTextField = function (settings) {
            CriteriaFormField.call(this, settings);
        };

        CriteriaFormTextField.prototype = new CriteriaFormField();

        CriteriaFormTextField.prototype.type = "criteriaFormTextField";

        CriteriaFormTextField.prototype.createControl = function () {
            this.control = document.createElement('input');
            this.control.type = 'text';

            $(this.control).on('keydown', function(e){
                e.stopPropagation();
            }).on('keyup', function (e) {
                e.stopPropagation();
            });

            return this;
        };

    //Criteria long_text field
        var CriteriaFormLongTextField = function (settings) {
            CriteriaFormField.call(this, settings);
        };

        CriteriaFormLongTextField.prototype = new CriteriaFormField();

        CriteriaFormLongTextField.prototype.type = "CriteriaFormLongTextField";

        CriteriaFormLongTextField.prototype.createControl = function () {
            this.control = document.createElement('textarea');

            return this;
        };

    //Criteria select field
        var CriteriaFormSelectField = function (settings) {
            this.defaultSelection = null;
            this.selectedIndex = null;
            this.preserveDefaultOptions = null;
            this.options = null;
            this.dataSource = null;
            CriteriaFormField.call(this, settings);

            CriteriaFormSelectField.prototype.initObject.call(this, settings);
        };

        CriteriaFormSelectField.prototype = new CriteriaFormField();

        CriteriaFormSelectField.prototype.initObject = function (settings) {
            var defaults = {
                dataSource: null, 
                options: [],
                defaultSelection: null,
                preserveDefaultOptions: false
            };

            $.extend(true, defaults, settings);
            this.options = defaults.options;

            this.setDefaultSelection(defaults.defaultSelection)
                .fill(this.options)
                .setDataSource(defaults.dataSource)
                .setPreserveDefaultOptions(defaults.preserveDefaultOptions);
        };

        CriteriaFormSelectField.prototype.setPreserveDefaultOptions = function (preserve) {
            this.preserveDefaultOptions = preserve;

            return this;
        };

        CriteriaFormSelectField.prototype.setDefaultSelection = function (selection) {
            this.defaultSelection = selection;

            return this;
        };

        CriteriaFormSelectField.prototype.getSelectedText = function () {
            var selectedOption = $(this.control).find('option:selected');
            return selectedOption.get(0) ? selectedOption.text() : null;
        };

        CriteriaFormSelectField.prototype.setDataSource = function (dataSource) {
            if(typeof dataSource === 'object') {
                this.dataSource = $.extend(true, {
                    labelField: 'text',
                    valueField: 'value',
                    itemsField: 'result'
                }, dataSource);
            }

            return this;
        };

        CriteriaFormSelectField.prototype.clear = function () {
            $(this.control).empty();
            this.value = "";

            return this;
        };

        CriteriaFormSelectField.prototype.fill = function (data, labelField, valueField) {
            var i, option, l, v, s, ifSelected = false,
                defaultSelection = this.defaultSelection;

            labelField = labelField || "label";
            valueField = valueField || "value";

            $(this.control).empty();

            for(i = 0; i < data.length; i++) {
                l = data[i][labelField];
                v = data[i][valueField];
                s = data[i].selected || false;
                option = document.createElement('option');
                option.label = l || v;
                option.value = v || l;
                option.selected = s;
                ifSelected = s || ifSelected;
                option.appendChild(document.createTextNode(l||v));
                this.control.appendChild(option);
            }

            if(!ifSelected) {
                if(defaultSelection === '[first]') {
                    $(this.control).find('option:first').attr("selected", true);
                } else if (defaultSelection === '[none]' || defaultSelection === null) {
                    this.control.selectedIndex = -1;
                } else {
                    $(this.control).find('option[value="' + defaultSelection + '"]').attr("selected", true);
                }
            }

            this.value = $(this.control).val();

            this.selectedIndex = this.control.selectedIndex;

            this.triggerDependentFields();

            return this;
        };

        CriteriaFormSelectField.prototype.bind = function (key) {
            var data, control, i, iField, option, auxOptions = [];

            if(this.dataSource) {
                if(!this.control) {
                    this.createControl();
                }

                if(this.dataSource.source) {
                    if(key) {
                        this.dataSource.source.uid = key;
                    }

                    data = this.dataSource.source.getData();

                    control = $(this.control);
                    control.empty();

                    data = data ? data : [];

                    iField = this.dataSource.itemsField;

                    data = iField ? data[iField] : data;

                    if(data && data.push){
                        if(this.preserveDefaultOptions) {
                            for(i = 0; i < this.options.length; i++) {
                                control = {};
                                control[this.dataSource.labelField] = this.options[i].label;
                                control[this.dataSource.valueField] = this.options[i].value;
                                auxOptions.push(control);
                            }
                            data = $.merge(auxOptions, data);
                        }
                        this.fill(data, this.dataSource.labelField, this.dataSource.valueField);
                    }
                }
            }

            return this;
        };

        CriteriaFormSelectField.prototype.createControl = function () {
            this.control = document.createElement("select");

            return this;
        };

        CriteriaFormSelectField.prototype.getHTML = function () {
            this.bind();
            return CriteriaFormField.prototype.getHTML.call(this);
        };

        CriteriaFormSelectField.prototype.onAppend = function () {
            this.control.selectedIndex = this.selectedIndex;
        };

    //Criteria hidden field
        var CriteriaFormHiddenField = function (settings) {
            CriteriaFormField.call(this, settings);
        };

        CriteriaFormHiddenField.prototype = new CriteriaFormField();

        CriteriaFormHiddenField.prototype.createControl = function () {
            this.control = document.createElement("input");
            this.control.type = "hidden";

            $(this.control).val(this.getValue());

            return this;
        };

        CriteriaFormHiddenField.prototype.getHTML = function () {
            var aux = CriteriaFormField.prototype.getHTML.call(this);

            this.html.style.display = 'none';

            this.triggerDependentFields();

            return this.html;
        };

//CriteriaForm 
    var CriteriaForm = function (settings) {
        MultipleItemSubpanel.call(this, settings);

        this.fields = null;
        this.onSubmit = null;
        this.onCancel = null;
        this.submitCaption = null;
        this.cancelButton = null;
        this.cancelCaption = null;

        CriteriaForm.prototype.initObject.call(this, settings);
    };

    CriteriaForm.prototype.type = "CriteriaForm";

    CriteriaForm.prototype = new MultipleItemSubpanel();

    CriteriaForm.prototype.initObject = function (settings) {
        var defaults = {
            fields: [],
            onSubmit: null,
            onCancel: null,
            submitCaption: "Submit",
            cancelCaption: "Cancel",
            cancelButton: false
        };

        $.extend(true, defaults, settings);

        this.onSubmit = defaults.onSubmit;
        this.onCancel = defaults.onCancel;
        this.submitCaption = defaults.submitCaption;
        this.cancelButton = defaults.cancelButton;
        this.cancelCaption = defaults.cancelCaption;

        this.setFields(defaults.fields);
    };

    CriteriaForm.prototype.getFieldByName = function (name) {
        return this.fields.find("name", name);
    };

    CriteriaForm.prototype.setFields = function (fields) {
        var i, field;

        this.fields = new jCore.ArrayList();

        for(i = 0; i < fields.length; i++) {
            field = fields[i];
            if(!(fields[i] instanceof CriteriaFormField)) {
                if(typeof field === 'object') {
                    field.dependsOn = (field.dependsOn && this.getFieldByName(field.dependsOn)) || null;
                    switch(field.type) {
                        case 'text':
                            field = new CriteriaFormTextField(field);
                            break;
                        case 'hidden':
                            field = new CriteriaFormHiddenField(field);
                            break;
                        case 'select':
                            field = new CriteriaFormSelectField(field);
                            break;
                        case 'long_text':
                            field = new CriteriaFormLongTextField(field);
                    }
                }
            }
            field.form = this;
            this.fields.insert(field);
        }

        return this;
    };

    CriteriaForm.prototype.getSubmitObject = function () {
        var obj = {}, i;

        for(i = 0; i < this.fields.getSize(); i++) {
            obj[this.fields.get(i).name] = this.fields.get(i).getValue();
        }

        return obj;
    };

    CriteriaForm.prototype.isValid = function () {
        var i, valid = true;

        for(i = 0; i < this.fields.getSize(); i++) {
            valid = valid && this.fields.get(i).isValid();
            if(!valid) {
                return valid;
            }
        }

        return valid;
    };

    CriteriaForm.prototype.reset = function () {
        var i;

        for(i = 0; i < this.fields.getSize(); i++) {
            this.fields.get(i).reset();
        }

        return this;
    };

    CriteriaForm.prototype.createHTML = function () {
        MultipleItemSubpanel.prototype.createHTML.call(this);
        $(this.html).addClass('multiple-item-form');
        return this.html;
    };

    CriteriaForm.prototype.open = function () {
        var loader = document.createElement('div'),
            $content = $(this.content), cancelButton,
            aux, i, field, that = this;

        $content.empty();

        this.showLoader();

        MultipleItemSubpanel.prototype.open.call(this);

        aux = [];

        for(i = 0; i < this.fields.getSize(); i++) {
            field = this.fields.get(i);
            aux.push(this.fields.get(i).getHTML());
        }

        this.hideLoader();

        for(i = 0; i < aux.length; i++) {
            $content.append(aux[i]);
            if(typeof aux[i].onAppend === 'function') {
                aux[i].onAppend();
            }
        }

        aux = document.createElement("div");
        aux.style.textAlign = 'center';

        loader = document.createElement('button');
        loader.appendChild(document.createTextNode(this.submitCaption));
        aux.appendChild(loader);

        $(loader).on("click", function() {
            var submitObject;

            if(that.isValid()){
                if(typeof that.onSubmit === 'function') {
                    submitObject= that.getSubmitObject();
                    that.onSubmit.call(that, submitObject);
                }
            }
        });

        if(this.cancelButton) {
            cancelButton = document.createElement('button');
            cancelButton.appendChild(document.createTextNode(this.cancelCaption));
            aux.appendChild(cancelButton);

            $(cancelButton).on("click", function () {
                if(typeof that.onCancel === 'function') {
                    that.onCancel.call(that);
                }
            });
        }

        $content.append(aux);

        return this;
    };

//SugarExpession

    var SugarExpression = function (options) {
        SingleItem.call(this, options);

        this.expType = null;
        this.expDirection = null;
        this.expModule = null;
        this.expField = null;
        this.expOperator = null;
        this.expValue = null;
        this.editable = null;
        this.onEdit = null;
        this.onExitEdit = null;
        this.editMode = null;

        SugarExpression.prototype.initObject.call(this, options);
    };

    SugarExpression.prototype = new SingleItem();

    SugarExpression.prototype.type = 'SugarExpression';
    SugarExpression.prototype.family = 'SugarExpression';

    SugarExpression.prototype.operators = [
        {
            value: 'equals',
            label: '=='
        }, {
            value: 'major_than',
            label: '>'
        }, {
            value: 'major_equals_than',
            label: '>='
        }, {
            value: 'not_equals',
            label: '!='
        }, {
            value: 'minor_than',
            label: '<'
        }, {
            value: 'minor_equals_than',
            label: '<='
        }, {
            value: 'within',
            label: 'within'
        }, {
            value: 'not_within',
            label: 'not within'
        }
    ];

    SugarExpression.prototype.eTypes = {
        'LOGIC' : 'LOGIC',
        'GROUP': 'GROUP',
        'MODULE' : 'MODULE',
        'CONTROL': 'CONTROL',
        'DEFAULTMODULE': 'DEFAULTMODULE',
        'BUSINESS_RULES': 'BUSINESS_RULES'
    };
    SugarExpression.prototype.initObject = function (options) {
        var defaults = {
            expType: null,
            expModule: {
                text: null,
                value: null
            },
            expField: {
                text: null,
                value: null
            },
            expOperator : {
                text: null,
                value: null
            },
            expDirection: null,
            expLabel: null,
            expValue: {
                text: null,
                value: null
            },
            editable: true,
            onEdit: null,
            onExitEdit: null
        };

        $.extend(true, defaults, options);

        this.editMode = false;

        this.setExpressionValue(defaults.expValue)
            .setExpressionType(defaults.expType)
            .setExpressionOperator(defaults.expOperator)
            .setExpressionModule(defaults.expModule)
            .setExpressionField(defaults.expField)
            .setExpressionDirection(defaults.expDirection)
            .setIsEditable(defaults.editable)
            .setOnEditHandler(defaults.onEdit)
            .setOnExitEditHandler(defaults.onExitEdit)
            .updateLabel();


        this.label = defaults.expLabel || "";

        this.getHTML(defaults.expLabel);
    };

    SugarExpression.prototype.setOnExitEditHandler = function (handlerFunction) {
        delete this.setOnExitEditHandler;
        if(typeof handlerFunction === 'function') {
            this.onExitEdit = handlerFunction;
        } else {
            this.onExitEdit = null;
        }

        return this;
    };

    SugarExpression.prototype.setIsEditable = function (editable) {
        this.editable = editable;
        if(this.html) {
            $(this.html).off("click").on("click", this.onClickHandler());
        }
        this.refreshCursor();

        return this;
    };

    SugarExpression.prototype.setValue = function () {
        return this;
    };

    SugarExpression.prototype.setExpressionType = function (type) {
        if (this.eTypes[type]) {
            this.expType = this.eTypes[type];
        }
        document.createElement('li');
        return this;
    };

    SugarExpression.prototype.refreshTooltip = function() {
        if(this.html) {
            $(this.html).attr("title", this.getSugarExpressionText());
        }

        return this;
    };

    SugarExpression.prototype.updateHTML = function () {
        this.updateLabel();

        return SingleItem.prototype.updateHTML.call(this);
    };

    SugarExpression.prototype.createHTML = function (label) {
        var liObjectm, that = this;

        if(this.html) {
            return this.html;
        }

        SingleItem.prototype.createHTML.call(this);
        if(this.label === "") {
            this.updateHTML();
        } else {
            SingleItem.prototype.updateHTML.call(this);    
        }
        
        return this.refreshTooltip().html;
    };

    SugarExpression.prototype.setExpressionModule = function (module) {
        if(typeof module === 'string') {
            module = {
                text: module, 
                value: module
            };
        }
        this.expModule = module;
        return this;
    };

    SugarExpression.prototype.setExpressionField = function (field) {
        if(typeof field === 'object') {
            this.expField = field;
        } else if(typeof field === 'string' || typeof field === 'number') {
            this.expField ={
                text: field,
                value: field
            };
        }
        this.updateHTML();
        return this;
    };

    SugarExpression.prototype.getObject = function () {
        return {
            expType: this.expType,
            expModule: this.expModule,
            expField: this.expField,
            expOperator: this.expOperator,
            expValue: this.expValue,
            expDirection: this.expDirection,
            expLabel: this.getLabel()
        };
    };

    SugarExpression.prototype.getValue = function () {
        return JSON.stringify(this.getObject());
    };

    SugarExpression.prototype.updateLabel = function () {
        if(this.expField && this.expOperator && this.expValue) {
            this.label = this.getFriendlyText();
        }

        return this;
    };

    SugarExpression.prototype.setExpressionOperator = function (operator) {
        var i; 
        if(typeof operator === 'object') {
            this.expOperator = operator;
            this.updateHTML();
        } else if(typeof operator === 'string') {
            for(i = 0; i < this.operators.length; i++) {
                if(this.operators[i].label === operator || this.operators[i].value === operator) {
                    this.expOperator = {
                        text: this.operators[i].label,
                        value: this.operators[i].value
                    };

                    break;
                }
            }
        }
        return this;
    };

    SugarExpression.prototype.setIsEditMode = function (isEditMode) {
        this.editMode = !!isEditMode;

        if(isEditMode) {
            this.hideCloseButton();
            if(typeof this.onEdit === 'function') {
                this.onEdit.call(this);
            }
            $(this.html).addClass('expanded');
        } else {
            this.showCloseButton();
            this.refreshTooltip();
            $(this.html).removeClass('expanded');
            if(typeof this.onExitEdit === 'function') {
                this.onExitEdit.call(this);
            }
        }

        return this;
    };

    SugarExpression.prototype.onClickHandler = function () {
        var that = this;

        if(this.editable) {
            return function (e) {
                e.stopPropagation();
                that.setIsEditMode(!that.editMode);
            };
        } else {
            return null;
        }
        this.refreshCursor();
        return this;
    };

    SugarExpression.prototype.setExpressionDirection = function (direction) {
        this.expDirection = direction;
        return this;
    };

    SugarExpression.prototype.setExpressionValue = function (value) {
        if(typeof value === 'string') {
            this.expValue = {
                text: value,
                value: value
            };
        } else {
            this.expValue = value;
        }
        this.updateHTML();
        return this;
    };

    SugarExpression.prototype.getFriendlyText = function () {
        var output = '';
        switch (this.expType) {
        case 'LOGIC':
        case 'GROUP':
            output = this.expValue.text;
            break;
        case 'DEFAULTMODULE':
        case 'CONTROL':
        case 'BUSINESS_RULES':
        case 'MODULE':
            if (this.expOperator === 'with_in') {
                output = this.expField.text + ' ' + this.expOperator.text + ' [' + this.expValue.text + ']';
            } else {
                output = this.expField.text + ' ' + this.expOperator.text + ' \'' + this.expValue.text + '\'';
            }
            break;
        }
        return output;
    };

    SugarExpression.prototype.getSugarExpressionText = function () {
        var output = '', fullOperand, aux = [];
        switch (this.expType) {
        case 'LOGIC':
        case 'GROUP':
            output = this.expValue.text;
            break;
        case 'CONTROL':
        case 'BUSINESS_RULES':
        case 'MODULE':
            aux.push('{');
            if (this.expDirection !== null) {
                aux.push(this.expDirection);
            }
            aux.push((this.expModule && this.expModule.value) || "NULL");
            aux.push(this.expField.value);
            aux.push('}');
            fullOperand = aux.join('::');
            if (this.expOperator.value === 'with_in' || this.expOperator.value === 'not_with_in') {
                output = fullOperand + ' ' + this.expOperator.text + ' [' + this.expValue.text + ']';
            } else {
                output = fullOperand + ' ' + this.expOperator.text + ' \'' + this.expValue.text + '\'';
            }
            break;
        }
        return output;
    };
var ExpressionField = function(settings, parent) {
    Field.call(this, settings, parent);
    this.variablesProxy = null;
    this.expressionFieldControl = null;
    ExpressionField.prototype.initObject.call(this, settings);
};

ExpressionField.prototype = new Field();

ExpressionField.prototype.initObject = function(settings) {
    var defaults = {
        variablesProxy: null
    };

    $.extend(true, defaults, settings);

    this.setVariablesProxy(defaults.variablesProxy);
    this.expressionFieldControl = new ExpressionFieldControl({
        value: this.value,
        variables: this.getVariables(),
        onChange: this.onChangeHandler()
    });
};

ExpressionField.prototype.getControl = function() {
    return this.expressionFieldControl;
};

ExpressionField.prototype.clear = function () {
    this.expressionFieldControl.clear();
    return this;
};

ExpressionField.prototype.onChangeHandler = function() {
    var that = this;

    return function() {
        that.value = this.value;
        that.onChange();
    };
};

ExpressionField.prototype.setVariablesProxy = function(proxy) {
    if(proxy instanceof RestProxy) {
        this.variablesProxy = proxy;
    }

    return this;
};

ExpressionField.prototype.getValue = function() {
    return this.value;
};

ExpressionField.prototype.getVariables = function() {
    var data, i, res = [];

    if(this.variablesProxy) {
        data = this.variablesProxy.getData();
        if(data && data.success) {
            for(i = 0; i < data.result.length; i++) {
                res.push({
                    label: data.result[i].text,
                    value: data.result[i].value
                });
            }
        }
    }

    return res;
};

ExpressionField.prototype.isValid = function() {
    var valid = this.expressionFieldControl.isValid();

    if (valid) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
        return valid = valid && Field.prototype.isValid.call(this);
    } else {
        this.errorTooltip.setMessage("invalid criteria syntax");
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
    }

    return valid;
};

ExpressionField.prototype.createHTML = function() {
    var fieldLabel, required = '', controlObject, textbox, panel;

    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = required + this.label + ':';
    fieldLabel.style.width = (this.parent.labelWidth) || 'auto';
    this.html.appendChild(fieldLabel);

    controlObject = this.expressionFieldControl.getHTML();
    this.controlObject = controlObject;

    this.html.appendChild(controlObject);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    return this.html;
};

ExpressionField.prototype.setValue = function(value) {
    this.value = value;
    if(this.expressionFieldControl) {
        this.expressionFieldControl.setValue(value);
    }

    return this;
};

ExpressionField.prototype.evalRequired = function () {
    var response = true;
    if (this.required) {
        response = this.expressionFieldControl.items.length;
        if (!response) {
            $(this.controlObject).addClass('required');
        } else {
            $(this.controlObject).removeClass('required');
        }
    }
    return response;
};

/////////////////////////////////////////////////////////////////////////////////////

var ExpressionFieldControl = function(settings){
    Element.call(this, settings);
    this.items = [];
    this.panel = null;
    this.isPanelOpen = null;
    this.value = null;
    this.onChange = null;
    this.expressionContainer = null;
    this.variables = [];
    this.initialized = false;

    ExpressionFieldControl.prototype.initObject.call(this, settings);
};

ExpressionFieldControl.prototype = new Element();

ExpressionFieldControl.prototype.expressionType = {
    'ARITMETIC': 0,
    'LOGIC': 1,
    'GROUP': 2,
    'EVALUATION': 3,
    'VAR': 4,
    'CONST': 5,
    'SQL': 6,
    'INT': 7,
    'FLOAT': 8,
    'STRING': 9,
    'BOOL': 10,
    'DATE': 11
};

ExpressionFieldControl.prototype.expressions = [
    ["+", "-", "x", "/"],
    ["AND", "OR", "NOT"],
    ["(", ")"],
    ["==", "<", "<=", "=>", ">", "<>", "within", "not within"],
    [],
    ["NOW", "NULL"]
];

ExpressionFieldControl.prototype.initObject = function(settings) {
    var defaults = {
        variables: [],
        value: "[]", 
        onChange: null
    };

    $.extend(true, defaults, settings);

    this.setVariables(defaults.variables)
        .setValue(defaults.value);

    this.onChange = defaults.onChange;
    this.initialized = true;
};

ExpressionFieldControl.prototype.setVariables = function(variables) {
    if(variables.push && variables.pop) {
        this.variables = variables;
    }

    return this;
};

ExpressionFieldControl.prototype.clear = function() {
    while(this.items.length) {
        this.removeItem(this.items[0]);
    }

    return this;
};

ExpressionFieldControl.prototype.setValue = function(value) {
    var prevValue = this.value, i/*, label*/;
    this.value = value;

    if(this.value === prevValue) {
        return this;
    }

    this.clear();
    if(typeof value !== 'undefined' && value !== null && $.trim(value) !== "") {
        value = JSON.parse(this.value);
        for(i = 0; i < value.length; i++) {
            /*label = null;
            if(value[i].type === this.expressionType.STRING) {
                label = '"' + value[i].value.replace(/"/g, "\\\"").replace(/'/g, "\\\'") + '"';
            }*/
            this.addItem(new ExpressionComponent({
                    label: value[i].value,
                    value: label || value[i].value,
                    data: {
                        type: this.expressionType[value[i].type]
                    }
                }));
        }
    }

    return this;
};

ExpressionFieldControl.prototype.removeItem = function(item) {
    var i;
    for(i = 0; i < this.items.length; i++) {
        if(this.items[i] === item) {
            this.items.splice(i, 1);
            $(this.expressionContainer).find("input").eq(i).remove();
            $(this.expressionContainer).find("input").eq(i).select();
            break;
        }
    }
    this.updateValue();
    return this;
};

ExpressionFieldControl.prototype.validateInputValue = function(value) {
    var i = 0, type, j, label, valid;
    if(typeof value === 'undefined' || value === null || $.trim(value) === "") {
        return {
            valid: false
        };
    }
    if(/^\s*(\+|-)?\d+\s*$/.test(value)) {
        return {
            valid: true,
            type: this.expressionType.INT,
            value: value
        };
    } else if(/^\s*(\+|-)?\d+\.\d+\s*$/.test(value)) {
        return {
            valid: true,
            type: this.expressionType.FLOAT,
            value: value
        };
    } else if(/^\s*(true|false)\s*$/i.test(value)) {
        return {
            valid: true,
            type: this.expressionType.BOOL,
            value: value.toUpperCase()
        };
    } else if(/^\s*\d{4}-((0\d)|(1[0-2]))-(([0-2]\d)|(3[01]))\s*$/.test(value)) {
        label = value.split("-");
        label[0] = parseInt(label[0], 10);
        label[1] = parseInt(label[1], 10);
        label[2] = parseInt(label[2], 10);
        valid = true;
        switch(label[1]) {
            case 2:
                if((label[0] % 4 && label[0] % 100 != 0) || (label[0] % 400 === 0)) {
                    valid = !(label[2] > 29);
                } else {
                    valid = !(label[2] > 28);
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                valid = !(label[2] > 30);
                break;
            default:
                valid = !(label[2] > 31);
                break;
        }
        if(valid) {
            return {
                valid: true, 
                type: this.expressionType.DATE,
                value: value,
                label: value
            };
        }
    } else if(/("(?:[^"\\]|\\.)*")|('(?:[^'\\]|\\.)*')/.test(value)) {
        value = value.substr(1, value.length - 2);
        label = '"' + value.replace(/"/g, "\\\"").replace(/'/g, "\\\'") + '"';
        return {
            valid: true, 
            type: this.expressionType.STRING,
            value: value,
            label: label
        };
    } else {
        for(type in this.expressionType) {
            j = this.expressionType[type];
            if(this.expressions[j]) {
                for(i = 0; i < this.expressions[j].length; i++) {
                    if(value.toLowerCase() == this.expressions[j][i].toLowerCase()) {
                        return {
                            valid: true,
                            value: this.expressions[j][i],
                            type: j
                        };
                    }
                }
            }
        }
    }

    return {
        valid: false
    }   
};

ExpressionFieldControl.prototype.processInput = function(textbox) {
    var isValid, index = $(textbox.parentElement).find("input").index(textbox);

    textbox.value = $.trim(textbox.value);
    isValid = this.validateInputValue(textbox.value);
    if(isValid.valid) {
        $(textbox).val("").css("width", "1px");
        this.addItem(new ExpressionComponent({
            label: isValid.label || isValid.value,
            value: isValid.value,
            data: {
                type: isValid.type
            }
        }), index);
        return true;
    }

    return false;
};

ExpressionFieldControl.prototype.createTextInput = function() {
    var input = this.createHTMLElement("input"),
        that = this;
    
    input.type = "text";
    input.style.width = "1px";
    input.style.margin = "0px";
    input.style.padding = "0px";
    input.className = "multiple-item-input";

    return input;
};

ExpressionFieldControl.prototype.dispatchOnChangeCallback = function(prevValue) {
    if(typeof this.onChange === 'function' && this.initialized) {
        this.onChange.call(this, this.getValue(), prevValue);
    }

    return this;
};

ExpressionFieldControl.prototype.updateValue = function() {
    var i, value = "", prevValue = this.getValue();
    if(this.html) {
        for(i = 0; i < this.items.length; i++) {
            value += this.items[i].getLabel() + " ";
        }

        this.html.value = this.html.title = value;
    }
    this.value = JSON.stringify(this.getObject());

    if(this.value !== prevValue) {
        this.dispatchOnChangeCallback(prevValue);
    }

    return this;
};

ExpressionFieldControl.prototype.onItemClickHandler = function() {
    var that = this;
    return function() {
        var miniPanel = new MultipleItemPanel({
                belongsTo: this.getHTML()
            }), expressions = that.expressions[this.getData("type")], 
            i, buttons = [], item = this;

        for(i = 0; i < expressions.length; i++) {
            buttons.push({
                caption: expressions[i],
                onClick: function() {
                    $(miniPanel.html).remove();
                    delete miniPanel;
                    item.setValue(this.name).setLabel(this.name);
                    that.dispatchOnChangeCallback();
                }
            });
        }

        miniPanel.setButtons(buttons);

        document.body.appendChild(miniPanel.getHTML());
        miniPanel.open();
    };
};

ExpressionFieldControl.prototype.addItem = function(item, index) {
    var that = this, target, label;
    if(!(item instanceof ExpressionComponent)) {
        if(item.type === this.expressionType.STRING) {
            label = '"' +  item.value.replace(/"/g, "\\\"").replace(/'/g, "\\\'")  + '"';
        }
        item = new ExpressionComponent({
            label: label || item.value,
            value: item.value,
            showValueTooltip: false,
            data: {
                type: item.type
            }          
        }, this);
    } else {
        item.parent = this;
    }
    item.onChange = function(newValue, oldValue) {
        that.dispatchOnChangeCallback(oldValue);
    };
    item.onEdit = function() {
        var i;
        for(i = 0; i < that.items.length; i++) {
            if(that.items[i] !== this) {
                that.items[i].exitEditMode();
            }
        }
    };

    if(!index && index !== 0) {
        target = $(this.expressionContainer).find("input:focus").get(0);
        if(target) {
            index = $(this.expressionContainer).find('input').index(target);
        }
    }
    item.onRemove = function() {
        that.removeItem(this);
    };
    /*if(item.getData("type") !== this.expressionType.VAR && item.getData("type") !== this.expressionType.CONST) {
        item.onClick = this.onItemClickHandler();    
    }*/
    if(typeof index === 'undefined' || index === null || index >= this.items.length) {
        this.items.push(item);
        if(this.html) {
            this.expressionContainer.appendChild(item.getHTML());
            this.expressionContainer.appendChild(this.createTextInput());
            $(this.expressionContainer).find("input:last").focus().select();
        }
    } else {
        this.items.splice(index, 0, item);
        if(this.html) {
            if(index === 0) {
                $(this.expressionContainer).prepend(item.getHTML()).prepend(this.createTextInput());
            } else {
                $(this.expressionContainer).find("li").eq(index).before(item.getHTML()).before(this.createTextInput());   
            }
            $(this.expressionContainer).find("input").eq(index +  1).focus().select();
        }
    }
    if(target) {
        target.value = "";
        target.style.width = '1px';
    }

    this.updateValue();
    return this;
};

ExpressionFieldControl.prototype.onListItemSelected = function(type) {
    var that = this;
    return function(value, data) {
        var item = new ExpressionComponent({
            label: value,
            value: value,
            showValueTooltip: false,
            data: {
                type: type
            }
        });
        that.addItem(item);
    };
};

ExpressionFieldControl.prototype.onOperatorClickHandler = function() {
    var that = this;
    return function() {
        that.addItem(new ExpressionComponent({
            label: this.caption, 
            value: this.caption,
            showValueTooltip: false,
            data: {
                type: this.value
            }
        }));
    };
};

ExpressionFieldControl.prototype.setupPanel = function() {
    var that = this, expressionContainer, operatorsPanel, variablesList, constantList, buttons = [], i, type, constants = [], closeButton;
    if(!this.panel) {
        this.panel = new MultipleItemPanel({
            matchParentWidth: false,
            width: 24,
            belongsTo: this.html
        });
        $(this.panel.getHTML()).addClass('expression-field-panel');

        expressionContainer = this.createHTMLElement('ul');
        expressionContainer.className = 'multiple-item-container';
        this.expressionContainer = expressionContainer;
        this.expressionContainer.appendChild(this.createTextInput());

        for(type in this.expressionType) {
            if(type === 'CONST') {
                continue;
            }
            i = this.expressionType[type];
            if(this.expressions[i]) {
                for(j = 0; j < this.expressions[i].length; j++) {
                    buttons.push({
                        caption: this.expressions[i][j],
                        value: i
                    });
                }
            }
        }

        for(i = 0; i < this.expressions[this.expressionType.CONST].length; i++) {
            constants.push({
                label: this.expressions[this.expressionType.CONST][i],
                value: this.expressions[this.expressionType.CONST][i]
            });
        }

        operatorsPanel = new MultipleItemButtonPanel({
            label: "",
            fallbackOnClickHandler: this.onOperatorClickHandler(),
            buttons: buttons
        });

        variablesList = new MultipleItemListSubpanel({
            title: "Variables",
            collapsable: true,
            items: this.variables,
            onItemSelect: this.onListItemSelected(this.expressionType.VAR)
        });
        constantList = new MultipleItemListSubpanel({
            title: "Constants",
            collapsable: true,
            items: constants,
            onItemSelect: this.onListItemSelected(this.expressionType.CONST),
            onOpen: function() {
                variablesList.close();
            }
        });
        variablesList.onOpen = function() {
            constantList.close();
        };
        this.panel.getHTML().appendChild(expressionContainer);
        this.panel.getHTML().appendChild(operatorsPanel.getHTML());
        this.panel.getHTML().appendChild(variablesList.getHTML());
        this.panel.getHTML().appendChild(constantList.getHTML());
    }
    return this;
};

ExpressionFieldControl.prototype.createHTML = function() {
    var controlObject, textbox, panel, i;

    if(this.html) {
        return this.html;
    }

    textbox = this.createHTMLElement('input');
    textbox.type = 'text';
    textbox.readOnly = true;
    this.html = textbox;

    this.updateValue();
    this.setupPanel();

    for(i = 0; i < this.items.length; i++) {
        this.expressionContainer.appendChild(this.items[i].getHTML());
        this.expressionContainer.appendChild(this.createTextInput());
    }

    this.attachListeners();

    return this.html;
};

ExpressionFieldControl.prototype.selectInput = function() {
    //$(this.html).find("input:last").select();
    return this;
};

ExpressionFieldControl.prototype.openPanel = function() {
    $(this.panel.html).addClass('focused');

    //this.html.parentElement.parentElement.parentElement.appendChild(this.panel.getHTML());
    $(document.body).append(this.panel.getHTML());
    this.panel.open();
    $(this.expressionContainer).trigger("click");
    $(this.controlObject).addClass("focused");
    this.isPanelOpen = true;
    return this;
};

ExpressionFieldControl.prototype.closePanel = function() {
    var i;
    this.panel.close();
    $(this.controlObject).removeClass('focused');
    for(i = 0; i < this.items.length; i++) {
        this.items[i].exitEditMode();
    }
    this.isPanelOpen = false;
    return this;
};

ExpressionFieldControl.prototype.updateTextInput = function(textInput, text) {
    var ffamily = $(textInput).css("font-family"), fsize = $(textInput).css("font-size"),
        w = this.calculateWidth(text || textInput.value, fsize + " " + ffamily);
    textInput.style.width = (w || 1) + "px";

    return this;
};

ExpressionFieldControl.prototype.checkExternalClickHandler = function() {
    var that = this;

    return function(e) {
        if(!that.isPanelOpen) {
            return;
        }
        var $target = $(e.target),
            panelID = that.panel.html.id;

        if($target[0].id !== panelID && $target.parents('#' + that.panel.html.id).length === 0) {
            that.closePanel();
        }
    };
};

ExpressionFieldControl.prototype.attachListeners = function() {
    var that = this;
    if(!this.html) {
        return this;
    }

    $(this.html).on('focus', function() {
        $(this).addClass('focused');
        that.selectInput().openPanel();
    }).on('keydown click', function(e) {
        if(e.which === 27 || e.which === 9) {
            that.closePanel();
        } else {
            that.openPanel();
        }
    });

    $(this.expressionContainer).on('click', function(e) {
        $(this).find('input:last').select();
    }).on('keydown', '.multiple-item-input', function(e) {
        if(e.which === 13) {
            if(!that.processInput(this)) {
                this.select();
            }
        } else if(e.which === 37 && this.value === "") {
            e.preventDefault();
            $(this).prev().prev().select();
        } else if(e.which === 39 && this.value === "") {
            e.preventDefault();
            $(this).next().next().select();
        } else if(e.which === 27) {
            $(that.controlObject).focus();
            that.closePanel();
        }
    }).on('keypress', '.multiple-item-input', function(e) {
        that.updateTextInput(this, this.value + String.fromCharCode(e.which));
    }).on('keyup', '.multiple-item-input', function(e) {
        that.updateTextInput(this);
    }).on('blur', '.multiple-item-input', function(e) { 
        if(!that.processInput(this)) {
            this.value = "";
            this.style.width = "1px";
        }
    }).on('click', '.multiple-item-input', function(e) {
        e.stopPropagation();
    });

    $(this.html.parentElement).on('scroll', function() {
        that.closePanel();
    });

    $(document).on('mousedown', this.checkExternalClickHandler());

    return this;
};

ExpressionFieldControl.prototype.isValid = function() {
    var valid = true, pendingClose = 0, prev = null, i, type;

    for(i = 0; i < this.items.length; i++) {
        if(i === 0) {
            switch(this.items[i].getData("type")) {
                case this.expressionType.ARITMETIC:
                    valid = false;
                    break;
                case this.expressionType.GROUP:
                    valid = this.items[i].getValue() === '(';
                        pendingClose++;
                    break;
                case this.expressionType.LOGIC:
                    valid = this.items[i].getValue() === 'NOT';
                    break;
                case this.expressionType.EVALUATION:
                    valid = false;
                    break;
                case this.expressionType.CONST:
                case this.expressionType.INT:
                case this.expressionType.FLOAT:
                case this.expressionType.BOOL:
                case this.expressionType.STRING:
                case this.expressionType.DATE:
                case this.expressionType.VAR:
                    valid = true;
                    break;
            } 
        } else {
            switch(this.items[i].getData("type")) {
                case this.expressionType.ARITMETIC:
                case this.expressionType.EVALUATION:
                    if(!(prev.type !== this.expressionType.ARITMETIC && (prev.type !== this.expressionType.GROUP || prev.value !== "(") && prev.type !== this.expressionType.LOGIC && prev.type !== this.expressionType.EVALUATION)) {
                        valid =false;
                    }
                    break;
                case this.expressionType.GROUP:
                    if(this.items[i].getValue() === '(') {
                        pendingClose++;
                        if(prev === null) {
                            valid =true;
                        } else if(!(prev.type !== this.expressionType.CONST && prev.type !== this.expressionType.VAR && (prev.type !== this.expressionType.GROUP || prev.value !== ")") && prev.type !== this.expressionType.INT && prev.type !== this.expressionType.FLOAT && prev.type !== this.expressionType.BOOL && prev.type !== this.expressionType.DATE && prev.type !== this.expressionType.STRING)) {
                            valid =false;
                        }
                    } else {
                        pendingClose--;
                        if(!(prev.type !== this.expressionType.ARITMETIC && (prev.type !== this.expressionType.GROUP || prev.value !== '(') && prev.type !== this.expressionType.LOGIC && prev.type !== this.expressionType.EVALUATION)) {
                            valid =false;
                        }
                    }
                    break;
                case this.expressionType.LOGIC:
                    if(this.items[i].getValue() === "NOT") {
                        if(prev === null) {
                            valid =true;
                        } else if((prev.type === this.expressionType.LOGIC && prev.value === "NOT") || (prev.type === this.expressionType.GROUP && prev.value === ")")) {
                            valid =false;
                        }
                    } else {
                        //if(prev === null || !(prev.type === this.expressionType.VAR || prev.type === this.expressionType.CONST || (prev.type === this.expressionType.GROUP && prev.value === ")"))) {
                        if(!(prev.type !== this.expressionType.ARITMETIC && (prev.type !== this.expressionType.GROUP || prev.value !="(") && prev.type !== this.expressionType.LOGIC && prev.type !== this.expressionType.EVALUATION)) {
                            valid =false;
                        }
                    }
                    break;
                case this.expressionType.CONST:
                case this.expressionType.INT:
                case this.expressionType.FLOAT:
                case this.expressionType.BOOL:
                case this.expressionType.STRING:
                case this.expressionType.DATE:
                case this.expressionType.VAR:
                    if(prev === null) {
                        valid =true;
                    } else if(!((prev.type !== this.expressionType.GROUP || prev.value !== ')') && prev.type !== this.expressionType.CONST && prev.type !== this.expressionType.VAR && prev.type !== this.expressionType.INT && prev.type !== this.expressionType.FLOAT && prev.type !== this.expressionType.BOOL && prev.type !== this.expressionType.STRING && prev.type !== this.expressionType.DATE)) {
                        valid =false;
                    }
                    break;
            }  
        }

        if(i === this.items.length - 1 && valid) {
            switch(this.items[i].getData("type")) {
                case this.expressionType.ARITMETIC:
                    valid = false;
                    break;
                case this.expressionType.GROUP:
                    valid = this.items[i].getValue() === ')';
                    break;
                case this.expressionType.LOGIC:
                    valid = false;
                    break;
                case this.expressionType.EVALUATION:
                    valid = false;
                    break;
                case this.expressionType.CONST:
                case this.expressionType.INT:
                case this.expressionType.FLOAT:
                case this.expressionType.BOOL:
                case this.expressionType.STRING:
                case this.expressionType.DATE:
                case this.expressionType.VAR:
                    valid = true;
                    break;
            } 
        }

        if(!valid) {
            break;
        }

        prev = {
            type: this.items[i].getData("type"),
            value: this.items[i].getValue()
        };
    }

    return valid && (pendingClose === 0);
};

ExpressionFieldControl.prototype.getValue = function() {
    return this.value;
};

ExpressionFieldControl.prototype.getObject = function() {
    var json = [], i, helper = [];

    for(i in this.expressionType) {
        helper[this.expressionType[i]] = i; 
    }

    for(i = 0; i < this.items.length; i++) {
        json.push({
            value: this.items[i].getValue(),
            type: helper[this.items[i].getData("type")]
        });
    }

    return json;
};

ExpressionFieldControl.prototype.remove = function() {
    $(this.html).remove();
    this.panel.remove();
    $(this.expressionContainer).remove();
    delete this.items;
    delete this.isPanelOpen;
    delete this.value;
    delete this.onChange;
    delete this.variables;
};

var ExpressionComponent = function(settings, parent) {
    SingleItem.call(this, $.extend(true, {editable: true}, settings));
    this.parent = parent;
    this.onChange = settings.onChange || null;
};

ExpressionComponent.prototype = new SingleItem();

ExpressionComponent.prototype.prepareEditionPanel = function() {
    var i, type = this.getData("type"), that = this;
    this.panel.clear();
    if(type === ExpressionFieldControl.prototype.expressionType.VAR) {
        this.panel.addSubpanel({
            title: "Variables",
            items: this.parent.variables,
            visibleHeader: false,
            onItemSelect: function(value) {
                that.setValue(value);
                that.setLabel(value);
                that.parent.updateValue();
                that.exitEditMode();
            }
        }, 'list');
        this.panel.matchParentWidth = false;
    } else if(type <= ExpressionFieldControl.prototype.expressionType.INT) {
        if(ExpressionFieldControl.prototype.expressions[type]) {
            for(i = 0; i < ExpressionFieldControl.prototype.expressions[type].length; i++) {
                if(ExpressionFieldControl.prototype.expressions[type][i] !== this.getValue()) {
                    this.panel.addButton({
                        caption: ExpressionFieldControl.prototype.expressions[type][i],
                        data: { value: ExpressionFieldControl.prototype.expressions[type][i] },
                        onClick: function() {
                            that.setValue(this.data.value);
                            that.setLabel(that.getValue());
                            that.parent.updateValue();
                            that.exitEditMode();
                        }
                    });
                }
            }
        }
    } else {

    }
    /*switch(this.getData("type")) {
        case ExpressionFieldControl.prototype.expressionType.ARITMETIC:
            
            break;
        case ExpressionFieldControl.prototype.expressionType.LOGIC:

            break;
        case ExpressionFieldControl.prototype.expressionType.EVALUATION:

            break;
        case ExpressionFieldControl.prototype.expressionType.VAR:

            break;
        case ExpressionFieldControl.prototype.expressionType.CONST:

            break;
        case ExpressionFieldControl.prototype.expressionType.SQL:

            break;
        default:
            break;
    }

    document.body.appendChild(this.panel.getHTML());*/
    $(this.panel.getHTML()).css("width", "150px");
    return this;
};
var DecisionTable = function(options) {
    Element.call(this, {id: options.id});
    this.base_module = null;
    this.hitType = null;
    this.dom = null;
    this.name = null;
    this.proxy = null;
    this.conditions = null;
    this.conclusions = null;
    this.decisionRows = null;
    this.rows = null;
    this.width = null;
    this.onAddColumn = null;
    this.onRemoveColumn = null;
    this.onChange = null;
    this.isDirty = null;
    this.fields = [];
    DecisionTable.prototype.initObject.call(this, options || {});
};

DecisionTable.prototype = new Element();

DecisionTable.prototype.type = 'DecisionTable';

DecisionTable.prototype.initObject = function(options) {
    var defaults = {
        name: "",
        proxy: new RestProxy({
            url: SUGAR_URL + '/rest/v10/CrmData/fields/',
            uid: null,
            restClient: new RestClient(),
            callback: null
        }),
        restClient: null,
        base_module: "",
        type: 'multiple',
        width: 'auto',
        rows: 0,
        container: null,
        columns: {
            conditions: [],
            conclusions: []
        },
        ruleset: [],
        onAddColumn: null,
        onRemoveColumn: null,
        onChange: null
    };

    $.extend(true, defaults, options);

    this.dom = {};
    this.conclusions = [];
    this.conditions = [];
    this.decisionRows = 0;
    this.onAddColumn = defaults.onAddColumn;
    this.onRemoveColumn = defaults.onRemoveColumn;
    this.onChange = defaults.onChange;
    this.rows = parseInt(defaults.rows, 10);

    this.setName(defaults.name)
        .setProxy(defaults.proxy, defaults.restClient)
        .setBaseModule(defaults.base_module)
        .setHitType(defaults.type)
        .setConditions(defaults.columns.conditions)
        .setConclusions(defaults.columns.conclusions)
        .setWidth(defaults.width);

    this.getHTML();
    if(defaults.container) {
        $(defaults.container).append(this.getHTML());

        if(!this.isDOMNodeInsertedSupported) {
            this.updateDimensions();
        }
    }

    this.setRuleset(defaults.ruleset);
    if(!this.conditions.length) {
        this.addCondition();
    }

    if(!this.conclusions.length) {
        this.addConclusion(true);
    }

    if(!this.decisionRows) {
        this.addDecisionRow();
    }

    this.setIsDirty(false);
};

DecisionTable.prototype.getIsDirty = function() {
    return this.dirty;
};

DecisionTable.prototype.setIsDirty = function(dirty) {
    this.isDirty = dirty;
    if(this.html) {
        this.dom.dirtyIndicator.textContent = dirty ? '*' : '';
    }
    return this;
};

DecisionTable.prototype.onChangeVariableHandler = function() {
    var that = this;
    return function(newVal, oldVal) {
        var valid, cell = this.getHTML(),
            index = $(cell.parentElement).find(cell.tagName.toLowerCase()).index(cell);
        if(this.mode === 'condition') {
            valid = that.validateColumn(index, 0);
        } else {
            valid = that.validateColumn(index, 1);
        }

        that.setIsDirty(true);

        if(typeof that.onChange === 'function') {
            that.onChange.call(that, {
                object: this,
                newVal: newVal,
                oldVal: oldVal
            }, valid);
        }
    };
};

DecisionTable.prototype.onChangeValueHandler = function() {
    var that = this;
    return function(valueObject, newVal, oldVal) {
        var row, cell, index, indexColumn, isEvaluationVariable, valid;

        isEvaluationVariable = valueObject instanceof DecisionTableValueEvaluation;
        cell = isEvaluationVariable ? valueObject.getHTML()[0] : valueObject.getHTML();
        row = cell.parentElement;
        indexColumn = $(cell.parentElement).find("td").index(cell) / (isEvaluationVariable ? 2 : 1);
        index = $(row.parentElement).find("tr").index(row);

        /*valid = valueObject.isValid();*/

        //if(valid.valid) {
            valid = that.validateColumn(indexColumn, isEvaluationVariable ? 0 : 1);
            if(valid.valid) {
                valid = that.validateRow(index);
            }
       /* } else {
            valid.location = (isEvaluationVariable ? 'Condition' : 'Conclusion') + " # " + (indexColumn + 1) + " - row # " + (index + 1);
        }*/
        that.setIsDirty(true);
        if(typeof that.onChange === 'function') {
            that.onChange.call(that, {
                object: valueObject,
                newVal: newVal,
                oldVal: oldVal
            }, valid);
        }
    };
};

DecisionTable.prototype.removeAllConclusions = function() {
    while(this.conclusions.length) {
        this.conclusions[0].remove();
    }

    return this;
};

DecisionTable.prototype.removeAllConditions = function() {
    while(this.conditions.length) {
        this.conditions[0].remove();
    }
    return this;
};

DecisionTable.prototype.setConditions = function(conditions) {
    var i;
    this.removeAllConditions();
    for(i = 0; i < conditions.length    ; i++) {
        this.addCondition(conditions[i]);
    }
    return this;
};

DecisionTable.prototype.setConclusions = function(conclusions) {
    var i;
    this.removeAllConclusions();
    for(i = 0; i < conclusions.length; i++) {
        this.addConclusion(!conclusions[i], conclusions[i]);
    }
    return this;
};

DecisionTable.prototype.setRuleset = function(ruleset) {
    var i, j, 
        condition_column_helper = {},  
        conclusion_column_helper = {},
        aux, 
        conditions, conclusions;

    //fill the column helper for conditions
    for(i = 0; i < this.conditions.length; i++) {
        if(!condition_column_helper[this.conditions[i].select.value]) {
            condition_column_helper[this.conditions[i].select.value] = [i];
        } else {
            condition_column_helper[this.conditions[i].select.value].push(i);
        }
    }

    conclusion_column_helper.result = 0;
    for(i = 1; i < this.conclusions.length; i++) {
        conclusion_column_helper[this.conclusions[i].select.value] = i
    }

    for(i = 0; i < ruleset.length; i++) {
        conditions = ruleset[i].conditions;
        aux = {};
        for(j = 0; j < conditions.length; j++) {
            if(typeof aux[conditions[j].variable_name] === 'undefined') {
                aux[conditions[j].variable_name] = -1;
            }
            aux[conditions[j].variable_name] ++;
            this.conditions[condition_column_helper[conditions[j].variable_name][aux[conditions[j].variable_name]]].addValue(conditions[j].value, conditions[j].condition);
        }

        conclusions = ruleset[i].conclusions;
        for(j = 0; j < conclusions.length; j++) {
            this.conclusions[conclusion_column_helper[conclusions[j].conclusion_value]].addValue(conclusions[j].value);
        }

        this.addDecisionRow();
    }

    this.updateDimensions();
    return this;
};

DecisionTable.prototype.isDOMNodeInsertedSupported = function() {
    var div = this.createHTMLElement('div'), supported = false;
    div.addEventListener('DOMNodeInserted', function() { supported = true; });
    div.appendChild(div.cloneNode());

    return supported;
};

DecisionTable.prototype.setRows = function(rows) {
    this.rows = parseInt(rows, 10);
    return this.updateDimensions();
};

DecisionTable.prototype.setWidth = function(w) {
    this.width = w;
    return this.updateDimensions();
}

DecisionTable.prototype.updateDimensions = function() {
    if(!this.html) {
        return this;
    }
    var w, w_cond, w_conc, index_w;

    if(this.width !== 'auto') {
        index_w = $(this.dom.indexTableContainer).outerWidth() + 4;
        w = (this.width - index_w) / (this.conditions.length + this.conclusions.length);
        w_cond = $(this.dom.conditionsTable).css("width", "").outerWidth();
        w_conc = $(this.dom.conclusionsTable).css("width", "").outerWidth();
        w = w_cond + w_conc;
        w_cond = Math.floor(w_cond / w * (this.width - index_w));
        w_conc = this.width - index_w - w_cond; 
    } else {
         $(this.dom.conditionsHeader.parentElement).css("width", "").find('th').css("width", "");
         $(this.dom.conclusionsTable).css("width", "");
         $(this.dom.conclusionsHeader.parentElement).css("width", "").find('th').css("width", "");
    }

    this.dom.conditionsTableContainer.style.width = this.dom.conditionsHeaderContainer.style.width = this.width !== 'auto' ? w_cond + "px" : "auto";
    this.dom.conclusionsTableContainer.style.width = this.dom.conclusionsHeaderContainer.style.width = this.width !== 'auto' ? w_conc + "px" : "auto";

    if(this.decisionRows && this.rows) {
        w = $(this.dom.conditionsTable).find("tr").outerHeight();
        this.dom.indexTableContainer.style.height = this.dom.conditionsTableContainer.style.height = this.dom.conclusionsTableContainer.style.height = ((w * this.rows) + 10 + this.rows) + "px";
    } else {
        this.dom.indexTableContainer.style.height = this.dom.conditionsTableContainer.style.height = this.dom.conclusionsTableContainer.style.height = "auto";    
    }

    w = $(this.dom.conditionsTable).outerWidth();
    if(w < $(this.dom.conditionsTableContainer).width() && this.width !== 'auto') {
        this.dom.conditionsTable.style.width = "100%";
        w = $(this.dom.conditionsTable).outerWidth();
        w = Math.ceil(w/2) * 2;
    }
    $(this.dom.conditionsHeader.parentElement).css("width", w + "px");
    w = Math.floor(w / this.conditions.length);
    $(this.dom.conditionsHeader).find('th').css("width", w + "px");

    w = $(this.dom.conclusionsTable).outerWidth();
    if(w < $(this.dom.conclusionsTableContainer).width() && this.width !== 'auto') {
        this.dom.conclusionsTable.style.width = "100%";
        w = $(this.dom.conclusionsTable).outerWidth();
        w = Math.ceil(w/2) * 2;
    }
    $(this.dom.conclusionsHeader.parentElement).css("width", w + "px");
    w = Math.floor(w / this.conclusions.length);
    $(this.dom.conclusionsHeader).find("th").css("width", w + "px");

    return this;
};

DecisionTable.prototype.createRemoveButton = function() {
    var button = this.createHTMLElement('span');
    button.tabIndex = 0;
    button.className = 'decision-table-remove';
    $(button).attr("title", "Remove row").append(document.createTextNode("-"));
    return button;
};

DecisionTable.prototype.addDecisionRow = function () {
    var row = this.createHTMLElement('tr'), i, aux;

    if(!(this.conditions.length && this.conclusions.length)) {
        return this;
    }

    for(i = 0; i < this.conditions.length; i++) {
        if(!this.conditions[i].values[this.decisionRows]) {
            this.conditions[i].addValue();
        }
        aux = this.conditions[i].getValueHTML(this.conditions[i].values.length - 1);
        row.appendChild(aux[0]);
        row.appendChild(aux[1]);
    }
    this.dom.conditionsTable.appendChild(row);

    row = row.cloneNode(false);
    for(i = 0; i < this.conclusions.length; i++) {
        if(!this.conclusions[i].values[this.decisionRows]) {
            this.conclusions[i].addValue();
        }
        row.appendChild(this.conclusions[i].getValueHTML(this.conclusions[i].values.length - 1));
    }
    this.dom.conclusionsTable.appendChild(row);

    row = row.cloneNode(false);
    aux = this.createRemoveButton(); 
    this.decisionRows++;
    i = this.createHTMLElement("td");
    i.appendChild(aux);
    row.appendChild(i);
    this.dom.indexTable.appendChild(row);

    if(this.decisionRows === 1) {
        this.updateDimensions();
    }

    return this;
};

DecisionTable.prototype.removeDecisionRow = function(index) {
    var i, asked = false, cancelled = false, valid;

    for(i = 0; i < this.conditions.length; i++) {
        if(this.conditions[i].values[index].filledValue()) {
            cancelled = !confirm("Do you really want to delete this rule set?");
            asked = true;
            break;
        }
    }        

    if(cancelled) {
        return this;   
    } else if(!asked){
        for(i = 0; i < this.conclusions.length; i++) {
            if(this.conclusions[i].values[index].filledValue()) {
                if(!confirm("Do you really want to delete this rule set?")) {
                    return this;
                }
                break;
            }   
        }
    }

    for(i = 0; i < this.conclusions.length; i++) {
        this.conclusions[i].removeValue(index);
    }

    for(i = 0; i < this.conditions.length; i++) {
        this.conditions[i].removeValue(index);
    }

    $(this.dom.indexTable).find('tr:eq(' + index + ')').remove();
    $(this.dom.conditionsTable).find('tr:eq(' + index + ')').remove();
    $(this.dom.conclusionsTable).find('tr:eq(' + index + ')').remove();

    this.decisionRows --;
    this.setIsDirty(true);

    valid = this.validateColumn();

    if(typeof this.onChange === 'function') {
        this.onChange.call(this, {}, valid);
    }

    return this;
};

DecisionTable.prototype.getFields = function() {
    var i = 0;

    if(this.fields.length) {
        return this.fields;
    }

    this.proxy.uid = this.base_module;
    data = this.proxy.getData();

    if(data && data.success) {
        this.fields = [];
        for(i = 0; i < data.result.length; i++) {
            this.fields.push({
                label: data.result[i].text,
                value: data.result[i].value
            });
        }
    }

    return this.fields;
};

DecisionTable.prototype.setName = function(name) {
    this.name = name;
    return this;
};

DecisionTable.prototype.setProxy = function(proxy, restClient) {
    this.proxy = proxy;
    if(restClient instanceof RestClient) {
        this.proxy.restClient = restClient;
    }
    return this;
};

DecisionTable.prototype.setBaseModule = function(base_module) {
    this.base_module = base_module;
    return this;
};

DecisionTable.prototype.setHitType = function(hitType) {
    this.hitType = hitType;
    return this;
};

DecisionTable.prototype.onRemoveVariableHandler = function(array) {
    var that = this, variablesArray = array, valid;
    return function() {
        var x;
        for(var i = 0; i < variablesArray.length; i++) {
            if(variablesArray[i] === this) {
                x = variablesArray[i];
                variablesArray.splice(i, 1);
            }
        }
        that.updateDimensions();
        valid = that.validateRow();
        if(typeof that.onRemoveColumn === 'function') {
            that.onRemoveColumn.call(this, x);
        }
        that.setIsDirty(true);
        if(typeof that.onChange === 'function') {
            that.onChange.call(that, {}, valid);
        }
    };
};

DecisionTable.prototype.addCondition = function(defaultValue) {
    var condition = new DecisionTableVariable({
        parent: this, 
        value: defaultValue || null,
        fields: this.getFields()
    }), i, html;

    condition.onRemove = this.onRemoveVariableHandler(this.conditions);
    condition.onChangeValue = this.onChangeValueHandler();
    condition.onChange = this.onChangeVariableHandler();
    this.conditions.push(condition);
    if(this.html) {
        this.dom.conditionsHeader.appendChild(condition.getHTML());
    }

    this.proxy.uid = this.base_module || "";

    for(i = 0; i < this.decisionRows; i++) {
        condition.addValue();
        html = condition.getValueHTML(i);
        $(this.dom.conditionsTable).find("tr:eq(" + i + ")").append(html[0]).append(html[1]);
    }

    this.updateDimensions();
    this.setIsDirty(true);

    if(typeof this.onAddColumn === 'function') {
        this.onAddColumn.call(this, condition);
    }

    return this;
};

DecisionTable.prototype.addConclusion = function (returnType, defaultValue) {
    var conclusion = new DecisionTableVariable({
        returnType: returnType,
        mode: "conclusion",
        fields: this.getFields(),
        value: defaultValue,
        parent: this
    }), i;
    conclusion.onRemove = this.onRemoveVariableHandler(this.conclusions);
    conclusion.onChangeValue = this.onChangeValueHandler();
    conclusion.onChange = this.onChangeVariableHandler();
    this.conclusions.push(conclusion);
    if(this.html) {
        this.dom.conclusionsHeader.appendChild(conclusion.getHTML());    
    }

    for(i = 0; i < this.decisionRows; i++) {
        conclusion.addValue();
        this.dom.conclusionsTable.childNodes[i].appendChild(conclusion.getValueHTML(i));
    }

    this.updateDimensions();
    this.setIsDirty(true);
    if(typeof this.onAddColumn === 'function') {
        this.onAddColumn.call(this, conclusion);
    }

    return this;
};

DecisionTable.prototype.createHTML = function() {
    if(this.html) {
        return this.html;
    }

    var table, row, cell, header, body, textContainer, subtable, button, i, span;

    //create the table header
    header = this.createHTMLElement('thead');
    row = this.createHTMLElement('tr');
    cell = this.createHTMLElement('th');
    cell.className = 'decision-table-title';
    cell.colSpan = 3;
    textContainer = this.createHTMLElement('div');
    span = this.createHTMLElement('span');
    span.appendChild(document.createTextNode((this.hitType === 'single' ? "[Single Hit]" : "[Multiple Hit]")));
    span.className = 'decision-table-type';
    span.tabIndex = 0;
    this.dom.hitTypeLabel = span;
    textContainer.appendChild(span);
    span = span.cloneNode(false);
    span.appendChild(document.createTextNode(this.name));
    span.className = 'decision-table-name';
    this.dom.nameLabel = span;
    textContainer.appendChild(span);
    span = span.cloneNode(false);
    $(span).removeAttr("tabIndex");
    span.textContent = this.isDirty ? '*' : '';
    textContainer.appendChild(span);
    this.dom.dirtyIndicator = span;
    span = span.cloneNode(false);
    span.className = 'decision-table-module';
    span.appendChild(document.createTextNode(this.base_module));
    textContainer.appendChild(span);
    
    cell.appendChild(textContainer);
    row.appendChild(cell);
    header.appendChild(row);
    this.dom.title = cell;

    //create the table subheaders
    row = this.createHTMLElement('tr');
    cell = this.createHTMLElement('th');
    row.appendChild(cell);
    cell = this.createHTMLElement('th');
    button = this.createHTMLElement('button');
    button.appendChild(document.createTextNode("+"));
    button.className = 'decision-table-add-button';
    this.dom.addConditionButton = button;
    textContainer = this.createHTMLElement('span');
    textContainer.appendChild(document.createTextNode("Conditions"));
    textContainer.appendChild(button);
    cell.appendChild(textContainer);
    cell.className = 'decision-table-separator-border';
    row.appendChild(cell);
    cell = cell.cloneNode(false);
    button = button.cloneNode(true);
    this.dom.addConclusionButton = button;
    textContainer = textContainer.cloneNode(false);
    textContainer.appendChild(document.createTextNode("Conclusions"));
    textContainer.appendChild(button);
    cell.appendChild(textContainer);
    row.appendChild(cell);
    header.appendChild(row);

    //create the body and the body header
    row = this.createHTMLElement("tr");
    cell = this.createHTMLElement('th');
    textContainer = this.createHTMLElement('button');
    textContainer.appendChild(document.createTextNode("+"));
    textContainer.title = "Add row";
    textContainer.className = 'decision-table-add-row';
    cell.appendChild(textContainer);
    row.appendChild(cell);
    cell = this.createHTMLElement('th');
    textContainer = this.createHTMLElement('div');
    textContainer.className = 'decision-table-conditions-header';
    this.dom.conditionsHeaderContainer = textContainer;
    subtable = this.createHTMLElement('table');
    subtable.appendChild(row.cloneNode(false));
    textContainer.appendChild(subtable);
    this.dom.conditionsHeader = subtable.childNodes[0];
    cell.className = 'decision-table-separator-border';
    cell.appendChild(textContainer);
    row.appendChild(cell);
    cell = cell.cloneNode(true);
    this.dom.conclusionsHeaderContainer = cell.childNodes[0];
    this.dom.conclusionsHeaderContainer.className = "decision-table-conclusions-header decision-table-scroll-y";
    this.dom.conclusionsHeader = this.dom.conclusionsHeaderContainer.childNodes[0].childNodes[0];
    row.appendChild(cell);
    body = this.createHTMLElement('tbody');
    body.appendChild(row);

    //create the cells in body that will contain the tables for data
    row = this.createHTMLElement('tr');
    cell = this.createHTMLElement('td');
    textContainer = textContainer.cloneNode(false);
    textContainer.className = 'decision-table-container';
    this.dom.indexTableContainer = textContainer;
    subtable = subtable.cloneNode(false);
    subtable.className = 'decision-table-index';
    this.dom.indexTable = subtable;
    textContainer.appendChild(subtable);
    cell.appendChild(textContainer);
    row.appendChild(cell);
    cell = cell.cloneNode(true);
    this.dom.conditionsTable = (this.dom.conditionsTableContainer = cell.childNodes[0]).childNodes[0];
    this.dom.conditionsTable.className = 'decision-table-conditions';
    cell.className = 'decision-table-separator-border';
    row.appendChild(cell);
    cell = cell.cloneNode(true);
    cell.className = "";
    this.dom.conclusionsTable = (this.dom.conclusionsTableContainer = cell.childNodes[0]).childNodes[0];
    $(this.dom.conclusionsTableContainer).addClass("decision-table-scroll-y");
    this.dom.conclusionsTable.className = 'decision-table-conclusions';
    row.appendChild(cell);
    body.appendChild(row);

    //create the table and append the header and body
    table = this.createHTMLElement('table');
    table.className = "decision-table";
    table.appendChild(header);
    table.appendChild(body);

    this.html = table;

    for(i = 0; i < this.conditions.length; i++) {
        this.dom.conditionsHeader.appendChild(this.conditions[i].getHTML());
    }

    for(i = 0; i < this.conclusions.length; i++) {
        this.dom.conclusionsHeader.appendChild(this.conclusions[i].getHTML());
    }

    this.attachListeners();

    return this.html;
};

DecisionTable.prototype.attachListeners = function() {
    var that = this;
    $(this.dom.conditionsTableContainer).on('scroll', function(){
        that.dom.conditionsHeaderContainer.scrollLeft = this.scrollLeft;
        that.dom.conclusionsTableContainer.scrollTop = this.scrollTop;
    });

    $(this.dom.conditionsHeaderContainer).on('scroll', function() {
        that.dom.conditionsTableContainer.scrollLeft = this.scrollLeft;
    });

    $(this.dom.conclusionsTableContainer).add(this.dom.conclusionsHeaderContainer).on('scroll', function(){
        that.dom.conclusionsHeaderContainer.scrollLeft = that.dom.conclusionsTableContainer.scrollLeft = this.scrollLeft;
        that.dom.indexTableContainer.scrollTop = that.dom.conditionsTableContainer.scrollTop = this.scrollTop;
    });

    $(this.dom.indexTableContainer).on('scroll', function() {
        that.dom.conditionsTableContainer.scrollTop = that.dom.conclusionsTableContainer.scrollTop = this.scrollTop;
    });

    $(this.dom.addConclusionButton).on('click', function() {
        that.addConclusion();
    });

    $(this.dom.addConditionButton).on('click', function() {
        that.addCondition();
    });

    $(this.dom.indexTable).on('click', 'span', function() {
        that.removeDecisionRow($(that.dom.indexTable).find("span").index(this));
    });

    $(this.dom.conditionsTable).on('keydown', 'td', function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                e.preventDefault();
                $(that.conclusions[0].getValueHTML(index)).find("span").focus();
            } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                e.preventDefault();
                $(that.dom.indexTable).find("span").eq(index).focus();
            }
        }
    });

    $(this.dom.indexTable).on("keydown", "td", function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if(!e.shiftKey) {
                e.preventDefault();
                $(that.conditions[0].getValueHTML(index)[0]).find("span").focus();
            } else if(index > 0){
                e.preventDefault();
                $(that.conclusions[that.conclusions.length - 1].getValueHTML(index - 1)).find("span").focus();
            }
        }
    });

    $(this.dom.conclusionsTable).on("keydown", "td", function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if($(row).find("td:last").get(0) === this && !e.shiftKey && index < that.decisionRows - 1) {
                e.preventDefault();
                $(that.dom.indexTable).find("span").eq(index + 1).focus();
            } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                e.preventDefault();
                $(that.conditions[that.conditions.length - 1].getValueHTML(index)[1]).find("span").focus();
            }
        }
    });

    $(this.dom.conditionsTable).on('keydown', 'td', function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                e.preventDefault();
                $(that.conclusions[0].getValueHTML(index)).find("span").focus();
            } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                e.preventDefault();
                $(that.dom.indexTable).find("button").eq(index).focus();
            }
        }
    });

    $(this.dom.indexTable).on('keydown', 'td', function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                e.preventDefault();
                $(that.conditions[0].getValueHTML(index)[0]).find("span").focus();
            } else if($(row).find("td:first").get(0) === this && e.shiftKey && index > 0){
                e.preventDefault();
                $(that.conclusions[that.conclusions.length - 1].getValueHTML(index - 1)).find('span').focus();
            }
        }
    });

    $(this.dom.conclusionsTable).on('keydown', 'td', function(e) {
        var index, row = this.parentElement;
        if(e.keyCode === 9) {
            index = $(row.parentElement).find("tr").index(row);
            if($(row).find("td:last").get(0) === this && !e.shiftKey && index < that.decisionRows - 1) {
                e.preventDefault();
                $(that.dom.indexTable).find("button").eq(index + 1).focus();
            } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                e.preventDefault();
                $(that.conditions[that.conditions.length - 1].getValueHTML(index)[1]).find("span").focus();
            }
        }
    });

    $(this.html).find('.decision-table-add-row').on('click', function() {
        that.addDecisionRow();
    });

    $(this.dom.nameLabel).on('focus', function() {
        var input = that.createHTMLElement('input');
        input.type = 'text';
        input.value = that.name;
        $(this).empty().append(input);
        $(input).select().focus();
    }).on('blur', 'input', function() {
        if(this.value) {
            that.name = $.trim(this.value);
        }
        $(this.parentElement).text(that.name);
    });

    $(this.dom.hitTypeLabel).on('focus', function() {
        var select = that.createHTMLElement('select'),
            option = that.createHTMLElement('option');

        option.label = 'Multiple Hit';
        option.value = 'multiple';
        option.appendChild(document.createTextNode(option.label));
        option.selected = that.hitType !== 'single';
        select.appendChild(option);

        option = option.cloneNode(false);
        option.label = 'Single Hit';
        option.value = 'single';
        option.appendChild(document.createTextNode(option.label));
        option.selected = that.hitType === 'single';
        select.appendChild(option);

        $(this).empty().append(select);
        $(select).focus();
    }).on('blur', 'select', function() {
        that.hitType = this.value;
        $(this.parentElement).text(that.hitType === 'single' ? '[Single Hit]' : '[Multiple Hit]');
    });

    $(this.dom.conditionsTable).add(this.dom.conclusionsTable).add(this.dom.indexTable).on("focus", "td", function() {
        var row = this.parentElement, index;
        $(that.html).find("tr.cell-edit").removeClass("cell-edit");
        index = $(row.parentElement).find("tr").index(row);
        $(that.dom.indexTable.childNodes[index]).add(that.dom.conditionsTable.childNodes[index]).add(that.dom.conclusionsTable.childNodes[index]).addClass("cell-edit");
    }).on("blur", "select, input", function(){
        //$(that.html).find("tr.cell-edit").removeClass("cell-edit");
    });

    $(document).bind('DOMNodeInserted', function(e) {
        if(e.target === that.html) {
            that.updateDimensions();
        }
    });

    return this;
};

DecisionTable.prototype.validateConclusions = function() {
    var i, obj = {};

    for(i = 0; i < this.conclusions.length; i++) {
        if(!this.conclusions[i].returnType && this.conclusions[i].select.value && this.conclusions[i].getFilledValuesNum()) {
            if(!obj[this.conclusions[i].select.value]) {
                obj[this.conclusions[i].select.value] = true;
            } else {
                $(this.conclusions[i].getHTML()).addClass('error');
                return {
                    valid: false,
                    location: "Conclusion # " + (i + 1),
                    message: "conclusion variable is duplicated"
                }
            }
        }
        $(this.conclusions[i].getHTML()).removeClass('error');
    }

    return {valid: true};
};

DecisionTable.prototype.validateRow = function(index) {
    var start = 0, limit = this.decisionRows, 
        rowHasConclusions, rowHasConditions, i, j;

    if(typeof index === 'number') {
        start = index;
        limit = index + 1;
    }

    for(i = start; i < limit; i++) {
        rowHasConditions = false;
        rowHasConclusions = false;
        //validate if the row has return value conclusion if there are any condition
        for(j = 0; j < this.conditions.length; j++) {
            if(this.conditions[j].values[i].filledValue()) {
                rowHasConditions = true;
                break;
            }
        }

        if(rowHasConditions) {
            if(!this.conclusions[0].values[i].filledValue()) {
                $(this.conclusions[0].values[i].getHTML()).addClass("error");
                return {
                    valid: false,
                    message: 'The "Return" conclusion is empty',
                    location: "row # " + (i + 1)
                };
            } else {
                rowHasConclusions = true;
            }
        }
        $(this.conclusions[0].values[i].getHTML()).removeClass("error");

        if(!rowHasConclusions) {
            for(j = 0; j < this.conclusions.length; j++) {
                if(this.conclusions[j].values[i].filledValue()) {
                    rowHasConclusions = true;
                    break;
                }
            }
        }
        if(rowHasConclusions && !rowHasConditions) {
                $(this.dom.conditionsTable).find('tr').eq(i).addClass('error');
                return {
                    valid: false,
                    message: 'No conditions were specified in row with conclusions',
                    location: 'row # ' + (i + 1)
                };
        }
        $(this.dom.conditionsTable).find('tr').eq(i).removeClass('error');
    }

    return {valid: true};
};

DecisionTable.prototype.validateColumn = function(index, type) {
    var valid, i, j, variables = [
        {
            type: "condition",
            collection: this.conditions
        }, {
            type: "conclusion",
            collection: this.conclusions
        }
    ];

    $(this.dom.conditionsTable).find('tr').removeClass('error');

    if(typeof index === 'number' && typeof type === 'number') {
        valid = variables[type].collection[index].isValid();
        if(!valid.valid) {
            return {
                valid: false,
                message: valid.message,
                location: variables[type].type + " # " + (index + 1) + (!isNaN(valid.index) ? " - row " + (valid.index + 1) : "")
            };
        }
    } else {
        for(j = 0; j < variables.length; j++) {
            for(i = 0; i < variables[j].collection.length; i++) {
                valid = variables[j].collection[i].isValid();
                if(!valid.valid) {
                    return {
                        valid: false,
                        message: valid.message,
                        location: variables[j].type + " # " + (i + 1) + (!isNaN(valid.index) ? " - row " + (valid.index + 1) : "")
                    };
                }
            }
        }
    }

    return {valid: true};
};

DecisionTable.prototype.isValid = function() {
    var valid = this.validateColumn();

    if(!valid.valid) {
        return valid;
    }
    valid = this.validateRow();
    if(!valid.valid) {
        return valid;
    }

    return this.validateConclusions();
};

DecisionTable.prototype.getJSON = function() {
    var json = {
        base_module: this.base_module,
        type: this.hitType,
        name: this.name,
        columns: {
            conditions: [],
            conclusions: []
        },
        ruleset: []
    }, ruleset, conditions, conclusions, i, j, obj;

    if(!this.isValid().valid) {
        return null;
    }

    //Add the conditions columns evaluating duplications
    obj = {};
    for(j = 0; j < this.decisionRows; j++) {
        for(i = 0; i < this.conditions.length; i++) {
            if(this.conditions[i].select.value && this.conditions[i].values[j].getValue().length) {
                if(!obj[this.conditions[i].select.value]) {
                    obj[this.conditions[i].select.value] = {
                        max: 0,
                        current: 0
                    };
                }
                obj[this.conditions[i].select.value].current ++;
                if(obj[this.conditions[i].select.value].current > obj[this.conditions[i].select.value].max) {
                    obj[this.conditions[i].select.value].max = obj[this.conditions[i].select.value].current;
                }
            }
        }
        for(i in obj) {
            obj[i].current = 0;
        }
    }
    for(i = 0; i < this.conditions.length; i++) {
        if(obj[this.conditions[i].select.value]) {
            for(j = 0; j < obj[this.conditions[i].select.value].max; j++) {
                json.columns.conditions.push(this.conditions[i].select.value);
            }
            delete obj[this.conditions[i].select.value];
        }
    }


    for(i = 0; i < this.conclusions.length; i++) {
        if(this.conclusions[i].returnType || (this.conclusions[i].select.value && this.conclusions[i].getFilledValuesNum())) {
            json.columns.conclusions.push(this.conclusions[i].select ? this.conclusions[i].select.value : "");
        }
    }

    for(i = 0; i < this.decisionRows; i++) {
        ruleset = {
            id: i + 1
        };
        conditions = [];
        conclusions = [];
        for(j = 0; j < this.conditions.length; j++) {
            obj = this.conditions[j].getJSON(i);
            if(obj) {
                conditions.push(obj);
            }
        }
        for(j = 0; j < this.conclusions.length; j++) {
            obj = this.conclusions[j].getJSON(i);
            if(obj.value.length) {
                conclusions.push(obj);
            }
        }
        ruleset.conditions = conditions;
        ruleset.conclusions = conclusions;
        if(conditions.length) {
            json.ruleset.push(ruleset);
        }
    }

    return json;
};

//DecisionTableVariable
    var DecisionTableVariable = function(options) {
        Element.call(this);
        this.values = null;
        this.name = null;
        this.value = null;
        this.parent = null;
        this.mode = null;
        this.returnType = null;
        this.select = null;
        this.closeButton = null;
        this.onRemove = null;
        this.onChange = null;
        this.onChangeValue = null;
        this.fields = [];
        DecisionTableVariable.prototype.initObject.call(this, options);
    };

    DecisionTableVariable.prototype = new Element();

    DecisionTableVariable.prototype.initObject = function(options) {
        var defaults = {
            values: [],
            name: null,
            value: null,
            parent: null,
            mode: "condition",
            returnType: false,
            onRemove: null,
            onChange: null,
            onChangeValue: null,
            fields: []
        };

        $.extend(true, defaults, options);
        this.value = defaults.value;
        this.values = [];
        this.parent = defaults.parent;
        this.mode = defaults.mode;
        this.onRemove = defaults.onRemove;
        this.onChange = defaults.onChange;
        this.onChangeValue = defaults.onChangeValue;
        this.returnType = defaults.returnType;
        this.setFields(defaults.fields)
            .setValues(defaults.values)
            .setName(defaults.name);

        if(!this.returnType) {
            this.select = this.createHTMLElement('select');
            this.updateSelect();
        }
    };

    DecisionTableVariable.prototype.updateSelect = function() {
        var i = 0, option;
        $(this.select).empty();
        if(this.fields.length) {
                option = this.createHTMLElement('option');
                this.select.appendChild(option);
            for(i = 0; i < this.fields.length; i++) {
                option = this.createHTMLElement('option');
                option.label = this.fields[i].label;
                option.value = this.fields[i].value;
                option.appendChild(document.createTextNode(this.fields[i].label));
                if(this.value === option.value) {
                    option.selected = true;
                }
                this.select.appendChild(option);
            }
        }

        return this;
    };

    DecisionTableVariable.prototype.setFields = function(fields) {
        if(fields.push && fields.pop) {
            this.fields = fields;
            if(this.select) {
                this.updateSelect();
            }
        }

        return this;
    };

    DecisionTableVariable.prototype.setName = function(name) {
        this.name = name;
        return this;
    };

    DecisionTableVariable.prototype.setValues = function(values) {
        if(typeof values != "object" || !values.push) {
            return this;
        }
        var i = 0;
        if(this.mode === 'conclusion') {
            for(i = 0; i < values.length; i++) {
                if(typeof values[i] === "string" || typeof values[i] === 'number') {
                    this.values.push(new DecisionTableSingleValue({value: values[i], parent: this, fields: this.fields}));
                }
            }
        } else {
            for(i = 0; i < values.length; i++) {
                this.values.push(new DecisionTableValueEvaluation({value: values[i].value, operator: values[i].operator, parent: this, fields: this.fields}));
            }
        }

        return this;
    };

    DecisionTableVariable.prototype.getValueHTML = function(index) {
        var cell, textContainer;

        if(this.values[index]) {
            return this.values[index].getHTML();
        }

        return null;
    };

    DecisionTableVariable.prototype.createHTML = function() {
        if(this.html) {
            return this.html;
        }

        var html = this.createHTMLElement('th'), content, closeButton;

        if(this.returnType) {
            content = this.createHTMLElement('span');
            content.className = 'decision-table-return';  
            content.appendChild(document.createTextNode(this.returnType ? "Return" : (this.name || "")));
        } else {
            content = this.select;
        }
        
        html.appendChild(content);

        if(!this.returnType) {
            closeButton = this.createHTMLElement("button");
            closeButton.appendChild(document.createTextNode(" "));
            closeButton.className = 'decision-table-close-button';
            closeButton.title = "Remove Column";
            this.closeButton = closeButton;
            html.appendChild(this.closeButton);
        }

        this.html = html;

        this.attachListeners();

        return html;
    };

    DecisionTableVariable.prototype.remove = function() {
        if(this.getFilledValuesNum()) {
            if(!confirm("Do you really want to remove this variable?")) {
                return;
            }

        }
        while(this.values.length) {
            this.values[0].remove();
        }
        this.values = null;
        $(this.html).remove();
        if(typeof this.onRemove === 'function') {
            this.onRemove.call(this);
        }
    };

    DecisionTableVariable.prototype.attachListeners = function() {
        var that = this;
        if(!this.html) {
            return this;
        }

        $(this.select).on('change', function(){
            var oldValue = that.value;
            that.name = $(this).find('option:selected').attr("label") || null;
            that.value = this.value || null;
            $(this).attr("title", that.name)
                .parent().removeClass("error");
            if(typeof that.onChange === 'function') {
                that.onChange.call(that, that.value, oldValue);
            }
        });

        $(this.closeButton).on("click", function() {
            that.remove();
        });

        return this;
    };

    DecisionTableVariable.prototype.getFilledValuesNum = function() {
        var i, n = 0;
        for(i = 0; i < this.values.length; i++) {
            if(this.values[i].filledValue()) {
                n ++;
            }
        }
        return n;
    };

    DecisionTableVariable.prototype.onRemoveValueHandler = function() {
        var that = this;
        return function() {
            var i; 
            for(i = 0; i < that.values.length; i++) {
                if(that.values[i] === this) {
                    that.values.splice(i, 1);
                    return;
                }
            }
        };
    };

    DecisionTableVariable.prototype.onChangeValueHandler = function() {
        var that = this;
        return function(newVal, oldVal) {
            if(typeof that.onChangeValue === 'function') {
                that.onChangeValue.call(that, this, newVal, oldVal);
            }
        };
    };

    DecisionTableVariable.prototype.addValue = function(value, operator) {
        var value;
        if(this.mode === 'conclusion') {
            value = new DecisionTableSingleValue({value: value, parent: this, fields: this.fields});
        } else {
            value = new DecisionTableValueEvaluation({value: value, operator: operator, parent: this, fields: this.fields});    
        }
        value.onRemove = this.onRemoveValueHandler();
        value.onChange = this.onChangeValueHandler();
        this.values.push(value);

        return this;
    };

    DecisionTableVariable.prototype.getJSON = function(index) {
        var json = {};
        if(typeof index === 'number') {
            if(this.values[index]) {

                json.value = this.values[index].getValue();

                if(this.mode === 'conclusion') {
                    json.conclusion_value = (this.returnType ? 'result' : this.select.value);
                    json.conclusion_type = this.returnType ? 'return' : 'variable'; //"expression" type also must be set
                } else {
                    json.variable_name = this.select.value;
                    json.condition = this.values[index].operator;
                    if(!(!json.value || json.condition) || (!json.value && !json.condition) /*|| (json.value.push && !json.value.length)*/)  {
                        return false;
                    }
                }

                return json;
            }
        } else {
            return false;
        }
    };

    DecisionTableVariable.prototype.removeValue = function(index) {
        if(this.values[index]) {
            $(this.values[index].getHTML()).remove();
            this.values.splice(index, 1);
        }

        return this;
    };

    DecisionTableVariable.prototype.isValid = function() {
        var valid = {
            valid: true
        }, i, values = 0, validation;
        $(this.select).parent().removeClass("error");
        if(this.mode === 'conclusion') {
            for(i = 0; i < this.values.length; i++) {
                validation = this.values[i].isValid();
                if(!validation.valid) {
                    return validation;
                }
                if(this.values[i].value.length) {
                    values ++;
                }
            }
        } else {
            for(i = 0; i < this.values.length; i++) {
                validation = this.values[i].isValid();
                if(this.values[i].operator) {
                    values ++;
                }
                if(!validation.valid) {
                    valid.valid = false;
                    valid.message = validation.message;
                    valid.index = i;
                    return valid;
                }
            }
        }

        if(values && (this.select && !this.select.value)) {
            $(this.select.parentElement).addClass("error");
            valid = {
                valid: false,
                message: "Any variable wasn't selected"
            };
        }

        return valid;
    };

//Value Cells for DecisionTable
    //DecisionTableValue
        var DecisionTableValue = function(settings) {
            Element.call(this);
            this.value = null;
            this.expression = null;
            this.onRemove = null;
            this.onChange = null;
            this.parent = null;
            DecisionTableValue.prototype.initObject.call(this, settings);
        };

        DecisionTableValue.prototype = new Element();

        DecisionTableValue.prototype.initObject = function(settings) {
            var defaults = {
                value: [],
                onRemove: null,
                onChange: null,
                parent: null,
                fields: []
            };
            $.extend(true, defaults, settings || {});
            this.parentElement = defaults.parent;
            this.expression = new ExpressionFieldControl({
                variables: defaults.fields,
                onChange: this.onChangeExpressionHandler()
            }, this);
            this.setValue(defaults.value);
            this.onRemove = defaults.onRemove;
            this.onChange = defaults.onChange;
        };

        DecisionTableValue.prototype.onChangeExpressionHandler = function() {
            var that = this;
            return function(newVal, oldVal) {
                that.value = this.getObject();
                if(typeof that.onChange === 'function') {
                    that.onChange.call(that, newVal, oldVal);
                }
            };
        };

        DecisionTableValue.prototype.updateHTML = function() {};

        DecisionTableValue.prototype.setValue = function(value) {
            var i;
            this.expression.clear();
            for(i = 0; i < value.length; i++) {
                this.expression.addItem({
                    value: value[i].value,
                    label: value[i].value,
                    type: ExpressionFieldControl.prototype.expressionType[value[i].type]
                });
            }
            this.value = value;
            this.updateHTML();
            return this;
        };

        DecisionTableValue.prototype.createHTML = function() {};

        DecisionTableValue.prototype.onEnterCellHandler = function(controlCreationFunction) {
            var that = this;
            return function() {
                if(typeof controlCreationFunction !== 'function') {
                    return;
                }
                var control = controlCreationFunction();
                $(this.parentElement).empty().append(control);
                $(control).select().focus();
            };
        };

        DecisionTableValue.prototype.onLeaveCellHandler = function(member) {
            var that = this;
            return function() {
                var span = document.createElement('span'),
                    cell = this.parentElement, oldValue = that[member], changed = false;
                span.tabIndex = 0;
                changed = oldValue !== this.value;
                that[member] = this.value;
                if(that[member]) {
                    span.appendChild(document.createTextNode(that[member]));    
                } else {
                    span.innerHTML = '&nbsp;';
                }
                try {
                    $(cell).empty().append(span);;
                } catch(e){}
                that.isValid();
                if(changed && typeof that.onChange === 'function') {
                    that.onChange.call(that, that[member], oldValue);
                }
            };
        };

        DecisionTableValue.prototype.isValid = function() {            
            if(this.expression.isValid()) {
                $(this.html).removeClass('error');
                return {
                    valid: true
                };
            } else {
                $(this.html).addClass('error');
                return {
                    valid: false,
                    message: "Invalid expression"
                }
            }
        };

        DecisionTableValue.prototype.attachListeners = function() {};

        DecisionTableValue.prototype.remove = function() {
            $(this.html).remove();
            this.expression.remove();
            if(typeof this.onRemove === 'function') {
                this.onRemove.call(this);
            }
        };

        DecisionTableValue.prototype.getValue = function() {
            return this.expression.getObject();
        };

        DecisionTableValue.prototype.filledValue = function() {
            return !!this.value.length;
        };

    //DecisionTableSingleValue
        var DecisionTableSingleValue = function(settings) {
            DecisionTableValue.call(this, settings);
        };

        DecisionTableSingleValue.prototype = new DecisionTableValue();

        DecisionTableSingleValue.prototype.createValueControl = function() {
            var that = this;
            return function() {
                var input = document.createElement('input');
                input.type = 'text';
                input.value = that.value || "";
                return input;
            };
        };

        DecisionTableSingleValue.prototype.updateHTML = function() {
            if(this.html) {
                if(this.value) {
                    $(this.html).find('span').text(this.value);
                } else {
                    $(this.html).find('span').html('&nbsp;');
                }
                $(this.html).find('input').val(this.value);
            }
            return this;
        };

        DecisionTableSingleValue.prototype.createHTML = function() {
            if(this.html) {
                return this.html;
            }

            var cell;

            cell = this.createHTMLElement('td');
            
            //span.tabIndex = 0; //<----remove
            cell.appendChild(this.expression.getHTML());

            this.html = cell;

            //this.attachListeners();

            return cell;
        };

    //DecisionTableValueEvaluation
        var DecisionTableValueEvaluation = function(settings) {
            DecisionTableValue.call(this, settings);
            this.operator = null;
            DecisionTableValueEvaluation.prototype.initObject.call(this, settings);
        };

        DecisionTableValueEvaluation.prototype = new DecisionTableValue();

        DecisionTableValueEvaluation.prototype.OPERATORS = ["=", ">=", "<=", ">", "<", "!=", "<>", "within", "not within"];
        
        DecisionTableValueEvaluation.prototype.initObject = function(settings) {
            this.setOperator(settings.operator || "");
        };

        DecisionTableValueEvaluation.prototype.setOperator = function(operator) {
            this.operator = operator;

            return this;
        };

        DecisionTableValueEvaluation.prototype.createHTML = function () {
            if(this.html) {
                return this.html;
            }

            var valueCell, operatorCell, span;
            valueCell = DecisionTableSingleValue.prototype.createHTML.call(this);
            
            operatorCell = this.createHTMLElement("td");
            operatorCell.className = 'decision-table-operator';
            span = this.createHTMLElement("span");
            span.tabIndex = 0;
            if(this.operator) {
                span.appendChild(document.createTextNode(this.operator));
            } else {
                span.innerHTML = '&nbsp';
            }
            operatorCell.appendChild(span);

            this.html = [operatorCell, valueCell];

            this.attachListeners();

            return this.html;
        };

        DecisionTableValueEvaluation.prototype.fillOperators = function(select) {
            var i, option;

            $(select).append('<option></option>');

            for(i = 0; i < this.OPERATORS.length; i++) {
                option = this.createHTMLElement("option");
                option.label = option.value = this.OPERATORS[i];
                option.appendChild(document.createTextNode(this.OPERATORS[i]));
                option.selected = this.OPERATORS[i] === this.operator;
                select.appendChild(option);
            }

            return select;
        };

        DecisionTableValueEvaluation.prototype.createValueControl = function() {
            var that = this;
            return function() {
                var input = document.createElement('input');
                input.type = 'text';
                input.value = that.value || "";
                return input;
            };
        };

        DecisionTableValueEvaluation.prototype.createOperatorControl = function() {
            var that = this;
            return function() {
                var select = document.createElement('select');
                that.fillOperators(select);
                select.value = that.operator;
                return select;
            };
        };

        DecisionTableValueEvaluation.prototype.attachListeners = function() {
            if(!this.html || !this.html.push) {
                return this;
            }

            $(this.html[0]).on('focus', 'span', this.onEnterCellHandler(this.createOperatorControl()))
                .on('blur', 'select', this.onLeaveCellHandler('operator'));

            return this;
        };

        DecisionTableValueEvaluation.prototype.filledValue = function() {
            return !!this.operator && DecisionTableValue.prototype.filledValue.call(this);
        };

        DecisionTableValueEvaluation.prototype.isValid = function() {
            var res = DecisionTableValue.prototype.isValid.call(this);

            if(!res.valid) {
                $(this.html[0]).removeClass('error');
            } else {
                res = {
                    valid: (!!this.value.length === !!this.operator)
                };
                if(!res.valid) {
                    $(this.html).addClass('error');
                    res.message = "missing expression or operator";
                } else {
                    $(this.html).removeClass('error');
                }
            }

            return res;
        };

        DecisionTableValueEvaluation.prototype.getOperator = function() {
            return this.operator;
        };

