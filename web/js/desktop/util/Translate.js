var Locale = {
    module: function(code, obj){
        if (this[code] == undefined) {
            this[code] = {};
        }
        Ext.merge(this[code], obj);
    }
};
/*
 * create object Locale:
 * Locale
 * {
 * core {'Add': 'Add', 'Edit': 'Edit'}
 * }
 */
/**
 * First element of arguments is always points at module to use
 * 
 * @param {String} module_code
 */
String.prototype.Translator = function(module_code){
    var key = this,
        module = 'core';
    
    if (module_code != undefined) {
        module = module_code;
    }

    if (!Locale[module]) {
        localized = '__module not found__';
    } else {
        localized = Locale[module][key];
    }
    if (localized == undefined) {
        $.each(Locale, function(k, v){
            if (localized == undefined) {
                localized = v[key];
            }
        });
        if (localized == undefined) {
            localized = '__translation not found__';//key;
        }
    }

    if (arguments.length > 1) {
        for (var i = 1, limit = arguments.length - 1; i <= limit; i++) {
            /*if (typeof arguments[i] != 'string') {
                continue;
            }*/
            localized = localized.replace(new RegExp("{.+}"), arguments[i]);
        }
    }
    
    return localized;
};