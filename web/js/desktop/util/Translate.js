/**
 * @author: HieuNLD 2014-11-04
 * @param {String} module_code
 * @return {string}
 */
var Locale = {
    module: function(code, obj){
        if (this[code] == undefined) {
            this[code] = {};
        }
        Ext.merge(this[code], obj);
    }
};

/**
 * First element of arguments is always points at module to use
 * @author: HieuNLD 2014-11-04
 * @param {String} module_code
 * @return {string}
 */
String.prototype.Translator = function(module_code){
    var key       = this,
        module    = 'core',
        localized = '__module not found__';
    
    if (module_code != undefined) {
        module = module_code;
    }

    if (Locale[module]) {
        localized = Locale[module][key];
    }
    if (localized == undefined) {
        localized = '__module not found__';
    }
    if (arguments.length > 1) {
        for (var i = 1, limit = arguments.length - 1; i <= limit; i++) {
            localized = localized.replace(new RegExp("{.+}"), arguments[i]);
        }
    }
    
    return localized;
};