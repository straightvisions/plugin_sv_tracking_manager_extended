window.screentime = (options) => {
    let defaults = {
        fields: [],
        percentOnScreen: "50%",
        reportInterval: 10,
        googleAnalytics: false,
        callback: function () {},
    };

    options = Object.assign({}, defaults, options);

    options.percentOnScreen = parseInt(
        options.percentOnScreen.replace("%", ""),
        10
    );

    let counter = {};
    let cache = {};
    let log = {};
    let looker = null;
    let reporter = null;
    let started = false;
    let universalGA = false;
    let classicGA = false;

    if (!options.fields.length) {
        return;
    }

    if (options.googleAnalytics) {
        if (typeof ga === "function") {
            universalGA = true;
        }

        if (typeof _gaq !== "undefined" && typeof _gaq.push === "function") {
            classicGA = true;
        }
    }

    /*!
     * visibly - v0.6 Aug 2011 - Page Visibility API Polyfill
     * http://github.com/addyosmani
     * Copyright (c) 2011 Addy Osmani
     * Dual licensed under the MIT and GPL licenses.
     *
     * Methods supported:
     * visibly.onVisible(callback)
     * visibly.onHidden(callback)
     * visibly.hidden()
     * visibly.visibilityState()
     * visibly.visibilitychange(callback(state));
     */

    (function () {
        window.visibly = {
            q: document,
            p: undefined,
            prefixes: ["webkit", "ms", "o", "moz", "khtml"],
            props: ["VisibilityState", "visibilitychange", "Hidden"],
            m: ["focus", "blur"],
            visibleCallbacks: [],
            hiddenCallbacks: [],
            genericCallbacks: [],
            _callbacks: [],
            cachedPrefix: "",
            fn: null,
            onVisible: function (i) {
                if (typeof i == "function") {
                    this.visibleCallbacks.push(i);
                }
            },
            onHidden: function (i) {
                if (typeof i == "function") {
                    this.hiddenCallbacks.push(i);
                }
            },
            getPrefix: function () {
                if (!this.cachedPrefix) {
                    for (var i = 0; (b = this.prefixes[i++]); ) {
                        if (b + this.props[2] in this.q) {
                            this.cachedPrefix = b;
                            return this.cachedPrefix;
                        }
                    }
                }
            },
            visibilityState: function () {
                return this._getProp(0);
            },
            hidden: function () {
                return this._getProp(2);
            },
            visibilitychange: function (i) {
                if (typeof i == "function") {
                    this.genericCallbacks.push(i);
                }
                var t = this.genericCallbacks.length;
                if (t) {
                    if (this.cachedPrefix) {
                        while (t--) {
                            this.genericCallbacks[t].call(
                                this,
                                this.visibilityState()
                            );
                        }
                    } else {
                        while (t--) {
                            this.genericCallbacks[t].call(this, arguments[0]);
                        }
                    }
                }
            },
            isSupported: function (i) {
                return this.cachedPrefix + this.props[2] in this.q;
            },
            _getProp: function (i) {
                return this.q[this.cachedPrefix + this.props[i]];
            },
            _execute: function (i) {
                if (i) {
                    this._callbacks =
                        i == 1 ? this.visibleCallbacks : this.hiddenCallbacks;
                    var t = this._callbacks.length;
                    while (t--) {
                        this._callbacks[t]();
                    }
                }
            },
            _visible: function () {
                window.visibly._execute(1);
                window.visibly.visibilitychange.call(window.visibly, "visible");
            },
            _hidden: function () {
                window.visibly._execute(2);
                window.visibly.visibilitychange.call(window.visibly, "hidden");
            },
            _nativeSwitch: function () {
                this[this._getProp(2) ? "_hidden" : "_visible"]();
            },
            _listen: function () {
                try {
                    if (!this.isSupported()) {
                        if (this.q.addEventListener) {
                            window.addEventListener(
                                this.m[0],
                                this._visible,
                                1
                            );
                            window.addEventListener(this.m[1], this._hidden, 1);
                        } else {
                            if (this.q.attachEvent) {
                                this.q.attachEvent("onfocusin", this._visible);
                                this.q.attachEvent("onfocusout", this._hidden);
                            }
                        }
                    } else {
                        this.q.addEventListener(
                            this.cachedPrefix + this.props[1],
                            function () {
                                window.visibly._nativeSwitch.apply(
                                    window.visibly,
                                    arguments
                                );
                            },
                            1
                        );
                    }
                } catch (i) {}
            },
            init: function () {
                this.getPrefix();
                this._listen();
            },
        };
        this.visibly.init();
    })();

    function random() {
        return Math.round(Math.random() * 2147483647);
    }

    /*
     * Constructors
     */
    class Field {
        constructor(elem, node) {
            this.selector = elem.selector;
            this.node = node;
            this.name = elem.name;
            this.domRect = this.node.getBoundingClientRect();

            // this.top = $elem.offset().top;
            // this.height = $elem.height();
            // this.bottom = this.top + this.height;
            // this.width = $elem.width();
        }
    }

    class Viewport {
        constructor() {
            this.top = window.scrollY;
            this.height = window.innerHeight;
            this.bottom = this.top + this.height;
            this.width = window.innerWidth;
        }
    }

    function sendGAEvent(field, time) {
        console.debug(field,time);
        if (universalGA) {
            ga(
                "send",
                "event",
                "Screentime",
                "Time on Screen",
                field,
                parseInt(time, 10),
                { nonInteraction: true }
            );
        }

        if (classicGA) {
            _gaq.push([
                "_trackEvent",
                "Screentime",
                "Time on Screen",
                field,
                parseInt(time, 10),
                true,
            ]);
        }
    }

    // checks if a field is in the viewport
    function onScreen(viewport, field) {
        let condition, buffered, partialView;

        // Check if node element is visible
        if(field.node.offsetParent === null) {
            return false;
        }

        // Check if node element is in viewport top and bottom
        if(field.domRect.bottom <= viewport.bottom && field.domRect.top >= viewport.top) {
            return true;
        }

        // Check if field is bigger than viewport
        if(field.domRect.height > viewport.height) {
            condition = (viewport.bottom - field.domRect.top) > (viewport.height / 2 ) && (field.domRect.bottom - viewport.top) > (viewport.height / 2);
            if(condition) {
                return true;
            }
        }

        // Partially visible
        buffered = (field.domRect.height * (options.percentOnScreen / 100));
        partialView = ((viewport.bottom - buffered) >= field.domRect.top && (field.domRect.bottom - buffered) > viewport.top);

        return partialView;
            
    }
    
    // checks all fields in cache if they are in the viewport and increases counters
    function checkViewport() {
        let viewport = new Viewport();

        for(let key in cache) {
            let field = cache[key];
            if(onScreen(viewport, field)) {
                log[key] += 1;
                counter[key] += 1;
            }
        }
    }

    function report() {
        let data = {};

        for(let key in counter) {
            let val = counter[key];
            if(val > 0) {
                data[key] = val;
                counter[key] = 0;

                if(options.googleAnalytics) {
                    sendGAEvent(key, val);
                }
            }
        }

        if(Object.keys(data).length > 0 && typeof options.callback === 'function') {
            options.callback(data);
        }
    }

    function startTimers() {
        if(!started) {
            checkViewport();
            started = true;
        }

        looker = setInterval(function() {
            checkViewport();
        }, 1000);

        reporter = setInterval(function() {
            report();
        }, options.reportInterval * 1000);
    }

    function stopTimers() {
        clearInterval(looker);
        clearInterval(reporter);
    }

    function reset() {
        for(let key in cache) {
            log[key] = 0;
            counter[key] = 0;
        }

        startTimers();
    }


    /**
     * Main init function
     */
    function init() {
        // Stop timers when page tab is not active anymore
        visibly.onHidden(() => {
            stopTimers();
        });
        
        // Restart timers when page tab is active again
        visibly.onVisible(() => {
            stopTimers();
            startTimers();
        });
        
        for (let field of options.fields) {
            if (
                typeof field == "object" &&
                "selector" in field &&
                "name" in field
            ) {
                let domElement = document.querySelector(field.selector);
                if (domElement) {
                    field = new Field(field, domElement);
                    cache[field.name] = field;
                    counter[field.name] = 0;
                    log[field.name] = 0;
                }
            }
        }

        startTimers();
    }

    init();
};
